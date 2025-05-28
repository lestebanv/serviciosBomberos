<?php
// modulos/inscripciones/claseControladorInscripcionesPublico.php
if (!defined('ABSPATH')) {
    exit;
}

// La ClaseControladorBaseBomberos ya debería estar incluida por 'utilidades.php' en el plugin principal.
// Si no, asegúrate de que `require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';` se ejecute.

class ControladorInscripcionesPublico extends ClaseControladorBaseBomberos
{
    // Reglas de sanitización para los datos del formulario de inscripción
    protected $sanitization_rules_inscripcion = [
        'id_curso' => 'int',
        'nombre_asistente' => 'text',
        'email_asistente' => 'email',
        'telefono_asistente' => 'text',
        // El nonce y 'funcionalidad_publica' se manejan por separado
    ];

    public function ejecutarFuncionalidadPublica($request_data)
    {
        // $request_data es el $_POST completo de la llamada AJAX.
        // La funcionalidad viene en $request_data['form_data'] (serializado) o directamente.
        // Para ser consistentes con el JS que envía 'form_data' serializado:
        
        $form_data_str = $request_data['form_data'] ?? '';
        parse_str($form_data_str, $parsed_form_data); // Parsear el string serializado
        
        // Ahora $parsed_form_data contiene los campos del formulario
        // y también 'funcionalidad_publica' si se incluyó en el serialize.

        $funcionalidad_publica = isset($parsed_form_data['funcionalidad_publica']) 
                               ? sanitize_text_field($parsed_form_data['funcionalidad_publica']) 
                               : 'mostrar_formulario_inscripcion'; // Default para el shortcode

        try {
            switch ($funcionalidad_publica) {
                case 'mostrar_formulario_inscripcion': // Este es el caso para cuando el shortcode se renderiza inicialmente
                    return $this->mostrarFormularioInscripcion();
                case 'procesar_inscripcion_curso':
                    // Pasamos $parsed_form_data que ya contiene los campos del formulario
                    return $this->procesarInscripcionCurso($parsed_form_data); 
                default:
                    return $this->armarRespuestaPublica('Funcionalidad pública no reconocida.', '', false);
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en InscripcionesPublico: " . $e->getMessage(), $request_data, $e);
            return $this->armarRespuestaPublica('Error: ' . esc_html($e->getMessage()), '', false);
        }
    }

    private function getCursosDisponibles()
    {
        global $wpdb;
        $tabla_cursos = $wpdb->prefix . 'cursos';
        $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';

        $sql = $wpdb->prepare(
            "SELECT c.*, (c.capacidad_maxima - COUNT(i.id_inscripcion)) AS cupos_disponibles
            FROM $tabla_cursos c
            LEFT JOIN $tabla_inscripciones i ON c.id_curso = i.id_curso AND i.estado_inscripcion = 'confirmada'
            WHERE (c.estado = 'planificado' OR c.estado = 'en_curso')
            AND c.fecha_inicio >= CURDATE() 
            GROUP BY c.id_curso
            HAVING cupos_disponibles > 0 OR c.capacidad_maxima IS NULL OR c.capacidad_maxima = 0
            ORDER BY c.fecha_inicio ASC, c.nombre_curso ASC"
        );
        
        $cursos = $wpdb->get_results($sql, ARRAY_A);
        return $cursos;
    }

    public function mostrarFormularioInscripcion()
    {
        $cursos_disponibles = $this->getCursosDisponibles();
        $html = '';
        ob_start();
        if (empty($cursos_disponibles)) {
            $html = "<p>" . esc_html__('Actualmente no hay cursos con inscripciones abiertas.', 'bomberos-servicios') . "</p>";
        } else {
            // Pasar $cursos_disponibles a la vista
            include_once BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/formularioInscripcionCurso.php';
        }
        $html_content = ob_get_clean();
        if (!empty($html_content)) $html = $html_content;

        // Para el renderizado inicial del shortcode, solo devolvemos el HTML.
        // El formato ['html' => ...] es esperado por la función del shortcode.
        return ['html' => $html];
    }

    public function procesarInscripcionCurso($form_fields) // Recibe los campos ya parseados
    {
        global $wpdb;

        // Sanitizar los campos específicos del formulario usando las reglas definidas
        $sanitized_fields = bomberos_sanitize_input($form_fields, $this->sanitization_rules_inscripcion);

        $id_curso = $sanitized_fields['id_curso'] ?? 0;
        $nombre_asistente = $sanitized_fields['nombre_asistente'] ?? '';
        $email_asistente = $sanitized_fields['email_asistente'] ?? '';
        $telefono_asistente = $sanitized_fields['telefono_asistente'] ?? '';

        // Validaciones
        if (empty($id_curso) || empty($nombre_asistente) || empty($email_asistente)) {
            $this->lanzarExcepcion(__('Por favor, complete todos los campos obligatorios.', 'bomberos-servicios'));
        }
        if (!is_email($email_asistente)) {
            $this->lanzarExcepcion(__('Por favor, ingrese un correo electrónico válido.', 'bomberos-servicios'));
        }

        $tabla_cursos = $wpdb->prefix . 'cursos';
        $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';

        $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_cursos WHERE id_curso = %d AND (estado = 'planificado' OR estado = 'en_curso') AND fecha_inicio >= CURDATE()", $id_curso), ARRAY_A);
        if (!$curso) {
            $this->lanzarExcepcion(__('El curso seleccionado no está disponible para inscripción.', 'bomberos-servicios'));
        }

        if ($curso['capacidad_maxima'] > 0) {
            $inscritos_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla_inscripciones WHERE id_curso = %d AND estado_inscripcion = 'confirmada'", $id_curso));
            if ($inscritos_count >= $curso['capacidad_maxima']) {
                $this->lanzarExcepcion(__('Este curso ha alcanzado su capacidad máxima.', 'bomberos-servicios'));
            }
        }

        $ya_inscrito = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla_inscripciones WHERE id_curso = %d AND email_asistente = %s AND estado_inscripcion = 'confirmada'", $id_curso, $email_asistente));
        if ($ya_inscrito > 0) {
            $this->lanzarExcepcion(__('Ya te encuentras inscrito en este curso.', 'bomberos-servicios'));
        }
        
        // Validación de cruce de horarios (simplificada)
        $inscripciones_previas = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT c.id_curso, c.nombre_curso, c.fecha_inicio, c.duracion_horas
                 FROM $tabla_inscripciones i JOIN $tabla_cursos c ON i.id_curso = c.id_curso
                 WHERE i.email_asistente = %s AND i.estado_inscripcion = 'confirmada'", $email_asistente
            ), ARRAY_A
        );

