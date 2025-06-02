<?php
if (!defined('ABSPATH'))   exit;

class ControladorInspecciones extends ClaseControladorBaseBomberos
{
    protected $tablaInspecciones;
    protected $tablaEmpresas;

    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_inspeccion' => 'int',
            'id_empresa' => 'int',
            // Otros campos como nombre_encargado, telefono_encargado se sanitizan como texto por defecto
        ],
    ];

    public function __construct() {
        global $wpdb;
        $this->tablaInspecciones = $wpdb->prefix . 'inspecciones';
        $this->tablaEmpresas = $wpdb->prefix . 'empresas';
    }

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
                case 'registrar_inspeccion': // Este caso no existe en el listado, probablemente para el público
                    return $this->insertarInspeccion($sanitized_request);
                case 'editar_inspeccion':
                    return $this->formularioEdicion($sanitized_request);
                case 'actualizar_inspeccion':
                    return $this->actualizarInspeccion($sanitized_request);
                case 'eliminar_inspeccion':
                    return $this->eliminarInspeccion($sanitized_request);
                case 'reporte_proximas_vencer': // Nuevo
                    return $this->reporteProximasAVencer($sanitized_request);
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
            
            $items_per_page = 5;
            $current_page = isset($request['form_data']['paged']) ? max(1, (int) $request['form_data']['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $this->tablaInspecciones");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $this->tablaInspecciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $total_pages = ceil($total_registros / $items_per_page);

            // Ordenar por estado DESC y luego por fecha_registro ASC
            $sql = $wpdb->prepare(
                "SELECT i.*, e.razon_social, e.direccion, e.barrio 
                    FROM $this->tablaInspecciones i 
                    LEFT JOIN $this->tablaEmpresas e ON i.id_empresa = e.id_empresa 
                    ORDER BY FIELD(i.estado, 'Registrada', 'En Proceso', 'Cerrada'), i.fecha_registro ASC 
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
            include plugin_dir_path(__FILE__) . 'listadoInspecciones.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inspecciones cargada con éxito', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarInspecciones: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioCreacion($request) // No se usa desde el admin actualmente, pero lo dejo
    {
        try {
            global $wpdb;
            $empresas = $wpdb->get_results("SELECT id_empresa, razon_social FROM $this->tablaEmpresas ORDER BY razon_social ASC", ARRAY_A);
            if ($empresas === null) {
                $this->enviarLog("Error al obtener lista de empresas", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearInspeccion.php'; // Este archivo no se provee, pero la función existe
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
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;

            if ($id <= 0) {
                $this->enviarLog("ID inválido para edición", ['id' => $id]);
                $this->lanzarExcepcion('ID inválido para edición.');
            }

            $inspeccion = $wpdb->get_row($wpdb->prepare(
                "SELECT i.*, e.razon_social, e.nit, e.direccion, e.barrio
                 FROM $this->tablaInspecciones i 
                 LEFT JOIN $this->tablaEmpresas e ON i.id_empresa = e.id_empresa 
                 WHERE i.id_inspeccion = %d",
                $id
            ), ARRAY_A);
            if (!$inspeccion) {
                $this->enviarLog("Inspección no encontrada", ['id' => $id]);
                $this->lanzarExcepcion('Inspección no encontrada.');
            }

            // No se necesita $empresas para editar una inspección existente, el id_empresa ya está fijado.
            
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarInspeccion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEdicion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function insertarInspeccion($request) // Para el módulo público
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            
            $campos_obligatorios = ['id_empresa', 'nombre_encargado', 'telefono_encargado', 'fecha_programada'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id_empresa = (int) $form['id_empresa'];
            $empresa = $wpdb->get_row($wpdb->prepare("SELECT id_empresa FROM $this->tablaEmpresas WHERE id_empresa = %d", $id_empresa), ARRAY_A);
            if (!$empresa) {
                $this->enviarLog("Empresa no encontrada", ['id_empresa' => $id_empresa]);
                $this->lanzarExcepcion("Empresa no encontrada para el ID: {$id_empresa}.");
            }

            $fecha_programada_obj = new DateTime($form['fecha_programada']);
            $hoy_obj = new DateTime();
            $hoy_obj->setTime(0,0,0); // Para comparar solo fechas

            if ($fecha_programada_obj < $hoy_obj) {
                $this->enviarLog("Fecha programada inválida", ['fecha_programada' => $form['fecha_programada']]);
                $this->lanzarExcepcion('La fecha programada no puede ser anterior a la fecha actual.');
            }

            $datos = [
                'id_empresa' => $id_empresa,
                'fecha_registro' => current_time('mysql'), // Usar current_time para zona horaria de WP
                'fecha_programada' => $form['fecha_programada'],
                'nombre_encargado' => $form['nombre_encargado'],
                'telefono_encargado' => $form['telefono_encargado'],
                'estado' => 'Registrada',
            ];

            $insertado = $wpdb->insert($this->tablaInspecciones, $datos);
            if ($insertado === false) {
                $this->enviarLog("Error al insertar inspección", $form, $wpdb->last_error);
                $this->lanzarExcepcion('Error al registrar la inspección: ' . esc_html($wpdb->last_error));
            }

            return $this->listarInspecciones(['form_data' => ['paged' => 1]]); // Volver a la página 1 del listado general
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
            
            $campos_obligatorios = ['id_inspeccion', 'nombre_encargado', 'telefono_encargado', 'fecha_programada', 'estado'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $id = (int) $form['id_inspeccion'];
            $inspeccion_existente = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->tablaInspecciones WHERE id_inspeccion = %d", $id), ARRAY_A);
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
            
            $fecha_programada_obj = new DateTime($form['fecha_programada']);
            $hoy_obj = new DateTime();
            $hoy_obj->setTime(0,0,0); // Para comparar solo fechas

            if ($fecha_programada_obj < $hoy_obj) {
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

            $actualizado = $wpdb->update($this->tablaInspecciones, $datos, ['id_inspeccion' => $id]);
            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar inspección", ['id_inspeccion' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al actualizar la inspección: ' . esc_html($wpdb->last_error));
            }

            // Devolver a la página donde estaba el registro editado
            $paged = isset($form['paged']) ? (int) $form['paged'] : 1;
            return $this->listarInspecciones(['form_data' => ['paged' => $paged]]);
        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarInspeccion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarInspeccion($request)
    {
        try {
            global $wpdb;
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            if ($id <= 0) {
                $this->enviarLog("ID de inspección no válido", ['id' => $id]);
                $this->lanzarExcepcion('ID de inspección no válido.');
            }

            $resultado = $wpdb->delete($this->tablaInspecciones, ['id_inspeccion' => $id]);
            if ($resultado === false) {
                $this->enviarLog("Error al eliminar inspección", ['id' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar la inspección: ' . esc_html($wpdb->last_error));
            }
            // Devolver a la página donde estaba el registro eliminado
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;
            return $this->listarInspecciones(['form_data' => ['paged' => $paged]]);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarInspeccion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function reporteProximasAVencer($request) {
        try {
            global $wpdb;
            $hoy = current_time('Y-m-d');
            $fecha_limite = date('Y-m-d', strtotime($hoy . ' +30 days'));

            $sql = $wpdb->prepare(
                "SELECT i.*, e.razon_social, e.nit
                 FROM $this->tablaInspecciones i
                 LEFT JOIN $this->tablaEmpresas e ON i.id_empresa = e.id_empresa
                 WHERE (i.estado = 'Registrada' OR i.estado = 'En Proceso')
                 AND i.fecha_programada BETWEEN %s AND %s
                 ORDER BY i.fecha_programada ASC",
                $hoy,
                $fecha_limite
            );
            $inspecciones_proximas = $wpdb->get_results($sql, ARRAY_A);

            if ($inspecciones_proximas === null) {
                $this->enviarLog("Error al obtener reporte de inspecciones próximas a vencer", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al generar el reporte.");
            }
            
            // La página actual no es relevante aquí, ya que es un reporte específico
            $current_page_listado = isset($request['form_data']['paged_listado']) ? (int) $request['form_data']['paged_listado'] : 1;


            ob_start();
            include plugin_dir_path(__FILE__) . 'reporteProximasAVencer.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Reporte de inspecciones próximas a vencer generado.', $html);

        } catch (Exception $e) {
            $this->enviarLog("Error en reporteProximasAVencer: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}