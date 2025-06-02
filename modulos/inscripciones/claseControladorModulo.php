<?php
if (!defined('ABSPATH')) {
    exit;
}

class ControladorInscripciones extends ClaseControladorBaseBomberos
{
    protected $tablaInscripciones;
    protected $tablaCursos;

    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'nombre_asistente' => 'text', 
            'email_asistente' => 'email', 
            'telefono_asistente' => 'text',
            'estado_inscripcion' => 'text',
            'notas' => 'textarea',
            'paged' => 'int'
        ],
    ];

    public function __construct() {
        global $wpdb;
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
        $this->tablaCursos = $wpdb->prefix . 'cursos';
    }

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $form_data = $sanitized_request['form_data'] ?? []; // Asegurarse de que form_data exista
            $funcionalidad = $sanitized_request['funcionalidad'] ?? ($form_data['funcionalidad'] ?? 'inicial');


            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada en Inscripciones (admin).");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInscripcionesAdmin($form_data, $sanitized_request); // $form_data puede tener 'paged'
                
                case 'eliminar_inscripcion_admin':
                    $id_inscripcion_eliminar = $form_data['id'] ?? 0;
                    return $this->eliminarInscripcionAdmin($id_inscripcion_eliminar, $form_data, $sanitized_request);

                case 'form_editar_inscripcion_admin': 
                    $id_inscripcion_editar = $form_data['id'] ?? 0; 
                    return $this->formularioEditarInscripcionAdmin($id_inscripcion_editar, $form_data, $sanitized_request);
                
                case 'actualizar_inscripcion_admin': 
                    return $this->actualizarInscripcionAdmin($form_data, $sanitized_request);

                default:
                    $this->enviarLog("Funcionalidad admin no encontrada en Inscripciones", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ControladorInscripciones::ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function listarInscripcionesAdmin($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            
            $items_per_page = 10;
            $current_page = isset($form_data['paged']) ? max(1, (int)$form_data['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $this->tablaInscripciones");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $this->tablaInscripciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de inscripciones.");
            }
            $total_pages = ceil($total_registros / $items_per_page);

            // Ordenar por nombre de curso ASC, luego por estado de inscripción ASC
            $sql = $wpdb->prepare(
                "SELECT i.*, c.nombre_curso 
                 FROM $this->tablaInscripciones i
                 JOIN $this->tablaCursos c ON i.id_curso = c.id_curso
                 ORDER BY c.nombre_curso ASC, FIELD(i.estado_inscripcion, 'Registrada', 'Aprobada', 'Pendiente', 'Cerrada') ASC, i.fecha_inscripcion DESC
                 LIMIT %d OFFSET %d",
                $items_per_page,
                $offset
            );
            $lista_inscripciones = $wpdb->get_results($sql, ARRAY_A);

            if ($lista_inscripciones === null && $total_registros > 0) {
                $this->enviarLog("Error al obtener lista de inscripciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de inscripciones.");
            }

            ob_start();
            // Asegúrate que la ruta es correcta. Si este archivo está en modulos/inscripciones/, plugin_dir_path(__FILE__) es correcto.
            include_once plugin_dir_path(__FILE__) . 'listadoInscripcionesAdmin.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inscripciones cargada.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarInscripcionesAdmin: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function eliminarInscripcionAdmin($id_inscripcion, $form_data_original, $request_original_sanitized)
    {
        try {
            global $wpdb;
            if ($id_inscripcion <= 0) {
                $this->lanzarExcepcion('ID de inscripción no válido para eliminar.');
            }
            $resultado = $wpdb->delete($this->tablaInscripciones, ['id_inscripcion' => $id_inscripcion], ['%d']);

            if ($resultado === false) {
                $this->enviarLog("Error al eliminar inscripción ID: $id_inscripcion", [], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar la inscripción: ' . esc_html($wpdb->last_error));
            }
            
            // $form_data_original ya tiene 'paged' del botón de eliminar
            return $this->listarInscripcionesAdmin($form_data_original, $request_original_sanitized);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarInscripcionAdmin: " . $e->getMessage(), ['id_inscripcion' => $id_inscripcion]);
            throw $e;
        }
    }

    public function formularioEditarInscripcionAdmin($id_inscripcion, $form_data_original, $request_original_sanitized)
    {
        try {
            global $wpdb;
            if ($id_inscripcion <= 0) {
                $this->lanzarExcepcion('ID de inscripción no válido para editar.');
            }

            // Obtener datos de la inscripción y el nombre del curso asociado
            $inscripcion = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT i.*, c.nombre_curso 
                     FROM $this->tablaInscripciones i
                     JOIN $this->tablaCursos c ON i.id_curso = c.id_curso
                     WHERE i.id_inscripcion = %d",
                    $id_inscripcion
                ), ARRAY_A
            );

            if (!$inscripcion) {
                $this->enviarLog("Inscripción no encontrada para editar. ID: $id_inscripcion");
                $this->lanzarExcepcion('Inscripción no encontrada.');
            }

            $paged = $form_data_original['paged'] ?? 1;
            $estados_posibles = ['Registrada', 'Aprobada', 'Pendiente', 'Cerrada']; // Asegúrate que estos son los estados correctos
            
            ob_start();
            include_once plugin_dir_path(__FILE__) . 'formularioEditarInscripcionAdmin.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición de inscripción cargado.', $html);

        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEditarInscripcionAdmin: " . $e->getMessage(), ['id_inscripcion' => $id_inscripcion]);
            throw $e;
        }
    }

    public function actualizarInscripcionAdmin($form_data_edicion, $request_original_sanitized)
    {
        try {
            global $wpdb;
            
            $id_inscripcion = $form_data_edicion['id_inscripcion'] ?? 0;

            if ($id_inscripcion <= 0) {
                $this->lanzarExcepcion('ID de inscripción no válido para actualizar.');
            }

            // Campos que se pueden actualizar desde el admin
            $datos_a_actualizar = [
                'telefono_asistente' => $form_data_edicion['telefono_asistente'] ?? null,
                'estado_inscripcion' => $form_data_edicion['estado_inscripcion'], // Este campo es obligatorio en el form
                'notas' => $form_data_edicion['notas'] ?? null,
            ];
            
            // Validar estado_inscripcion
            $estados_validos = ['Registrada', 'Aprobada', 'Pendiente', 'Cerrada'];
            if (!in_array($datos_a_actualizar['estado_inscripcion'], $estados_validos)) {
                $this->lanzarExcepcion('Estado de inscripción no válido.');
            }

            $formatos_datos = [
                '%s', // telefono_asistente
                '%s', // estado_inscripcion
                '%s', // notas
            ];
            
            $resultado = $wpdb->update(
                $this->tablaInscripciones,
                $datos_a_actualizar,
                ['id_inscripcion' => $id_inscripcion], // WHERE
                $formatos_datos, 
                ['%d'] // Formato del WHERE
            );

            if ($resultado === false) {
                $this->enviarLog("Error al actualizar inscripción ID: $id_inscripcion", $datos_a_actualizar, $wpdb->last_error);
                $this->lanzarExcepcion('Error al actualizar la inscripción: ' . esc_html($wpdb->last_error));
            }

            // $form_data_edicion ya tiene 'paged' del formulario
            return $this->listarInscripcionesAdmin($form_data_edicion, $request_original_sanitized);

        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarInscripcionAdmin: " . $e->getMessage(), $form_data_edicion);
            throw $e;
        }
    }
}
?>