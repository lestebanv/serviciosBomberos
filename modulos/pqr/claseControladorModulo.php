
<?php
class ControladorPQR extends ClaseControladorBaseBomberos{
    protected $tablaPqrs;
    protected $reglasSanitizacion = [
        'form_data' => 
            [
                'id' => 'int',
            ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaPqrs = $wpdb->prefix . 'pqrs';
    }

    public function ejecutarFuncionalidad($peticion)
    {
        try {
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $funcionalidad = $peticionLimpia['funcionalidad'] ?? '';
            $datos=$peticionLimpia['form_data'];

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarPQR($datos);
                case 'editar_pqr':
                    return $this->formularioEdicion($datos);
                case 'actualizar_pqr':
                    return $this->actualizarPQR($datos);
                case 'eliminar_pqr':
                    return $this->eliminarPQR($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad '$funcionalidad' no reconocida.");
            }
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $solicitud);
        }
    }

    public function listarPQR($datos)
    {
        try {

            $this->logInfo("valores unicos de estado de pqr",$this->valoresUnicos($this->tablaPqrs,'estado_solicitud'));
            global $wpdb;
            $elementosPorPagina = 5;
            $actualpagina= $datos['actualpagina'] ?? 1;
            $offset = ($actualpagina- 1) * $elementosPorPagina;

            $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaPqrs}");
            if ($total === null) {
                $this->lanzarExcepcion("No se pudo obtener el total de PQR.");
            }

            $totalpaginas = ceil($total / $elementosPorPagina);
            $sqlPqrs=$wpdb->prepare(
                "SELECT * FROM {$this->tablaPqrs} ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
                $elementosPorPagina, $offset
            );
            $listaPqrs = $wpdb->get_results($sqlPqrs, ARRAY_A);
            if ($listaPqrs === null) {
                $this->lanzarExcepcion("No se pudo obtener la lista de PQR.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoPqr.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Listado de PQR cargado', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

 
    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;
            $id = (int) ($datos['id'] ?? 0);
            $actualpagina = (int) ($datos['actualpagina'] ?? 1);
            if ($id <= 0) {
                $this->lanzarExcepcion("ID de Pqr no válido para responder.");
            }
            $sqlPqr=$wpdb->prepare("SELECT * FROM {$this->tablaPqrs} WHERE id = %d", $id);
            $pqr = $wpdb->get_row($sqlPqr, ARRAY_A);
            if (!$pqr) {
                $this->lanzarExcepcion("PQR no encontrada.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarPqr.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de respuesta cargado.', $html);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function actualizarPQR($datos)
    {
        try {
            global $wpdb;
            $id = (int) ($datos['id'] ?? 0);
            $datosActualizar=[
                'respuesta' => $datos['respuesta'],
                'estado_solicitud' => $datos['estado_solicitud'],
                'fecha_respuesta' => current_time('mysql'),
            ];
            $actualizado = $wpdb->update($this->tablaPqrs, $datosActualizar,['id' => $id]);
            if ($actualizado === false) {
                $this->lanzarExcepcion("Error al guardar la respuesta.");
            }
            return $this->listarPQR($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function eliminarPQR($datos)
    {
        try {
            global $wpdb;
            $id = (int) ($datos['id'] ?? 0);

            if ($id <= 0) {
                $this->lanzarExcepcion("ID no válido para eliminar pqr.");
            }
            $resultado = $wpdb->delete($this->tablaPqrs, ['id' => $id]);
            if ($resultado === false) {
                $this->lanzarExcepcion("No se pudo eliminar la PQR.");
            }

            return $this->listarPQR($datos);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}
