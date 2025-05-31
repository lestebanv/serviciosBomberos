<?php
class ControladorEmpresas extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'id' => 'int',
        'paged' => 'int',
        'form_data' => [
            'id_empresa' => 'int',
            'email' => 'email',
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $funcionalidad = isset($request['funcionalidad']) ? $request['funcionalidad'] : '';

            if (empty($funcionalidad)) {
                $this->enviarLog("Funcionalidad no especificada en la solicitud", $request);
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarEmpresas($sanitized_request);
                case 'editar_empresa':
                    return $this->formularioEdicion($sanitized_request);
                case 'actualizar_empresa':
                    return $this->actualizarEmpresa($sanitized_request);
                case 'form_crear':
                    return $this->formularioCreacion($sanitized_request);
                case 'registrar_empresa':
                    return $this->insertarEmpresa($sanitized_request);
                case 'eliminar_empresa':
                    return $this->eliminarEmpresa($sanitized_request);
                default:
                    $this->enviarLog("Funcionalidad no encontrada", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad '" . esc_html($funcionalidad) . "' no encontrada en el módulo.");
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e; // Relanzar para que el manejador AJAX lo capture
        }
    }

    public function listarEmpresas($request)
    {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'empresas';

            $items_per_page = 4;
            $current_page = isset($request['form_data']['paged']) ? max(1, (int) $request['form_data']['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $table_name", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $total_pages = ceil($total_registros / $items_per_page);

            $sql = $wpdb->prepare("SELECT * FROM $table_name ORDER BY UPPER(razon_social) ASC LIMIT %d OFFSET %d", $items_per_page, $offset);
            $lista_empresas = $wpdb->get_results($sql, ARRAY_A);
            if ($lista_empresas === null) {
                $this->enviarLog("Error al obtener lista de empresas", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoEmpresas.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de empresas ordenadas alfabeticamente', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarEmpresas: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioEdicion($request)
    {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'empresas';
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $paged=isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 1;
            if ($id <= 0) {
                $this->enviarLog("ID inválido para edición", ['id' => $id]);
                $this->lanzarExcepcion('ID inválido para edición.');
            }

            $empresa = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_empresa = %d", $id), ARRAY_A);
            if (!$empresa) {
                $this->enviarLog("Empresa no encontrada", ['id' => $id]);
                $this->lanzarExcepcion('Empresa no encontrada.');
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarEmpresa.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('Formulario de edición cargado.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioEdicion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function eliminarEmpresa($request)
    {
        try {
            global $wpdb;
            $tabla = $wpdb->prefix . 'empresas';
            $id = isset($request['form_data']['id']) ? (int) $request['form_data']['id'] : 0;
            $paged = isset($request['form_data']['paged']) ? (int) $request['form_data']['paged'] : 0;
            if ($id <= 0) {
                $this->enviarLog("ID de empresa no válido", ['id' => $id]);
                $this->lanzarExcepcion('ID de empresa no válido.');
            }

            $resultado = $wpdb->delete($tabla, ['id_empresa' => $id]);
            if ($resultado === false) {
                $this->enviarLog("Error al eliminar empresa", ['id' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('Error al eliminar la empresa.');
            }

            return $this->listarEmpresas($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en eliminarEmpresa: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function actualizarEmpresa($request)
    {
        try {
            global $wpdb;
            $form = $request['form_data'] ?? [];
            $tabla = $wpdb->prefix . 'empresas';

            $id = isset($form['id_empresa']) ? (int) $form['id_empresa'] : 0;
            $datos = [
                'razon_social' => $form['razon_social'] ?? '',
                'direccion' => $form['direccion'] ?? '',
                'barrio' => $form['barrio'] ?? '',
                'representante_legal' => $form['representante_legal'] ?? '',
                'email' => $form['email'] ?? '',
            ];

            $campos_obligatorios = ['razon_social', 'direccion', 'barrio', 'representante_legal', 'email'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            if ($id <= 0) {
                $this->enviarLog("ID de empresa no válido", ['id_empresa' => $id]);
                $this->lanzarExcepcion('ID de empresa no válido.');
            }

            $actualizado = $wpdb->update($tabla, $datos, ['id_empresa' => $id]);
            if ($actualizado === false) {
                $this->enviarLog("Error al actualizar empresa", ['id_empresa' => $id], $wpdb->last_error);
                $this->lanzarExcepcion('No se pudo actualizar la empresa.');
            }

            return $this->listarEmpresas($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en actualizarEmpresa: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function formularioCreacion($request)
    {
        try {
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearEmpresa.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en formularioCreacion: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function insertarEmpresa($request)
    {
        try {
            global $wpdb;
            $tabla = $wpdb->prefix . 'empresas';
            $data = $request['form_data'] ?? [];

            $campos_obligatorios = ['nit', 'razon_social', 'direccion', 'barrio', 'representante_legal', 'email'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($data[$campo])) {
                    $this->enviarLog("Campo obligatorio faltante", ['campo' => $campo]);
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $existe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla WHERE nit = %s", $data['nit']));
            if ($existe > 0) {
                $this->enviarLog("NIT ya registrado", ['nit' => $data['nit']]);
                $this->lanzarExcepcion("Ya existe una empresa registrada con el NIT: {$data['nit']}");
            }

            $insertado = $wpdb->insert(
                $tabla,
                [
                    'nit' => $data['nit'],
                    'razon_social' => $data['razon_social'],
                    'direccion' => $data['direccion'],
                    'barrio' => $data['barrio'],
                    'representante_legal' => $data['representante_legal'],
                    'email' => $data['email'],
                ]
            );

            if ($insertado === false) {
                $this->enviarLog("Error al insertar empresa", $data, $wpdb->last_error);
                $this->lanzarExcepcion('Ocurrió un error al guardar: ' . esc_html($wpdb->last_error));
            }

            return $this->listarEmpresas($request);
        } catch (Exception $e) {
            $this->enviarLog("Error en insertarEmpresa: " . $e->getMessage(), $request);
            throw $e;
        }
    }
}