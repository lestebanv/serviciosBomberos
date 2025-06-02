<?php
if (!defined('ABSPATH'))   exit;

class ControladorPQR extends ClaseControladorBaseBomberos
{
    protected $tablaPqrs;

    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id' => 'int',
            'email' => 'email',
            'estado_solicitud' => 'text',
            'respuesta' => 'textarea',
        ],
    ];

    public function __construct() {
        global $wpdb;
        $this->tablaPqrs = $wpdb->prefix . 'pqrs';
    }

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            // La funcionalidad puede venir directamente o dentro de form_data (ej. paginación)
            $funcionalidad = $sanitized_request['funcionalidad'] 
                             ?? ($sanitized_request['form_data']['funcionalidad'] ?? '');


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
            $items_per_page = 5;
             // Paged puede venir de $request['paged'] o $request['form_data']['paged']
            $current_page = $request['form_data']['paged'] ?? ($request['paged'] ?? 1);
            $current_page = max(1, (int)$current_page);
            $offset = ($current_page - 1) * $items_per_page;

            $total = $wpdb->get_var("SELECT COUNT(*) FROM $this->tablaPqrs");
            if ($total === null) {
                $this->lanzarExcepcion("No se pudo obtener el total de PQR.");
            }

            $total_pages = ceil($total / $items_per_page);

            $lista_pqr = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $this->tablaPqrs ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
                $items_per_page, $offset
            ), ARRAY_A);

            if ($lista_pqr === null) {
                $this->lanzarExcepcion("No se pudo obtener la lista de PQR.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoPqr.php';
            $html = ob_get_clean();
            // El array asociativo como tercer argumento es opcional y no se usa en la función armarRespuesta del padre.
            // Si necesitas pasar 'total_pages' al JS, tendrás que manejarlo en la respuesta JSON de otra manera.
            // Por ahora, lo quito ya que armarRespuesta no lo usa.
            return $this->armarRespuesta('Listado de PQR cargado', $html); 
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
            $paged = (int) ($request['form_data']['paged'] ?? 1);
            if ($id <= 0) {
                $this->lanzarExcepcion("ID no válido para responder.");
            }

            $pqr = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->tablaPqrs WHERE id = %d", $id), ARRAY_A);
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
            $form = $request['form_data'] ?? [];

            $id = (int) ($form['id'] ?? 0);
            if ($id <= 0) {
                $this->lanzarExcepcion("ID de PQR no válido.");
            }
            
            $respuesta = $form['respuesta'] ?? ''; // Sanitizado por bomberos_sanitize_input como textarea
            $estado_solicitud = $form['estado_solicitud'] ?? 'Registrada'; // Sanitizado como text
            
            $estados_validos = ['Registrada', 'Pendiente', 'En Proceso', 'Cerrada']; // Ajusta según tus estados
            if (!in_array($estado_solicitud, $estados_validos)) {
                 $this->lanzarExcepcion("Estado de solicitud no válido.");
            }

            $actualizado = $wpdb->update($this->tablaPqrs, [
                'respuesta' => $respuesta,
                'estado_solicitud' => $estado_solicitud,
                'fecha_respuesta' => current_time('mysql'),
            ], ['id' => $id]);

            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar PQR", ['id' => $id, 'data' => $form], $wpdb->last_error);
                $this->lanzarExcepcion("Error al guardar la respuesta.");
            }
            
            // $form ya tiene 'paged' del formulario de edición
            return $this->listarPQR(['form_data' => $form]);
        } catch (Exception $e) {
            $this->enviarLog("Error en responderPQR: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarPQR($request)
    {
        try {
            global $wpdb;
            $id = (int) ($request['form_data']['id'] ?? 0);

            if ($id <= 0) {
                $this->lanzarExcepcion("ID no válido para eliminar.");
            }

            $resultado = $wpdb->delete($this->tablaPqrs, ['id' => $id]);
            if ($resultado === false) {
                $this->lanzarExcepcion("No se pudo eliminar la PQR.");
            }
            
            // $request['form_data'] ya tiene 'paged' del botón de eliminar
            return $this->listarPQR($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarPQR: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}