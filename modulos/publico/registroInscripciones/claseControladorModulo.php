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
                'estado_inscripcion'       =>"Registrada",
                'fecha_inscripcion' => current_time('mysql')
            ];
            $result = $wpdb->insert($this->tablaInscripciones, $dataInscripcion);
            $id = $wpdb->insert_id;
            $strsql= $wpdb->prepare("
                       SELECT  i.*,  c.nombre_curso FROM  {$this->tablaInscripciones} AS i INNER JOIN {$this->tablaCursos} AS c 
                       ON  i.id_curso = c.id_curso  WHERE  i.id_inscripcion = $id;");
            
            $objincripcion = $wpdb->get_row($strsql, ARRAY_A);
            ob_start();
            include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('PQR registrada con éxito', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}