        $fecha_inicio_nuevo = new DateTime($curso['fecha_inicio']);
        $duracion_nuevo = max(1, ceil(($curso['duracion_horas'] ?? 8) / 24));
        $fecha_fin_nuevo = (clone $fecha_inicio_nuevo)->add(new DateInterval("P".($duracion_nuevo - 1)."D"));

        foreach ($inscripciones_previas as $insc_existente) {
            if ($insc_existente['id_curso'] == $id_curso) continue;
            $fecha_inicio_existente = new DateTime($insc_existente['fecha_inicio']);
            $duracion_existente = max(1, ceil(($insc_existente['duracion_horas'] ?? 8) / 24));
            $fecha_fin_existente = (clone $fecha_inicio_existente)->add(new DateInterval("P".($duracion_existente - 1)."D"));

            if (($fecha_inicio_nuevo <= $fecha_fin_existente) && ($fecha_fin_nuevo >= $fecha_inicio_existente)) {
                $this->lanzarExcepcion(sprintf(
                    __('Tienes un cruce de horario con: "%s" (Inicia: %s).', 'bomberos-servicios'),
                    esc_html($insc_existente['nombre_curso']),
                    esc_html(date_i18n(get_option('date_format'), $fecha_inicio_existente->getTimestamp()))
                ));
            }
        }

        $insertado = $wpdb->insert($tabla_inscripciones, [
            'id_curso' => $id_curso, 'nombre_asistente' => $nombre_asistente,
            'email_asistente' => $email_asistente, 'telefono_asistente' => $telefono_asistente,
            'estado_inscripcion' => 'confirmada'
        ], ['%d', '%s', '%s', '%s', '%s']);

        if ($insertado === false) {
            $this->lanzarExcepcion(__('Error al procesar tu inscripción. Inténtalo de nuevo.', 'bomberos-servicios'));
        }
        
        $mensaje_exito = __('¡Inscripción realizada con éxito! Te has inscrito correctamente en el curso: ', 'bomberos-servicios') . esc_html($curso['nombre_curso']);
        ob_start();
        // Pasamos el mensaje a la vista de respuesta
        $mensaje_vista = $mensaje_exito; 
        include_once BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/mensajeRespuestaInscripcion.php';
        $html_respuesta = ob_get_clean();

        return $this->armarRespuestaPublica($mensaje_exito, $html_respuesta, true);
    }
    
    // Método para armar la respuesta AJAX pública consistente
    public function armarRespuestaPublica($mensaje_texto, $html_contenido = "", $es_exito = false) {
        if ($es_exito) {
            return ['success' => true, 'data' => ['mensaje' => $mensaje_texto, 'html' => $html_contenido]];
        } else {
            // Para errores, el JS espera 'mensaje' dentro de 'data' si success es false
            return ['success' => false, 'data' => ['mensaje' => $mensaje_texto, 'html' => $html_contenido]];
        }
    }
}
?>