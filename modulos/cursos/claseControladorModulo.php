<?php
if (!defined('ABSPATH'))   exit;

class ControladorCursos extends ClaseControladorBaseBomberos
{
    protected $tablaCursos;
    protected $tablaInscripciones;

    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_curso' => 'int',
            'duracion_horas' => 'int',
            'capacidad_maxima' => 'int',
            'fecha_inicio' => 'text', // Para fechas, usamos text y validamos manualmente
            'estado' => 'text', // Para el ENUM
            // Otros campos como nombre_curso, descripcion, instructor, lugar se sanitizan como texto por defecto
        ],
    ];

    public function __construct() {
        global $wpdb;
        $this->tablaCursos = $wpdb->prefix . 'cursos';
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
    }

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $funcionalidad = isset($sanitized_request['funcionalidad']) ? $sanitized_request['funcionalidad'] : '';

            if (empty($funcionalidad)) {
                $this->enviarLog("Funcionalidad no especificada en la solicitud", $request);
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarCursos($sanitized_request);
                case 'form_crear':
                    return $this->formularioCreacion($sanitized_request);
                case 'registrar_curso':
                    return $this->insertarCurso($sanitized_request);
                case 'editar_curso':
                    return $this->formularioEdicion($sanitized_request);
                case 'actualizar_curso':
                    return $this->actualizarCurso($sanitized_request);
                case 'eliminar_curso':
                    return $this->eliminarCurso($sanitized_request);
                default:
                    $this->enviarLog("Funcionalidad no encontrada", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function listarCursos($request)
    {
        try {
            global $wpdb;
            
            $items_per_page = 5;
            $current_page = isset($request['form_data']['paged']) ? max(1, (int) $request['form_data']['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $this->tablaCursos");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $this->tablaCursos", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $total_pages = ceil($total_registros / $items_per_page);

            // Criterio de ordenamiento actual: fecha_inicio DESC
            $sql = $wpdb->prepare(
                "SELECT * FROM $this->tablaCursos ORDER BY fecha_inicio DESC LIMIT %d OFFSET %d",
                $items_per_page,
                $offset
            );
            $lista_cursos = $wpdb->get_results($sql, ARRAY_A);
            if ($lista_cursos === null) {
                $this->enviarLog("Error al obtener lista de cursos", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de cursos.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoCursos.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de cursos cargada con éxito', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarCursos: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioCreacion($request)
    {
        try {
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioCreacion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function insertarCurso($request)
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            
            $campos_obligatorios = ['nombre_curso', 'fecha_inicio'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            // Se elimina la validación de fecha_inicio > hoy para el registro.
            // $fecha_inicio = strtotime($form['fecha_inicio']);
            // $hoy = strtotime(date('Y-m-d')); 
            // if ($fecha_inicio < $hoy) {
            //     $this->enviarLog("Fecha de inicio inválida", ['fecha_inicio' => $form['fecha_inicio']]);
            //     $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            // }

            $datos = [
                'nombre_curso' => $form['nombre_curso'],
                'descripcion' => $form['descripcion'] ?? '',
                'fecha_inicio' => $form['fecha_inicio'],
                'duracion_horas' => isset($form['duracion_horas']) && $form['duracion_horas'] !== '' ? (int) $form['duracion_horas'] : null,
                'instructor' => $form['instructor'] ?? '',
                'lugar' => $form['lugar'] ?? '',
                'capacidad_maxima' => isset($form['capacidad_maxima']) && $form['capacidad_maxima'] !== '' ? (int) $form['capacidad_maxima'] : null,
                'estado' => 'planificado', // Estado inicial
            ];

            $insertado = $wpdb->insert($this->tablaCursos, $datos);
            if ($insertado === false) {
                $this->enviarLog("Error al insertar curso", $form, $wpdb->last_error);
                $this->lanzarExcepcion('Error al registrar el curso: ' . esc_html($wpdb->last_error));
            }
            
            $paged = isset($form['paged']) ? (int) $form['paged'] : 1;
            return $this->listarCursos(['form_data' => ['paged' => $paged]]);
        } catch (Exception $e) {
            $this->enviarLog("Error en insertarCurso: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioEdicion($request)
    {
        try {
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;
            global $wpdb;
            
            if ($id <= 0) {
                $this->enviarLog("ID inválido para edición", ['id' => $id]);
                $this->lanzarExcepcion('ID inválido para edición.');
            }

            $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->tablaCursos WHERE id_curso = %d", $id), ARRAY_A);
            if (!$curso) {
                $this->enviarLog("Curso no encontrado", ['id' => $id]);
                $this->lanzarExcepcion('Curso no encontrado.');
            }

            // Obtener inscripciones del curso
            $inscripciones_curso = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT nombre_asistente, email_asistente, fecha_inscripcion, estado_inscripcion 
                     FROM $this->tablaInscripciones 
                     WHERE id_curso = %d AND (estado_inscripcion = 'Registrada' OR estado_inscripcion = 'Aprobada')
                     ORDER BY nombre_asistente ASC", 
                    $id
                ), ARRAY_A
            );
            if ($inscripciones_curso === null) {
                $inscripciones_curso = [];
                $this->enviarLog("No se encontraron inscripciones o hubo un error al buscarlas para el curso ID: $id", [], $wpdb->last_error);
            }


            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEdicion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function actualizarCurso($request)
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            
            $campos_obligatorios = ['id_curso', 'nombre_curso', 'fecha_inicio'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id = (int) $form['id_curso'];
            $curso_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->tablaCursos WHERE id_curso = %d", $id), ARRAY_A);
            if (!$curso_existente) {
                $this->enviarLog("Curso no encontrado", ['id_curso' => $id]);
                $this->lanzarExcepcion('Curso no encontrado.');
            }

            // Mantenemos la validación de fecha para la actualización si se desea, o se puede quitar también.
            // Por ahora, la dejo, ya que editar un curso a una fecha pasada podría ser problemático.
            $fecha_inicio_obj = new DateTime($form['fecha_inicio']);
            $hoy_obj = new DateTime();
            $hoy_obj->setTime(0,0,0); // Para comparar solo fechas

            if ($fecha_inicio_obj < $hoy_obj) {
                $this->enviarLog("Fecha de inicio inválida al actualizar", ['fecha_inicio' => $form['fecha_inicio']]);
                $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'nombre_curso' => $form['nombre_curso'],
                'descripcion' => $form['descripcion'] ?? '',
                'fecha_inicio' => $form['fecha_inicio'],
                'duracion_horas' => isset($form['duracion_horas']) && $form['duracion_horas'] !== '' ? (int) $form['duracion_horas'] : null,
                'instructor' => $form['instructor'] ?? '',
                'lugar' => $form['lugar'] ?? '',
                'capacidad_maxima' => isset($form['capacidad_maxima']) && $form['capacidad_maxima'] !== '' ? (int) $form['capacidad_maxima'] : null,
                'estado' => $form['estado'] ?? $curso_existente['estado'],
            ];

            $actualizado = $wpdb->update($this->tablaCursos, $datos, ['id_curso' => $id]);
            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar curso", ['id_curso' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al actualizar el curso: ' . esc_html($wpdb->last_error));
            }
            
            $paged = isset($form['paged']) ? (int) $form['paged'] : 1;
            return $this->listarCursos(['form_data' => ['paged' => $paged]]);
        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarCurso: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarCurso($request)
    {
        try {
            global $wpdb;
           
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;

            if ($id <= 0) {
                $this->enviarLog("ID de curso no válido", ['id' => $id]);
                $this->lanzarExcepcion('ID de curso no válido.');
            }

            $resultado = $wpdb->delete($this->tablaCursos, ['id_curso' => $id]);
            if ($resultado === false) {
                $this->enviarLog("Error al eliminar curso", ['id' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar el curso: ' . esc_html($wpdb->last_error));
            }
            
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;
            return $this->listarCursos(['form_data' => ['paged' => $paged]]);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarCurso: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}