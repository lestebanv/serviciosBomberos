<?php
class ControladorEmpresas extends ClaseControladorBaseBomberos
{
    protected $tablaEmpresas;
    protected $tablaInspecciones;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_empresa' => 'int',
            'email' => 'email',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaEmpresas = $wpdb->prefix . 'empresas';
        $this->tablaInspecciones = $wpdb->prefix . 'inspecciones';
    }

    public function ejecutarFuncionalidad($solicitud)
    {
        try {
            $solicitudSanitizada = $this->sanitizarRequest($solicitud, $this->reglasSanitizacion);
            $funcionalidad = $solicitudSanitizada['funcionalidad'] ?? '';
            $datos = $solicitudSanitizada['form_data'] ?? [];
            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }
            
            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarEmpresas($datos);
                case 'editar_empresa':
                    return $this->formularioEdicion($datos);
                case 'actualizar_empresa':
                    return $this->actualizarEmpresa($datos);
                case 'form_crear':
                    return $this->formularioCreacion($datos);
                case 'registrar_empresa':
                    return $this->insertarEmpresa($datos);
                case 'eliminar_empresa':
                    return $this->eliminarEmpresa($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad '" . esc_html($funcionalidad) . "' no encontrada.");
            }
        } catch (Exception $e) {
            $this->$this->manejarExcepcion($e, $solicitud);
        }
    }

    public function listarEmpresas($datos)
    {
        try {
            global $wpdb;
            $itemsPorPagina = 4;
            
            $actualpagina = isset($datos['actualpagina']) ? max(1, (int) $datos['actualpagina']) : 1;
            $offset = ($actualpagina - 1) * $itemsPorPagina;

            $totalRegistros = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaEmpresas}");
            if ($totalRegistros === null) {
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $totalpaginas = ceil($totalRegistros / $itemsPorPagina);

            $strSqlEmpresas = $wpdb->prepare(
                "SELECT * FROM {$this->tablaEmpresas} ORDER BY UPPER(razon_social) ASC LIMIT %d OFFSET %d",
                $itemsPorPagina,
                $offset
            );
            $listaEmpresas = $wpdb->get_results($strSqlEmpresas, ARRAY_A);

            if ($listaEmpresas === null) {
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoEmpresas.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de empresas', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;

            $idEmpresa = (int) ($datos['id'] ?? 0);
            $actualpagina=$datos['actualpagina'];
            if ($idEmpresa <= 0) {
                $this->lanzarExcepcion("ID inválido de empresa ");
            }

            $strSqlEmpresa = $wpdb->prepare(
                "SELECT * FROM {$this->tablaEmpresas} WHERE id_empresa = %d",
                $idEmpresa
            );
            $empresa = $wpdb->get_row($strSqlEmpresa, ARRAY_A);

            if (!$empresa) {
                $this->lanzarExcepcion("Empresa no encontrada:".$idEmpresa);
            }

            $strSqlInspecciones = $wpdb->prepare(
                "SELECT * FROM {$this->tablaInspecciones} WHERE id_empresa = %d",
                $idEmpresa
            );
            $listaInspecciones = $wpdb->get_results($strSqlInspecciones, ARRAY_A);

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarEmpresa.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('Formulario de edición cargado', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function eliminarEmpresa($datos)
    {
        try {
            global $wpdb;
            $idEmpresa = (int) ($datos['id'] ?? 0);
            if ($idEmpresa <= 0) {
                $this->lanzarExcepcion("ID de empresa no válido:".$idEmpresa);
            }

            $resultado = $wpdb->delete($this->tablaEmpresas, ['id_empresa' => $idEmpresa]);
            if ($resultado === false) {
                $this->lanzarExcepcion("Error al eliminar la empresa por id".$idEmpresa);
            }

            return $this->listarEmpresas($datos);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function actualizarEmpresa($datos)
    {
        try {
            global $wpdb;
            $idEmpresa = (int) ($datos['id_empresa'] ?? 0);
            $actualpagina=$datos['actualpagina'];
            $datosEmpresa = [
                'razon_social' => $datos['razon_social'] ?? '',
                'direccion' => $datos['direccion'] ?? '',
                'barrio' => $datos['barrio'] ?? '',
                'representante_legal' => $datos['representante_legal'] ?? '',
                'email' => $datos['email'] ?? '',
            ];

            foreach ($datosEmpresa as $campo => $valor) {
                if (empty($valor)) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            if ($idEmpresa <= 0) {
                $this->lanzarExcepcion("ID de empresa no válido.");
            }

            $resultado = $wpdb->update($this->tablaEmpresas, $datosEmpresa, ['id_empresa' => $idEmpresa]);
            if ($resultado === false) {
                $this->lanzarExcepcion("No se pudo actualizar la empresa.");
            }
            return $this->listarEmpresas($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function formularioCreacion($datos)
    {
        try {
            $actualpagina=$datos['actualpagina'];
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearEmpresa.php';
            $html = ob_get_clean();
            return $this->armarRespuesta("Formulario de creación cargado", $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function insertarEmpresa($datos)
    {
        try {
            global $wpdb;
            $camposObligatorios = ['nit', 'razon_social', 'direccion', 'barrio', 'representante_legal', 'email'];

            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio en insertar empresa.");
                }
            }

            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaEmpresas} WHERE nit = %s",
                $datos['nit']
            ));
            if ($existe > 0) {
                $this->lanzarExcepcion("Ya existe una empresa registrada con el NIT: {$datos['nit']}");
            }

            $resultado = $wpdb->insert($this->tablaEmpresas, [
                'nit' => $datos['nit'],
                'razon_social' => $datos['razon_social'],
                'direccion' => $datos['direccion'],
                'barrio' => $datos['barrio'],
                'representante_legal' => $datos['representante_legal'],
                'email' => $datos['email'],
            ]);

            if ($resultado === false) {
                $this->lanzarExcepcion("Error al guardar: " . esc_html($wpdb->last_error));
            }

            return $this->listarEmpresas($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}
