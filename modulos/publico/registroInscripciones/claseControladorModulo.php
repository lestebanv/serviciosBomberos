<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroInscripciones extends ClaseControladorBaseBomberos
{
    private $tablaInscripciones;
    private $tablaCursos;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'notas' => 'textarea',
            'actualpagina' => 'int'
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
        $this->tablaCursos = $wpdb->prefix . 'cursos';
    }


    public function ejecutarShortCode()
    {
        try {

            global $wpdb;
   

           $sql = $wpdb->prepare(
         "SELECT * from {$this->tablaCursos}"
);


        
        $cursosDisponibles = $wpdb->get_results($sql, ARRAY_A);
            
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioInscripcionCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e);
        }
    }

    public function ejecutarFuncionalidad($peticion)
    {
        
        try {
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $plantilla = $peticionLimpia['plantilla'] ?? ($peticionLimpia['plantilla'] ?? 'inicial');
            $datos = $peticionLimpia['form_data'] ?? [];
            switch ($plantilla) {
                case 'registrar_inscripcion':
                    return $this->registrarInscripcion($datos);
                default:
                    return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($plantilla));
            }
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

        public function registrarInscripcion($datos)
    {
        try {
            global $wpdb;
            $camposObligatorios = ['nombre_asistente', 'telefono_asistente', 'email_asistente','id_curso'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.");
                }
            }

            $dataInscripcion = [
                'id_curso'                 => $datos['id_curso'],
                'nombre_asistente'         => $datos['nombre_asistente'],
                'telefono_asistente'       => $datos['telefono_asistente'],
                'email_asistente'          => $datos['email_asistente'],
                'estado_inscripcion'       => "Registrada",
                'fecha_inscripcion'        => current_time('mysql')
            ];
            $result = $wpdb->insert($this->tablaInscripciones, $dataInscripcion);
            
            if ($result === false) {
                $this->lanzarExcepcion("Hubo un error al registrar su inscripción en la base de datos.");
            }

            $id = $wpdb->insert_id;
            $strsql = $wpdb->prepare("
                       SELECT i.*, c.nombre_curso, c.fecha_inicio, c.lugar, c.instructor 
                       FROM {$this->tablaInscripciones} AS i 
                       INNER JOIN {$this->tablaCursos} AS c ON i.id_curso = c.id_curso
                       WHERE i.id_inscripcion = %d;", $id);
            
            $objincripcion = $wpdb->get_row($strsql, ARRAY_A);

           
            if ($objincripcion) {
                // 1. Preparamos los datos del correo
                $para = $objincripcion['email_asistente'];
                $asunto = 'Confirmación de Inscripción al Curso: ' . $objincripcion['nombre_curso'];
                
                // 2. Construimos el cuerpo del correo en HTML
                $cuerpo = '<h1>¡Inscripción Confirmada!</h1>';
                $cuerpo .= '<p>Hola <strong>' . esc_html($objincripcion['nombre_asistente']) . '</strong>,</p>';
                $cuerpo .= '<p>Tu inscripción al siguiente curso ha sido registrada con éxito:</p>';
                $cuerpo .= '<h2 style="color:#8a2be2;">' . esc_html($objincripcion['nombre_curso']) . '</h2>';
                $cuerpo .= '<ul>';
                $cuerpo .= '<li><strong>Fecha de Inicio:</strong> ' . esc_html(date_i18n('l, j \d\e F \d\e Y', strtotime($objincripcion['fecha_inicio']))) . '</li>';
                $cuerpo .= '<li><strong>Lugar:</strong> ' . esc_html($objincripcion['lugar']) . '</li>';
                $cuerpo .= '<li><strong>Instructor:</strong> ' . esc_html($objincripcion['instructor']) . '</li>';
                $cuerpo .= '<li><strong>Estado de tu Inscripción:</strong> ' . esc_html($objincripcion['estado_inscripcion']) . '</li>';
                $cuerpo .= '</ul>';
                $cuerpo .= '<p>Guardaremos esta información y te contactaremos si hay alguna novedad. ¡Te esperamos!</p>';
                $cuerpo .= '<hr><p>Cuerpo de Bomberos Voluntarios de Pamplona</p>';

                // 3. Enviamos el correo usando el método de la clase base
                try {
                    $this->enviarCorreoPorGmail($para, $asunto, $cuerpo);
                } catch (Exception $e) {
                    $this->logError("Fallo al enviar correo de confirmación de inscripción a {$para}: " . $e->getMessage());
                }
            }
          

            ob_start();
            include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Inscripción registrada con éxito', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}
