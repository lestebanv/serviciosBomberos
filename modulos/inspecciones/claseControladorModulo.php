<?php
class ControladorInspecciones extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_inspeccion' => 'int',
            'id_empresa' => 'int',
            // Otros campos como nombre_encargado, telefono_encargado se sanitizan como texto por defecto
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $funcionalidad = isset($sanitized_request['funcionalidad']) ? $sanitized_request['funcionalidad'] : '';

            if (empty($funcionalidad)) {
                $this->enviarLog("Funcionalidad no especificada en la solicitud", $request);
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInspecciones($sanitized_request);
                case 'registrar_inspeccion':
                    return $this->insertarInspeccion($sanitized_request);
                case 'editar_inspeccion':
                    return $this->formularioEdicion($sanitized_request);
                case 'actualizar_inspeccion':
                    return $this->actualizarInspeccion($sanitized_request);
                case 'eliminar_inspeccion':
                    return $this->eliminarInspeccion($sanitized_request);
                default:
                    $this->enviarLog("Funcionalidad no encontrada", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function listarInspecciones($request)
    {
        try {
            global $wpdb;
            $tabla_inspecciones = $wpdb->prefix . 'inspeccion';
            $tabla_empresas = $wpdb->prefix . 'empresa';

            $items_per_page = 5;
            $current_page = isset($request['form_data']['paged']) ? max(1, (int) $request['form_data']['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_inspecciones");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $tabla_inspecciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $total_pages = ceil($total_registros / $items_per_page);

            $sql = $wpdb->prepare(
                "SELECT i.*, e.razon_social, e.direccion, e.barrio 
                    FROM $tabla_inspecciones i 
                    LEFT JOIN $tabla_empresas e ON i.id_empresa = e.id_empresa 
                    ORDER BY i.estado DESC, i.fecha_registro ASC 
                    LIMIT %d OFFSET %d;", 
                $items_per_page,
                $offset
            );
            $lista_inspecciones = $wpdb->get_results($sql, ARRAY_A);
            if ($lista_inspecciones === null) {
                $this->enviarLog("Error al obtener lista de inspecciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de inspecciones.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/listadoInspecciones.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inspecciones cargada con éxito', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarInspecciones: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioCreacion($request)
    {
        try {
            global $wpdb;
            $tabla_empresas = $wpdb->prefix . 'empresa';

            $empresas = $wpdb->get_results("SELECT id_empresa, razon_social FROM $tabla_empresas ORDER BY razon_social ASC", ARRAY_A);
            if ($empresas === null) {
                $this->enviarLog("Error al obtener lista de empresas", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/formularioCrearInspeccion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioCreacion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioEdicion($request)
    {
        try {
            global $wpdb;
            $tabla_inspecciones = $wpdb->prefix . 'inspeccion';
            $tabla_empresas = $wpdb->prefix . 'empresa';
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;

            if ($id <= 0) {
                $this->enviarLog("ID inválido para edición", ['id' => $id]);
                $this->lanzarExcepcion('ID inválido para edición.');
            }

            $inspeccion = $wpdb->get_row($wpdb->prepare(
                "SELECT i.*, e.razon_social, e.nit, e.direccion, e.barrio
                 FROM $tabla_inspecciones i 
                 LEFT JOIN $tabla_empresas e ON i.id_empresa = e.id_empresa 
                 WHERE i.id_inspeccion = %d",
                $id
            ), ARRAY_A);
            if (!$inspeccion) {
                $this->enviarLog("Inspección no encontrada", ['id' => $id]);
                $this->lanzarExcepcion('Inspección no encontrada.');
            }

            $empresas = $wpdb->get_results("SELECT id_empresa, razon_social FROM $tabla_empresas ORDER BY razon_social ASC", ARRAY_A);
            if ($empresas === null) {
                $this->enviarLog("Error al obtener lista de empresas", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/formularioEditarInspeccion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEdicion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function insertarInspeccion($request)
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            $tabla_inspecciones = $wpdb->prefix . 'inspeccion';
            $tabla_empresas = $wpdb->prefix . 'empresa';

            $campos_obligatorios = ['id_empresa', 'nombre_encargado', 'telefono_encargado', 'fecha_programada'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id_empresa = (int) $form['id_empresa'];
            $empresa = $wpdb->get_row($wpdb->prepare("SELECT id_empresa FROM $tabla_empresas WHERE id_empresa = %d", $id_empresa), ARRAY_A);
            if (!$empresa) {
                $this->enviarLog("Empresa no encontrada", ['id_empresa' => $id_empresa]);
                $this->lanzarExcepcion("Empresa no encontrada para el ID: {$id_empresa}.");
            }

            $fecha_programada = strtotime($form['fecha_programada']);
            $hoy = strtotime(date('Y-m-d')); // Fecha actual: 2025-05-24
            if ($fecha_programada < $hoy) {
                $this->enviarLog("Fecha programada inválida", ['fecha_programada' => $form['fecha_programada']]);
                $this->lanzarExcepcion('La fecha programada no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'id_empresa' => $id_empresa,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'fecha_programada' => $form['fecha_programada'],
                'nombre_encargado' => $form['nombre_encargado'],
                'telefono_encargado' => $form['telefono_encargado'],
                'estado' => 'Registrada',
            ];

            $insertado = $wpdb->insert($tabla_inspecciones, $datos);
            if ($insertado === false) {
                $this->enviarLog("Error al insertar inspección", $form, $wpdb->last_error);
                $this->lanzarExcepcion('Error al registrar la inspección: ' . esc_html($wpdb->last_error));
            }

            return $this->listarInspecciones($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en insertarInspeccion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function actualizarInspeccion($request)
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            $tabla_inspecciones = $wpdb->prefix . 'inspeccion';
            $tabla_empresas = $wpdb->prefix . 'empresa';

            $campos_obligatorios = ['id_inspeccion', 'nombre_encargado', 'telefono_encargado', 'fecha_programada', 'estado'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id = (int) $form['id_inspeccion'];
            $inspeccion_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_inspecciones WHERE id_inspeccion = %d", $id), ARRAY_A);
            if (!$inspeccion_existente) {
                $this->enviarLog("Inspección no encontrada", ['id_inspeccion' => $id]);
                $this->lanzarExcepcion('Inspección no encontrada.');
            }

            $estado = $form['estado'];
            $estados_permitidos = ['Registrada', 'En Proceso', 'Cerrada'];
            if (!in_array($estado, $estados_permitidos)) {
                $this->enviarLog("Estado inválido", ['estado' => $estado]);
                $this->lanzarExcepcion('Estado inválido.');
            }

            $fecha_programada = strtotime($form['fecha_programada']);
            $hoy = strtotime(date('Y-m-d')); // Fecha actual: 2025-05-24
            if ($fecha_programada < $hoy) {
                $this->enviarLog("Fecha programada inválida", ['fecha_programada' => $form['fecha_programada']]);
                $this->lanzarExcepcion('La fecha programada no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'fecha_programada' => $form['fecha_programada'],
                'fecha_expedicion' => !empty($form['fecha_expedicion']) ? $form['fecha_expedicion'] : null,
                'estado' => $estado,
                'nombre_encargado' => $form['nombre_encargado'],
                'telefono_encargado' => $form['telefono_encargado'],
            ];

            $actualizado = $wpdb->update($tabla_inspecciones, $datos, ['id_inspeccion' => $id]);
            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar inspección", ['id_inspeccion' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al actualizar la inspección: ' . esc_html($wpdb->last_error));
            }

            return $this->listarInspecciones($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarInspeccion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarInspeccion($request)
    {
        try {
            global $wpdb;
            $tabla_inspecciones = $wpdb->prefix . 'inspeccion';
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $current_page = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;
            if ($id <= 0) {
                $this->enviarLog("ID de inspección no válido", ['id' => $id]);
                $this->lanzarExcepcion('ID de inspección no válido.');
            }

            $resultado = $wpdb->delete($tabla_inspecciones, ['id_inspeccion' => $id]);
            if ($resultado === false) {
                $this->enviarLog("Error al eliminar inspección", ['id' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar la inspección: ' . esc_html($wpdb->last_error));
            }
            return $this->listarInspecciones($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarInspeccion: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}