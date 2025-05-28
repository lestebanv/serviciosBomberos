<?php
class ControladorCursos extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        // Mantenemos 'id' y 'paged' aquí por si alguna vez se envían fuera de form_data,
        // pero priorizaremos su uso desde form_data para consistencia con Empresas.
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_curso' => 'int',
            'paged' => 'int', // Para la paginación a través de formularios o acciones
            'duracion_horas' => 'int',
            'capacidad_maxima' => 'int',
            'fecha_inicio' => 'text', 
            'estado' => 'text',
            // nombre_curso, descripcion, instructor, lugar se sanitizan como texto por defecto
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            // La sanitización es crucial. bomberos_sanitize_input ya maneja form_data como string y lo parsea.
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            
            // Extraemos form_data parseado si existe, o usamos un array vacío
            $form_data = $sanitized_request['form_data'] ?? [];

            $funcionalidad = isset($sanitized_request['funcionalidad']) ? $sanitized_request['funcionalidad'] : '';

            if (empty($funcionalidad)) {
                $this->enviarLog("Funcionalidad no especificada en la solicitud", $sanitized_request);
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }

            // Pasamos $form_data y $sanitized_request a las funciones miembro
            // para que tengan acceso tanto a los datos del formulario como a otros parámetros si es necesario.
            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarCursos($form_data, $sanitized_request);
                case 'form_crear':
                    return $this->formularioCreacion($form_data, $sanitized_request);
                case 'registrar_curso':
                    return $this->insertarCurso($form_data, $sanitized_request);
                case 'editar_curso':
                    return $this->formularioEdicion($form_data, $sanitized_request);
                case 'actualizar_curso':
                    return $this->actualizarCurso($form_data, $sanitized_request);
                case 'eliminar_curso':
                    return $this->eliminarCurso($form_data, $sanitized_request);
                default:
                    $this->enviarLog("Funcionalidad no encontrada", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ejecutarFuncionalidad: " . $e->getMessage(), $request); // Log original request
            throw $e;
        }
    }

    public function listarCursos($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            $tabla_cursos = $wpdb->prefix . 'cursos';

            $items_per_page = 5;
            // paged ahora viene de form_data, como en Empresas
            $current_page = isset($form_data['paged']) ? max(1, (int) $form_data['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_cursos");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $tabla_cursos", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $total_pages = ceil($total_registros / $items_per_page);

            $sql = $wpdb->prepare(
                "SELECT * FROM $tabla_cursos ORDER BY fecha_inicio DESC LIMIT %d OFFSET %d",
                $items_per_page,
                $offset
            );
            $lista_cursos = $wpdb->get_results($sql, ARRAY_A);
            if ($lista_cursos === null) {
                $this->enviarLog("Error al obtener lista de cursos", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de cursos.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/listadoCursos.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de cursos cargada con éxito', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarCursos: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function formularioCreacion($form_data, $request_original_sanitized)
    {
        try {
            // Podríamos necesitar paged si el botón "Cancelar" debe volver a una página específica,
            // pero para "crear nuevo", usualmente no se tiene un 'paged' previo.
            // $paged = isset($form_data['paged']) ? (int) $form_data['paged'] : 1; 
            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/formularioCrearCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioCreacion: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function insertarCurso($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            // $form_data ya es el array de campos del formulario
            $tabla = $wpdb->prefix . 'cursos';

            $campos_obligatorios = ['nombre_curso', 'fecha_inicio'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $fecha_inicio_str = $form_data['fecha_inicio'];
            // Validar formato de fecha YYYY-MM-DD antes de usar strtotime
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_inicio_str)) {
                $this->enviarLog("Formato de fecha de inicio inválido", ['fecha_inicio' => $fecha_inicio_str]);
                $this->lanzarExcepcion('El formato de la fecha de inicio es inválido. Use YYYY-MM-DD.');
            }
            
            $fecha_inicio = strtotime($fecha_inicio_str);
            $hoy = strtotime(date('Y-m-d'));

            if ($fecha_inicio === false) {
                 $this->enviarLog("Fecha de inicio inválida para strtotime", ['fecha_inicio' => $fecha_inicio_str]);
                 $this->lanzarExcepcion('La fecha de inicio proporcionada no es válida.');
            }

            if ($fecha_inicio < $hoy) {
                $this->enviarLog("Fecha de inicio inválida (anterior a hoy)", ['fecha_inicio' => $form_data['fecha_inicio']]);
                $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'nombre_curso' => $form_data['nombre_curso'],
                'descripcion' => $form_data['descripcion'] ?? '',
                'fecha_inicio' => $form_data['fecha_inicio'],
                'duracion_horas' => isset($form_data['duracion_horas']) && $form_data['duracion_horas'] !== '' ? (int) $form_data['duracion_horas'] : null,
                'instructor' => $form_data['instructor'] ?? '',
                'lugar' => $form_data['lugar'] ?? '',
                'capacidad_maxima' => isset($form_data['capacidad_maxima']) && $form_data['capacidad_maxima'] !== '' ? (int) $form_data['capacidad_maxima'] : null,
                'estado' => 'planificado', 
            ];

            $insertado = $wpdb->insert($tabla, $datos);
            if ($insertado === false) {
                $this->enviarLog("Error al insertar curso", $datos, $wpdb->last_error);
                $this->lanzarExcepcion('Error al registrar el curso: ' . esc_html($wpdb->last_error));
            }
            // Después de insertar, listar los cursos. $request_original_sanitized podría tener 'paged' si vino de algún lado,
            // pero es más común volver a la página 1 o la página donde estaba si se está editando.
            // Para insertar, usualmente se vuelve a la página 1 del listado.
            // Preparamos un form_data limpio para listar la página 1.
            return $this->listarCursos(['paged' => 1], $request_original_sanitized);
        } catch (Exception $e) {
            $this->enviarLog("Error en insertarCurso: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function formularioEdicion($form_data, $request_original_sanitized)
    {
        try {
            // id y paged vienen de form_data, como en Empresas
            $id = isset($form_data['id']) ? (int) $form_data['id'] : 0;
            $paged = isset($form_data['paged']) ? (int) $form_data['paged'] : 1; // Página actual para el botón cancelar

            global $wpdb;
            $tabla_cursos = $wpdb->prefix . 'cursos';
            if ($id <= 0) {
                $this->enviarLog("ID inválido para edición", ['id' => $id]);
                $this->lanzarExcepcion('ID inválido para edición.');
            }

            $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_cursos WHERE id_curso = %d", $id), ARRAY_A);
            if (!$curso) {
                $this->enviarLog("Curso no encontrado", ['id' => $id]);
                $this->lanzarExcepcion('Curso no encontrado.');
            }

            ob_start();
            // Pasamos $curso y $paged a la vista
            include plugin_dir_path(__FILE__) . 'vistas/formularioEditarCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEdicion: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function actualizarCurso($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            // $form_data ya contiene los campos del formulario, incluyendo id_curso y paged (del input hidden)
            $tabla = $wpdb->prefix . 'cursos';

            $campos_obligatorios = ['id_curso', 'nombre_curso', 'fecha_inicio'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante al actualizar", ['campo' => $campo, 'data' => $form_data]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id = (int) $form_data['id_curso'];
            $curso_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla WHERE id_curso = %d", $id), ARRAY_A);
            if (!$curso_existente) {
                $this->enviarLog("Curso no encontrado para actualizar", ['id_curso' => $id]);
                $this->lanzarExcepcion('Curso no encontrado.');
            }
            
            $fecha_inicio_str = $form_data['fecha_inicio'];
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_inicio_str)) {
                $this->enviarLog("Formato de fecha de inicio inválido al actualizar", ['fecha_inicio' => $fecha_inicio_str]);
                $this->lanzarExcepcion('El formato de la fecha de inicio es inválido. Use YYYY-MM-DD.');
            }

            $fecha_inicio = strtotime($fecha_inicio_str);
            $hoy = strtotime(date('Y-m-d'));

            if ($fecha_inicio === false) {
                 $this->enviarLog("Fecha de inicio inválida para strtotime al actualizar", ['fecha_inicio' => $fecha_inicio_str]);
                 $this->lanzarExcepcion('La fecha de inicio proporcionada no es válida.');
            }

            if ($fecha_inicio < $hoy) {
                $this->enviarLog("Fecha de inicio inválida (anterior a hoy) al actualizar", ['fecha_inicio' => $form_data['fecha_inicio']]);
                $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'nombre_curso' => $form_data['nombre_curso'],
                'descripcion' => $form_data['descripcion'] ?? '',
                'fecha_inicio' => $form_data['fecha_inicio'],
                'duracion_horas' => isset($form_data['duracion_horas']) && $form_data['duracion_horas'] !== '' ? (int) $form_data['duracion_horas'] : null,
                'instructor' => $form_data['instructor'] ?? '',
                'lugar' => $form_data['lugar'] ?? '',
                'capacidad_maxima' => isset($form_data['capacidad_maxima']) && $form_data['capacidad_maxima'] !== '' ? (int) $form_data['capacidad_maxima'] : null,
                'estado' => $form_data['estado'] ?? $curso_existente['estado'],
            ];

            $actualizado = $wpdb->update($tabla, $datos, ['id_curso' => $id]);
            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar curso", ['id_curso' => $id, 'datos' => $datos], $wpdb->last_error);
                $this->lanzarExcepcion('Error al actualizar el curso: ' . esc_html($wpdb->last_error));
            }

            // Volver a la lista, manteniendo la paginación si 'paged' está en form_data (viene del hidden input)
            // $form_data para listarCursos contendrá el 'paged' del formulario de edición.
            return $this->listarCursos($form_data, $request_original_sanitized);
        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarCurso: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }

    public function eliminarCurso($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            $tabla = $wpdb->prefix . 'cursos';
            // id viene de form_data, como en Empresas
            $id = isset($form_data['id']) ? (int) $form_data['id'] : 0;

            if ($id <= 0) {
                $this->enviarLog("ID de curso no válido para eliminar", ['id' => $id]);
                $this->lanzarExcepcion('ID de curso no válido.');
            }

            $resultado = $wpdb->delete($tabla, ['id_curso' => $id]);
            if ($resultado === false) {
                $this->enviarLog("Error al eliminar curso", ['id' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar el curso: ' . esc_html($wpdb->last_error));
            }
            // Después de eliminar, listar los cursos, manteniendo la paginación actual
            // $form_data para listarCursos contendrá el 'paged' que se envió con la solicitud de eliminación.
            return $this->listarCursos($form_data, $request_original_sanitized);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarCurso: " . $e->getMessage(), $form_data);
            throw $e;
        }
    }
}
?>