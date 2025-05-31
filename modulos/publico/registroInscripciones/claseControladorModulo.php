<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroInscripciones extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'form_data' => [
            'id' => 'int',
            'email' => 'email',
        ],
    ];

    public function ejecutarShortCode()
    {
        try {

            global $wpdb;
            $tabla_cursos = $wpdb->prefix . 'cursos';
            $tabla_inscripciones = $wpdb->prefix . 'inscripciones';

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
        
        $cursos_disponibles = $wpdb->get_results($sql, ARRAY_A);

            
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioInscripcionCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar el shortcode: ' . $e->getMessage(), null, false);
        }
    }

    public function ejecutarFuncionalidad($request)
    {
        
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $plantilla = $request['plantilla'] ?? '';

            if (empty($plantilla)) {
                $this->enviarLog("Plantilla no especificada en la solicitud", $request);
                $this->lanzarExcepcion("Plantilla no especificada.");
            }

            switch ($plantilla) {
                case 'registrar_inscripcion':
                    return $this->registrarInscripcion($sanitized_request);
                default:
                    return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($plantilla));
            }
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar la funcionalidad: ' . $e->getMessage(), null, false);
        }
    }

    public function registrarInscripcion($request)
    {
        try {
            global $wpdb;
            $tabla_inscripciones = $wpdb->prefix . 'inscripciones';
            $tabla_cursos= $wpdb->prefix . 'cursos';
            $form_data = $request['form_data'] ?? [];
            $campos_obligatorios = ['nombre_asistente', 'telefono_asistente', 'email_asistente','id_curso'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.");
                }
            }

            $data = [
                'id_curso'                 => $form_data['id_curso'],
                'nombre_asistente'         => $form_data['nombre_asistente'],
                'telefono_asistente'       => $form_data['telefono_asistente'],
                'email_asistente'          => $form_data['email_asistente'],
                'estado_inscripcion'       =>"Registrada",
                'fecha_inscripcion' => current_time('mysql')
            ];
            $result = $wpdb->insert($tabla_inscripciones, $data);
            if ($result === false) {
                return $this->armarRespuesta('Error al registrar la PQR: ' . esc_html($wpdb->last_error), null, false);
            }

            $id = $wpdb->insert_id;
            $strsql= $wpdb->prepare("
                       SELECT  i.*,  c.nombre_curso FROM  $tabla_inscripciones AS i INNER JOIN $tabla_cursos AS c 
                       ON  i.id_curso = c.id_curso  WHERE  i.id_inscripcion = $id;");
            
            $objincripcion = $wpdb->get_row($strsql, ARRAY_A);

            ob_start();
            include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('PQR registrada con éxito', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error inesperado al registrar PQR: ' . $e->getMessage(), null, false);
        }
    }
}
