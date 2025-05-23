<?php
class ControladorCursos extends ClaseControladorBaseBomberos
{
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

    public function ejecutarFuncionalidad($request)
    {
        // Sanitizar los datos de entrada con las reglas específicas del módulo
        $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);

        // Asumimos que 'funcionalidad' ya está sanitizado como texto
        $funcionalidad = isset($sanitized_request['funcionalidad']) ? $sanitized_request['funcionalidad'] : '';

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
                return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($funcionalidad));
        }
    }

    public function listarCursos($request)
    {
        global $wpdb;
        $tabla_cursos = $wpdb->prefix . 'cursos';

        $items_per_page = 10;
        $current_page = isset($request['paged']) ? max(1, (int) $request['paged']) : 1;
        $offset = ($current_page - 1) * $items_per_page;

        $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_cursos");
        $total_pages = ceil($total_registros / $items_per_page);

        $sql = $wpdb->prepare(
            "SELECT * FROM $tabla_cursos ORDER BY fecha_inicio DESC LIMIT %d OFFSET %d",
            $items_per_page,
            $offset
        );
        $lista_cursos = $wpdb->get_results($sql, ARRAY_A);

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/listadoCursos.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Lista de cursos cargada con éxito', $html);
    }

    public function formularioCreacion($request)
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/formularioCrearCurso.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
    }

    public function insertarCurso($request)
    {
        global $wpdb;
        $form = $request['form_data'] ?? [];
        $tabla = $wpdb->prefix . 'cursos';

        // Validar campos requeridos
        $campos_obligatorios = ['nombre_curso', 'fecha_inicio'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($form[$campo])) {
                return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
            }
        }

        // Validar que fecha_inicio no sea anterior a la fecha actual
        $fecha_inicio = strtotime($form['fecha_inicio']);
        $hoy = strtotime(date('Y-m-d')); // Fecha actual: 2025-05-23
        if ($fecha_inicio < $hoy) {
            return $this->armarRespuesta('La fecha de inicio no puede ser anterior a la fecha actual.', null, false);
        }

        $datos = [
            'nombre_curso' => $form['nombre_curso'],
            'descripcion' => $form['descripcion'] ?? '',
            'fecha_inicio' => $form['fecha_inicio'],
            'duracion_horas' => isset($form['duracion_horas']) ? (int) $form['duracion_horas'] : null,
            'instructor' => $form['instructor'] ?? '',
            'lugar' => $form['lugar'] ?? '',
            'capacidad_maxima' => isset($form['capacidad_maxima']) ? (int) $form['capacidad_maxima'] : null,
            'estado' => 'planificado', // Estado inicial
        ];

        $insertado = $wpdb->insert($tabla, $datos);
        if ($insertado === false) {
            error_log("Bomberos Plugin: Error al insertar curso, Nombre: {$form['nombre_curso']}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al registrar el curso: ' . esc_html($wpdb->last_error), null, false);
        }

        return $this->listarCursos($request);
    }

    public function formularioEdicion($request)
    {
        global $wpdb;
        $tabla_cursos = $wpdb->prefix . 'cursos';
        $id = isset($request['id']) ? (int) $request['id'] : 0;

        if ($id <= 0) {
            return $this->armarRespuesta('ID inválido para edición.', null, false);
        }

        $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_cursos WHERE id_curso = %d", $id), ARRAY_A);
        if (!$curso) {
            return $this->armarRespuesta('Curso no encontrado.', null, false);
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/formularioEditarCurso.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Formulario de edición cargado.', $html);
    }

    public function actualizarCurso($request)
    {
        global $wpdb;
        $form = $request['form_data'] ?? [];
        $tabla = $wpdb->prefix . 'cursos';

        // Validar campos requeridos
        $campos_obligatorios = ['id_curso', 'nombre_curso', 'fecha_inicio'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($form[$campo])) {
                return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
            }
        }

        $id = (int) $form['id_curso'];
        $curso_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla WHERE id_curso = %d", $id), ARRAY_A);
        if (!$curso_existente) {
            return $this->armarRespuesta('Curso no encontrado.', null, false);
        }

        // Validar que fecha_inicio no sea anterior a la fecha actual
        $fecha_inicio = strtotime($form['fecha_inicio']);
        $hoy = strtotime(date('Y-m-d')); // Fecha actual: 2025-05-23
        if ($fecha_inicio < $hoy) {
            return $this->armarRespuesta('La fecha de inicio no puede ser anterior a la fecha actual.', null, false);
        }

        $datos = [
            'nombre_curso' => $form['nombre_curso'],
            'descripcion' => $form['descripcion'] ?? '',
            'fecha_inicio' => $form['fecha_inicio'],
            'duracion_horas' => isset($form['duracion_horas']) ? (int) $form['duracion_horas'] : null,
            'instructor' => $form['instructor'] ?? '',
            'lugar' => $form['lugar'] ?? '',
            'capacidad_maxima' => isset($form['capacidad_maxima']) ? (int) $form['capacidad_maxima'] : null,
            'estado' => $form['estado'] ?? $curso_existente['estado'],
        ];

        $actualizado = $wpdb->update($tabla, $datos, ['id_curso' => $id]);
        if ($actualizado === false) {
            error_log("Bomberos Plugin: Error al actualizar curso ID: {$id}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al actualizar el curso: ' . esc_html($wpdb->last_error), null, false);
        }

        return $this->listarCursos($request);
    }

    public function eliminarCurso($request)
    {
        global $wpdb;
        $tabla = $wpdb->prefix . 'cursos';
        $id = isset($request['id']) ? (int) $request['id'] : 0;

        if ($id <= 0) {
            return $this->armarRespuesta('ID de curso no válido.', null, false);
        }

        $resultado = $wpdb->delete($tabla, ['id_curso' => $id]);
        if ($resultado === false) {
            error_log("Bomberos Plugin: Error al eliminar curso ID: {$id}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al eliminar el curso: ' . esc_html($wpdb->last_error), null, false);
        }

        return $this->listarCursos($request);
    }
}