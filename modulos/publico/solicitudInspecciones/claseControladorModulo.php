<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';
class ControladorBomberosShortCodeSolicitudInspecciones extends ClaseControladorBaseBomberos
{
    protected $tablaEmpresas;
    protected $tablaInspecciones;
    protected $reglasSanitizacion = [
        'form_data' => [
                'id_empresa' => 'int',
                'id_inspeccion' => 'int',
                'email' => 'email',
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaEmpresas = $wpdb->prefix . 'empresas';
        $this->tablaInspecciones= $wpdb->prefix . 'inspecciones';
    }
    public function ejecutarShortCode() {
        try {
            ob_start();
            include plugin_dir_path(__FILE__) . 'frmBuscarEmpresa.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
      } catch (Exception $e) {
            $this->manejarExcepcion($e);
        }
    }

    public function ejecutarFuncionalidad($peticion)
    {
     try{
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $plantilla = $peticionLimpia['plantilla'] ?? '';
            $datos=$peticionLimpia['form_data'];
            switch ($plantilla) {
                case 'buscar_empresa':
                    return $this->enviarFrmEmpresa($datos);
                case 'registrar_empresa_solicitud':
                    return $this->registrarSolicitudInspeccion($datos);
                default:
                    return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($plantilla));
            }
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function enviarFrmEmpresa($datos)
    {
        try {
            global $wpdb;
            $nit = $datos['nit'] ?? '';
            $strsql = $wpdb->prepare("SELECT * FROM {$this->tablaEmpresas} WHERE nit = %s", $nit);
            $empresa = $wpdb->get_row($strsql, ARRAY_A);
            if ($empresa) {
                ob_start();
                include plugin_dir_path(__FILE__) . 'frmRegistrarSoloSolicitud.php';
                $html = ob_get_clean();
                return $this->armarRespuesta('Formulario completo enviado', $html);
            } else {
                ob_start();
                include plugin_dir_path(__FILE__) . 'frmRegistrarEmpresaSolicitud.php';
                $html = ob_get_clean();
                return $this->armarRespuesta('Formulario completo enviado', $html);
            }
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function registrarSolicitudInspeccion($datos)
    {
        try {
            if (isset($datos['id_empresa'])) {
                return $this->actualizarEmpresaInsertarSolicitud($datos);
            } else {
                return $this->insertarEmpresaInsertarSolicitud($datos);
            }
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function insertarEmpresaInsertarSolicitud($datos)
    {
        try {
            global $wpdb;
            $sqlEmpresa=$wpdb->prepare("SELECT COUNT(*) FROM {$this->tablaEmpresas} WHERE nit = %s", $datos['nit']);
            $existe = $wpdb->get_var($sqlEmpresa);
            $dataEmpresa = [
                'nit' => $datos['nit'],
                'razon_social' => $datos['razon_social'],
                'direccion' => $datos['direccion'],
                'barrio' => $datos['barrio'],
                'representante_legal' => $datos['representante_legal'],
                'email' => $datos['email'],
            ];
            $result = $wpdb->insert($this->tablaEmpresas, $dataEmpresa);
            $id_empresa = $wpdb->insert_id;
            $dataInspeccion = [
                'id_empresa' => $id_empresa,
                'nombre_encargado' => $datos['nombre_encargado'],
                'telefono_encargado' => $datos['telefono_encargado'],
            ];
            $result = $wpdb->insert($this->tablaInspecciones, $dataInspeccion);
            $id_inspeccion = $wpdb->insert_id;

            $strSql = $wpdb->prepare("
                SELECT 
                    e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                    e.representante_legal, e.email,
                    i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                    i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
                FROM {$this->tablaEmpresas} AS e
                INNER JOIN {$this->tablaInspecciones} AS i 
                    ON e.id_empresa = i.id_empresa
                WHERE e.id_empresa = %d AND i.id_inspeccion = %d
            ", $id_empresa, $id_inspeccion);
            $resultado = $wpdb->get_row($strSql, ARRAY_A);

        //Función de Correo
        $this->_enviarCorreoConfirmacionInspeccion($resultado);
       


            ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Empresa y solicitud registradas con éxito', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function actualizarEmpresaInsertarSolicitud($datos)
    {
        try {
            global $wpdb;
            $id_empresa = (int)$datos['id_empresa'];
            $dataEmpresa = [
                'representante_legal' => $datos['representante_legal'],
                'email' => $datos['email'],
            ];
            $result = $wpdb->update($this->tablaEmpresas, $dataEmpresa, ['id_empresa' => $id_empresa]);
            $dataInspeccion = [
                'id_empresa' => $id_empresa,
                'nombre_encargado' => $datos['nombre_encargado'],
                'telefono_encargado' => $datos['telefono_encargado'],
            ];
            $result = $wpdb->insert($this->tablaInspecciones, $dataInspeccion);
            $id_inspeccion = $wpdb->insert_id;

            $strSql = $wpdb->prepare("
                SELECT 
                    e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                    e.representante_legal, e.email,
                    i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                    i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
                FROM {$this->tablaEmpresas} AS e
                INNER JOIN {$this->tablaInspecciones} AS i 
                    ON e.id_empresa = i.id_empresa
                WHERE e.id_empresa = %d AND i.id_inspeccion = %d
            ", $id_empresa, $id_inspeccion);
            $resultado = $wpdb->get_row($strSql, ARRAY_A);


            // Funcion enviar correo.
        $this->_enviarCorreoConfirmacionInspeccion($resultado);
        

            ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Solicitud registrada con éxito', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }


    private function _enviarCorreoConfirmacionInspeccion($datosCorreo)
    {
        // Si no tenemos datos, no hacemos nada.
        if (empty($datosCorreo) || !is_array($datosCorreo)) {
            return;
        }

        // 1. Preparamos los datos del correo
        $para = $datosCorreo['email'];
        $asunto = 'Confirmación de Solicitud de Inspección - Bomberos Pamplona';

        // 2. Construimos el cuerpo del correo en HTML
        $cuerpo = '<h1>Solicitud Registrada Exitosamente</h1>';
        $cuerpo .= '<p>Estimado(a) ' . esc_html($datosCorreo['representante_legal']) . ',</p>';
        $cuerpo .= '<p>Hemos recibido su solicitud de inspección para la empresa <strong>' . esc_html($datosCorreo['razon_social']) . '</strong> (NIT: ' . esc_html($datosCorreo['nit']) . ') con los siguientes detalles:</p>';
        $cuerpo .= '<ul>';
        $cuerpo .= '<li><strong>Fecha de Registro:</strong> ' . esc_html($datosCorreo['fecha_registro']) . '</li>';
        $cuerpo .= '<li><strong>Persona de Contacto en Sitio:</strong> ' . esc_html($datosCorreo['nombre_encargado']) . '</li>';
        $cuerpo .= '<li><strong>Teléfono de Contacto:</strong> ' . esc_html($datosCorreo['telefono_encargado']) . '</li>';
        $cuerpo .= '</ul>';
        $cuerpo .= '<p>Nuestro equipo se pondrá en contacto con usted próximamente para coordinar la fecha y hora de la visita.</p>';
        $cuerpo .= '<p>Gracias por utilizar nuestros servicios.</p>';
        $cuerpo .= '<hr><p>Cuerpo de Bomberos Voluntarios de Pamplona</p>';

        // 3. Enviamos el correo usando el método de la clase base
        try {
            $this->enviarCorreoPorGmail($para, $asunto, $cuerpo);
        } catch (Exception $e) {
            // Si falla el correo, lo registramos en el log pero no detenemos el proceso
            
            $this->logError("Fallo al enviar correo de confirmación de inspección a {$para}: " . $e->getMessage());
        }
    }




}
