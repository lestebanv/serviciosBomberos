
<?php
class ControladorPQR extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id' => 'int',
            'email' => 'email',
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $funcionalidad = $request['funcionalidad'] ?? '';

            if (empty($funcionalidad)) {
                $this->enviarLog("Funcionalidad no especificada", $request);
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarPQR($sanitized_request);
                case 'editar_pqr':
                    return $this->formularioEdicion($sanitized_request);
                case 'actualizar_pqr':
                    return $this->actualizarPQR($sanitized_request);
                case 'eliminar_pqr':
                    return $this->eliminarPQR($sanitized_request);
                default:
                    $this->enviarLog("Funcionalidad desconocida", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad '$funcionalidad' no reconocida.");
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function listarPQR($request)
    {
        try {
            global $wpdb;
            $table = $wpdb->prefix . 'pqr';
            $items_per_page = 5;
            $current_page = $request['form_data']['paged'] ?? 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
            if ($total === null) {
                $this->lanzarExcepcion("No se pudo obtener el total de PQR.");
            }

            $total_pages = ceil($total / $items_per_page);

            $lista_pqr = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
                $items_per_page, $offset
            ), ARRAY_A);

            if ($lista_pqr === null) {
                $this->lanzarExcepcion("No se pudo obtener la lista de PQR.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoPqr.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Listado de PQR cargado', $html, ['total_pages' => $total_pages]);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarPQR: " . $e->getMessage(), $request);
            throw $e;
        }
    }

 
    public function formularioEdicion($request)
    {
        try {
            global $wpdb;
            $id = (int) ($request['form_data']['id'] ?? 0);
            $tabla = $wpdb->prefix . 'pqr';
            $paged = (int) ($request['form_data']['paged'] ?? 1);
            if ($id <= 0) {
                $this->lanzarExcepcion("ID no válido para responder.");
            }

            $pqr = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla WHERE id = %d", $id), ARRAY_A);
            if (!$pqr) {
                $this->lanzarExcepcion("PQR no encontrada.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarPqr.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de respuesta cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioRespuesta: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function actualizarPQR($request)
    {
        try {
            global $wpdb;
            $tabla = $wpdb->prefix . 'pqr';
            $form = $request['form_data'] ?? [];

            $id = (int) ($form['id'] ?? 0);
            $respuesta = $form['respuesta'] ?? '';
            $estado =  $form['estado_solicitud']??'Registrada';


            $actualizado = $wpdb->update($tabla, [
                'respuesta' => $respuesta,
                'estado_solicitud' => $estado,
                'fecha_respuesta' => current_time('mysql'),
            ], ['id' => $id]);

            if ($actualizado === false) {
                $this->lanzarExcepcion("Error al guardar la respuesta.");
            }

            return $this->listarPQR($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en responderPQR: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarPQR($request)
    {
        try {
            global $wpdb;
            $tabla = $wpdb->prefix . 'pqr';
            $id = (int) ($request['form_data']['id'] ?? 0);

            if ($id <= 0) {
                $this->lanzarExcepcion("ID no válido para eliminar.");
            }

            $resultado = $wpdb->delete($tabla, ['id' => $id]);
            if ($resultado === false) {
                $this->lanzarExcepcion("No se pudo eliminar la PQR.");
            }

            return $this->listarPQR($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarPQR: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}
