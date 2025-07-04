<?php
class ControladorInspecciones extends ClaseControladorBaseBomberos
{
    protected $tablaEmpresas;
    protected $tablaInspecciones;
    protected $tablaBomberos;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_empresa' => 'int',
            'id_inspeccion'=>'int',
            'id_bombero_asignado' => 'int',
            'email' => 'email',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaEmpresas = $wpdb->prefix . 'empresas';
        $this->tablaInspecciones = $wpdb->prefix . 'inspecciones';
        $this->tablaBomberos = $wpdb->prefix . 'bomberos';
      
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


                 $sqlInspecciones = $wpdb->prepare("  
                      SELECT 
                       i.*,
                       e.razon_social, e.direccion, e.barrio,
                       CONCAT(b.apellidos,' ',b.nombres) AS nombre_bombero_asignado
                    FROM {$this->tablaInspecciones} i
                    INNER JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa
                    LEFT JOIN {$this->tablaBomberos} b ON i.id_inspector_asignado = b.id_bombero
                    ORDER BY i.fecha_registro DESC;");


            // $sqlInspecciones = $wpdb->prepare(
            //     "SELECT i.*, e.razon_social, e.direccion, e.barrio 
            //         FROM {$this->tablaInspecciones} i 
            //         LEFT JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa 
            //         ORDER BY i.estado DESC, i.fecha_registro ASC 
            //         LIMIT %d OFFSET %d;", 
            //     $elementosPorPagina,
            //     $offset
            // );
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
             $estadoValidos=$this->valoresUnicos($this->tablaInspecciones,'estado');
            // Obtener lista de bomberos activos para el desplegable
            $listaBomberos = $wpdb->get_results(
                "SELECT id_bombero, nombres, apellidos, telefono FROM {$this->tablaBomberos} WHERE estado = 'activo' ORDER BY apellidos ASC, nombres ASC", 
                ARRAY_A
            );

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
            $id_inspeccion = (int) $datos['id_inspeccion'];
            $id_inspector_asignado = !empty($datos['id_inspector_asignado']) ? (int)$datos['id_inspector_asignado'] : null;


            if ($id_inspector_asignado) {
                
                        $datosActualizar = [
                            'fecha_programada' => !empty($datos['fecha_programada']) ? $datos['fecha_programada'] : null,
                            'fecha_expedicion' => !empty($datos['fecha_expedicion']) ? $datos['fecha_expedicion'] : null,
                            'estado' => $datos['estado'],
                            'nombre_encargado' => $datos['nombre_encargado'], 
                            'telefono_encargado' => $datos['nombre_encargado'], 
                            'id_inspector_asignado' => $id_inspector_asignado
                       ];

                
                }else{
                        $datosActualizar = [
                            'fecha_programada' => !empty($datos['fecha_programada']) ? $datos['fecha_programada'] : null,
                            'fecha_expedicion' => !empty($datos['fecha_expedicion']) ? $datos['fecha_expedicion'] : null,
                            'estado' => $datos['estado'],
                            'nombre_encargado' => $datos['nombre_encargado'], 
                            'telefono_encargado' => $datos['nombre_encargado'], 
                        ];

                }
            
            $actualizado = $wpdb->update($this->tablaInspecciones, $datosActualizar, ['id_inspeccion' => $id_inspeccion]);
            
            if ($actualizado === false) {
                 $this->lanzarExcepcion("Error al actualizar la inspección.");
            }
            

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