<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroPqr extends ClaseControladorBaseBomberos
{
    protected $tablaPqrs;
    protected $reglasSanitizacion = [
        'form_data' => [
            'id' => 'int',
            'email' => 'email',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaPqrs = $wpdb->prefix .'pqrs';
    }
    public function ejecutarShortCode()
    {
        try {
         $tipo_solicitudValidos=$this->valoresUnicos($this->tablaPqrs,'tipo_solicitud');
            ob_start();
            include plugin_dir_path(__FILE__) . 'formPqr.php';
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
            $plantilla = $peticionLimpia['plantilla'] ?? '';
            $datos=$peticionLimpia['form_data'];
            switch ($plantilla) {
                case 'registrar_pqr':
                    return $this->registrarPqr($datos);
                default:
                    $this->lanzarExcepcion("Plantilla no encontrada en el shortcode prqs " . esc_html($plantilla));
            }
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function registrarPqr($datos)
    {
        try {
            global $wpdb;
            $camposObligatorios = ['nombre', 'telefono', 'email', 'tipo_solicitud', 'contenido'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
                }
            }
          
            $datosInsertar = [
                'nombre'         => $datos['nombre'],
                'telefono'       => $datos['telefono'],
                'email'          => $datos['email'],
                'tipo_solicitud' => $datos['tipo_solicitud'],
                'contenido'      => $datos['contenido'],
                'ip_address'     => $_SERVER['REMOTE_ADDR'],
                'fecha_registro' => current_time('mysql'),
            ];
            $this->logDebug("desdel shorcode pqr",$datos);
            $result = $wpdb->insert($this->tablaPqrs, $datosInsertar);
            $id = $wpdb->insert_id;
            $sqlPqr=$wpdb->prepare("SELECT * FROM {$this->tablaPqrs} WHERE id = %d", $id);
            $objpqr = $wpdb->get_row($sqlPqr, ARRAY_A);
           $para=$datosInsertar['email'];
           $asunto='Hola servicio Bomberos';
           $cuerpo='Esta es una prueba';
           $this->enviar_correo_gmail_smtp($para,$asunto,$cuerpo);
           
           ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('PQR registrada con éxito', $html);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }



public function enviar_correo_gmail_smtp($to, $subject, $body) {


    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $this->logInfo('enviando correo a:'.$to);
    $enviado = wp_mail($to, $subject, $body, $headers);

    if ($enviado) {
        $this->logDebug("Correo enviado correctamente a $to");
    } else {
        $this->logDebug("Error al enviar el correo a $to");
    }
}


}
