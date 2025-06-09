<?php
class ControladorInspecciones extends ClaseControladorBaseBomberos
{
    protected $tablaEmpresas;
    protected $tablaInspecciones;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_empresa' => 'int',
            'id_inspeccion'=>'int',
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
                $this->lanzarExcepcion("Funcionalidad no especificada en la solicitud");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInspecciones($datos);
                case 'editar_inspeccion':
                    return $this->formularioEdicion($datos);
                case 'actualizar_inspeccion':
                    return $this->actualizarInspeccion($datos);
                case 'eliminar_inspeccion':
                    return $this->eliminarInspeccion($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->manejarExcepcion( $e, $solicitud);
        }
    }

    public function listarInspecciones($datos)
    {
        try {
            global $wpdb;
            $elementosPorPagina = 5;
            $actualpagina = isset($datos['actualpagina']) ? max(1, (int) $datos['actualpagina']) : 1;
            $offset = ($actualpagina - 1) * $elementosPorPagina;

            $totalRegistros = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaInspecciones}");
            $totalpaginas = ceil($totalRegistros / $elementosPorPagina);

            $sqlInspecciones = $wpdb->prepare(
                "SELECT i.*, e.razon_social, e.direccion, e.barrio 
                    FROM {$this->tablaInspecciones} i 
                    LEFT JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa 
                    ORDER BY i.estado DESC, i.fecha_registro ASC 
                    LIMIT %d OFFSET %d;", 
                $elementosPorPagina,
                $offset
            );
            $listaInspecciones = $wpdb->get_results($sqlInspecciones, ARRAY_A);
            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoInspecciones.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inspecciones cargada con éxito', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;
            $id = isset($datos['id']) ? (int) $datos['id'] : 0;
            $actualpagina = isset($datos['actualpagina']) ? (int) $datos['actualpagina'] : 1;
            $sqlInspeccion=$wpdb->prepare(
                "SELECT i.*, e.razon_social, e.nit, e.direccion, e.barrio
                 FROM {$this->tablaInspecciones} i 
                 LEFT JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa 
                 WHERE i.id_inspeccion = %d",
                $id
            );
            $inspeccion = $wpdb->get_row($sqlInspeccion, ARRAY_A);

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarInspeccion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

   
    public function actualizarInspeccion($datos)
    {
        try {
            global $wpdb;
            $id_inspeccion=$datos['id_inspeccion'];
            $camposObligatorios = ['id_inspeccion', 'nombre_encargado', 'telefono_encargado', 'fecha_programada', 'estado'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            };
            $estadosPermitidos = ['Registrada', 'En Proceso', 'Cerrada'];
            $datosActualizar = [
                'fecha_programada' => $datos['fecha_programada'],
                'fecha_expedicion' => !empty($datos['fecha_expedicion']) ? $datos['fecha_expedicion'] : null,
                'estado' => $datos['estado'],
                'nombre_encargado' => $datos['nombre_encargado'],
                'telefono_encargado' => $datos['telefono_encargado'],
            ];
            $actualizado = $wpdb->update($this->tablaInspecciones, $datosActualizar, ['id_inspeccion' => $id_inspeccion]);
            return $this->listarInspecciones($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

    public function eliminarInspeccion($datos)
    {
        try {
            global $wpdb;
            $id = isset($datos['id']) ? (int) $datos['id'] : 0;
            $actualpagina = isset($datos['actualpagina']) ? (int) $datos['actualpagina'] : 1;
            $resultado = $wpdb->delete($this->tablaInspecciones, ['id_inspeccion' => $id]);
            return $this->listarInspecciones($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }
}