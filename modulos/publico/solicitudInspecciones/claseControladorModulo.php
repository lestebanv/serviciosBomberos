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
        $this->tablaCursos = $wpdb->prefix . 'cursos';
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
    }
    public function ejecutarShortCode() {
        try {
            ob_start();
            include plugin_dir_path(__FILE__) . 'frmBuscarEmpresa.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar el shortcode: ' . $e->getMessage(), null, false);
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
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar la funcionalidad: ' . $e->getMessage(), null, false);
        }
    }

    public function enviarFrmEmpresa($datos)
    {
        try {
            global $wpdb;
            $nit = $datos['nit'] ?? '';
            $strsql = $wpdb->prepare("SELECT * FROM {$this->$tablaEmpresas} WHERE nit = %s", $nit);
            $empresa = $wpdb->get_row($strsql, ARRAY_A);
*-** **************************************************
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
            return $this->armarRespuesta('Error procesando la solicitud: ' . $e->getMessage(), null, false);
        }
    }

    public function registrarSolicitudInspeccion($request)
    {
        try {
            $form_data = $request['form_data'] ?? [];
            $this->enviarLog("se recibio el siguiente formulario",$form_data);
            if (isset($form_data['id_empresa'])) {
                return $this->actualizarEmpresaInsertarSolicitud($form_data);
            } else {
                return $this->insertarEmpresaInsertarSolicitud($form_data);
            }
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al registrar la solicitud: ' . $e->getMessage(), null, false);
        }
    }

    public function insertarEmpresaInsertarSolicitud($form_data)
    {
        try {
            global $wpdb;
        
            $campos_obligatorios = ['nit', 'razon_social', 'direccion', 'barrio', 'representante_legal', 'email', 'nombre_encargado', 'telefono_encargado'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
                }
            }

            $existe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->tablaEmpresas} WHERE nit = %s", $form_data['nit']));
            if ($existe > 0) {
                return $this->armarRespuesta("Ya existe una empresa registrada con el NIT: {$form_data['nit']}", null, false);
            }

            $dataEmpresa = [
                'nit' => $form_data['nit'],
                'razon_social' => $form_data['razon_social'],
                'direccion' => $form_data['direccion'],
                'barrio' => $form_data['barrio'],
                'representante_legal' => $form_data['representante_legal'],
                'email' => $form_data['email'],
            ];
            $result = $wpdb->insert($tabla_empresas, $dataEmpresa);
            if ($result === false) {
                return $this->armarRespuesta('Error al registrar la empresa: ' . esc_html($wpdb->last_error), null, false);
            }
            $id_empresa = $wpdb->insert_id;

            $dataInspeccion = [
                'id_empresa' => $id_empresa,
                'nombre_encargado' => $form_data['nombre_encargado'],
                'telefono_encargado' => $form_data['telefono_encargado'],
            ];
            $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
            if ($result === false) {
                return $this->armarRespuesta('Error al registrar la inspección: ' . esc_html($wpdb->last_error), null, false);
            }
            $id_inspeccion = $wpdb->insert_id;

            $strSql = $wpdb->prepare("
                SELECT 
                    e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                    e.representante_legal, e.email,
                    i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                    i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
                FROM $tabla_empresas AS e
                INNER JOIN $tabla_inspecciones AS i 
                    ON e.id_empresa = i.id_empresa
                WHERE e.id_empresa = %d AND i.id_inspeccion = %d
            ", $id_empresa, $id_inspeccion);
            $resultado = $wpdb->get_row($strSql, ARRAY_A);
            ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Empresa y solicitud registradas con éxito', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error inesperado al registrar empresa y solicitud: ' . $e->getMessage(), null, false);
        }
    }

    public function actualizarEmpresaInsertarSolicitud($form_data)
    {
        try {
            global $wpdb;
            $tabla_empresas = $wpdb->prefix . 'empresas';
            $tabla_inspecciones = $wpdb->prefix . 'inspecciones';

            $campos_obligatorios = ['id_empresa', 'representante_legal', 'email', 'nombre_encargado', 'telefono_encargado'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
                }
            }

            $id_empresa = (int)$form_data['id_empresa'];
            $dataEmpresa = [
                'representante_legal' => $form_data['representante_legal'],
                'email' => $form_data['email'],
            ];
            $result = $wpdb->update($tabla_empresas, $dataEmpresa, ['id_empresa' => $id_empresa]);
            if ($result === false) {
                return $this->armarRespuesta('Error al actualizar la empresa: ' . esc_html($wpdb->last_error), null, false);
            }

            $dataInspeccion = [
                'id_empresa' => $id_empresa,
                'nombre_encargado' => $form_data['nombre_encargado'],
                'telefono_encargado' => $form_data['telefono_encargado'],
            ];
            $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
            if ($result === false) {
                return $this->armarRespuesta('Error al registrar la inspección: ' . esc_html($wpdb->last_error), null, false);
            }
            $id_inspeccion = $wpdb->insert_id;

            $strSql = $wpdb->prepare("
                SELECT 
                    e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                    e.representante_legal, e.email,
                    i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                    i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
                FROM $tabla_empresas AS e
                INNER JOIN $tabla_inspecciones AS i 
                    ON e.id_empresa = i.id_empresa
                WHERE e.id_empresa = %d AND i.id_inspeccion = %d
            ", $id_empresa, $id_inspeccion);
            $resultado = $wpdb->get_row($strSql, ARRAY_A);

            ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Solicitud registrada con éxito', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error inesperado al actualizar empresa y registrar solicitud: ' . $e->getMessage(), null, false);
        }
    }
}
