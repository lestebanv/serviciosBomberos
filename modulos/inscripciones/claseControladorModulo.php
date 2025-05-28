<?php
// modulos/inscripciones/claseControladorModulo.php
if (!defined('ABSPATH')) {
    exit;
}

class ControladorInscripciones extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'nombre_asistente' => 'text', // Aunque no se edite, lo pasamos
            'email_asistente' => 'email', // Aunque no se edite, lo pasamos
            'telefono_asistente' => 'text',
            'estado_inscripcion' => 'text', // Validar contra ENUM 'confirmada', 'pendiente', 'cancelada'
            'notas' => 'textarea',
            'paged' => 'int'
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $form_data = $sanitized_request['form_data'] ?? [];
            $funcionalidad = $sanitized_request['funcionalidad'] ?? ($form_data['funcionalidad'] ?? 'inicial');

            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada en Inscripciones (admin).");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInscripcionesAdmin($form_data, $sanitized_request);
                
                case 'eliminar_inscripcion_admin':
                    $id_inscripcion_eliminar = $form_data['id'] ?? 0;
                    return $this->eliminarInscripcionAdmin($id_inscripcion_eliminar, $form_data, $sanitized_request);

                case 'form_editar_inscripcion_admin': // Corresponde a 'editar_empresa'
                    $id_inscripcion_editar = $form_data['id'] ?? 0; // 'id' viene del data-id del botón
                    return $this->formularioEditarInscripcionAdmin($id_inscripcion_editar, $form_data, $sanitized_request);
                
                case 'actualizar_inscripcion_admin': // Corresponde a 'actualizar_empresa'
                    return $this->actualizarInscripcionAdmin($form_data, $sanitized_request); // $form_data tiene los datos del formulario de edición

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
        // ... (código de listarInscripcionesAdmin sin cambios, como lo tenías) ...
        try {
            global $wpdb;
            $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';
            $tabla_cursos = $wpdb->prefix . 'cursos';

            $items_per_page = 10;
            $current_page = isset($form_data['paged']) ? max(1, (int)$form_data['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_inscripciones");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $tabla_inscripciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de inscripciones.");
            }
            $total_pages = ceil($total_registros / $items_per_page);

            $sql = $wpdb->prepare(
                "SELECT i.*, c.nombre_curso 
                 FROM $tabla_inscripciones i
                 JOIN $tabla_cursos c ON i.id_curso = c.id_curso
                 ORDER BY i.fecha_inscripcion DESC 
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
            include_once BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/listadoInscripcionesAdmin.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inscripciones cargada.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarInscripcionesAdmin: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function eliminarInscripcionAdmin($id_inscripcion, $form_data_original, $request_original_sanitized)
    {
        // ... (código de eliminarInscripcionAdmin sin cambios, como lo tenías) ...
        try {
            global $wpdb;
            if ($id_inscripcion <= 0) {
                $this->lanzarExcepcion('ID de inscripción no válido para eliminar.');
            }
            $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';
            $resultado = $wpdb->delete($tabla_inscripciones, ['id_inscripcion' => $id_inscripcion], ['%d']);

            if ($resultado === false) {
                $this->enviarLog("Error al eliminar inscripción ID: $id_inscripcion", [], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar la inscripción: ' . esc_html($wpdb->last_error));
            }
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

            $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';
            $tabla_cursos = $wpdb->prefix . 'cursos';

            // Obtener datos de la inscripción y el nombre del curso asociado
            $inscripcion = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT i.*, c.nombre_curso 
                     FROM $tabla_inscripciones i
                     JOIN $tabla_cursos c ON i.id_curso = c.id_curso
                     WHERE i.id_inscripcion = %d",
                    $id_inscripcion
                ), ARRAY_A
            );

            if (!$inscripcion) {
                $this->enviarLog("Inscripción no encontrada para editar. ID: $id_inscripcion");
                $this->lanzarExcepcion('Inscripción no encontrada.');
            }

            // $paged viene de $form_data_original, que son los datos enviados por el botón "Editar"
            $paged = $form_data_original['paged'] ?? 1;

            // Opcional: si quisieras permitir cambiar el curso, necesitarías cargar todos los cursos.
            // $todos_los_cursos = $wpdb->get_results("SELECT id_curso, nombre_curso FROM $tabla_cursos ORDER BY nombre_curso ASC", ARRAY_A);

            ob_start();
            // Pasar $inscripcion, $paged (y $todos_los_cursos si fuera necesario) a la vista
            include_once BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/formularioEditarInscripcionAdmin.php';
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
            $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';

            // $form_data_edicion ya está sanitizado por la regla general en ejecutarFuncionalidad
            // y parseado si vino como string.
            $id_inscripcion = $form_data_edicion['id_inscripcion'] ?? 0;

            if ($id_inscripcion <= 0) {
                $this->lanzarExcepcion('ID de inscripción no válido para actualizar.');
            }

            // Validar estado
            $estados_permitidos = ['confirmada', 'pendiente', 'cancelada'];
            if (!in_array($form_data_edicion['estado_inscripcion'], $estados_permitidos)) {
                $this->lanzarExcepcion('Estado de inscripción no válido.');
            }

            $datos_a_actualizar = [
                // 'nombre_asistente' => $form_data_edicion['nombre_asistente'], // No se permite editar
                // 'email_asistente' => $form_data_edicion['email_asistente'], // No se permite editar
                // 'id_curso' => $form_data_edicion['id_curso'], // Si se permitiera cambiar
                'telefono_asistente' => $form_data_edicion['telefono_asistente'] ?? null,
                'estado_inscripcion' => $form_data_edicion['estado_inscripcion'],
                'notas' => $form_data_edicion['notas'] ?? null,
            ];

            // Quitar campos nulos si no se quieren actualizar a NULL explícitamente
            // $datos_a_actualizar = array_filter($datos_a_actualizar, function($value) { return $value !== null; });

            $formatos_datos = [
                // '%s', // nombre_asistente
                // '%s', // email_asistente
                // '%d', // id_curso
                '%s', // telefono_asistente
                '%s', // estado_inscripcion
                '%s', // notas
            ];
            
            $resultado = $wpdb->update(
                $tabla_inscripciones,
                $datos_a_actualizar,
                ['id_inscripcion' => $id_inscripcion], // WHERE
                $formatos_datos, // Formato de los datos a actualizar
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