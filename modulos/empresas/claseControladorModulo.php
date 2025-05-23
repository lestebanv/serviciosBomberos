<?php
class ControladorEmpresas extends ClaseControladorBaseBomberos
{
    public function ejecutarFuncionalidad($request)
    {
        $funcionalidad = $request['funcionalidad'];

        switch ($funcionalidad) {
            case 'inicial':
            case 'pagina_inicial':
                return $this->listarEmpresas($request);
            case 'editar_empresa':
                return $this->formularioEdicion($request);
            case 'actualizar_empresa':
                return $this->actualizarEmpresa($request);
            case 'form_crear':
                return $this->formularioCreacion(request: $request);
            case 'registrar_empresa':
                return $this->insertarEmpresa(request: $request);
            case 'eliminar_empresa':
                return $this->eliminarEmpresa($request);
            default:
                return $this->armarRespuesta('Funcionalidad ' . $funcionalidad . ' no encontrada en el módulo.');
        }
    }

    public function listarEmpresas($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'empresa';

        $items_per_page = 10;
        $current_page = isset($request['paged']) ? max(1, intval($request['paged'])) : 1;
        $offset = ($current_page - 1) * $items_per_page;

        $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $total_pages = ceil($total_registros / $items_per_page);

        $sql = $wpdb->prepare("SELECT * FROM $table_name ORDER BY nit DESC LIMIT %d OFFSET %d", $items_per_page, $offset);
        $lista_empresas = $wpdb->get_results($sql, ARRAY_A);

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/listadoEmpresas.php';
        $html = ob_get_clean();

        return $this->armarRespuesta('Lista de empresas cargada con éxito', $html);
    }

    public function formularioEdicion($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'empresa';

        $id = isset($request['id']) ? intval($request['id']) : 0;

        if ($id <= 0) {
            return $this->armarRespuesta('ID inválido para edición.', '', false);
        }

        $empresa = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_empresa = %d", $id), ARRAY_A);

        if (!$empresa) {
            return $this->armarRespuesta('Empresa no encontrada.', '', false);
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/formularioEditarEmpresa.php';
        $html = ob_get_clean();

        return $this->armarRespuesta('Formulario de edición cargado.', $html);
    }

    public function eliminarEmpresa($request)
    {
        global $wpdb;
        $tabla = $wpdb->prefix . 'empresa';

        $id = isset($request['id']) ? intval($request['id']) : 0;

        if ($id <= 0) {
            return $this->armarRespuesta('ID de empresa no válido');
        }

        $resultado = $wpdb->delete($tabla, ['id_empresa' => $id]);

        if ($resultado !== false) {
            return $this->listarEmpresas($request);
        } else {
            return $this->armarRespuesta('Error al eliminar la empresa.');
        }
    }


    public function actualizarEmpresa($request)
    {
        global $wpdb;

        parse_str($request['form_data'], $form);

        $tabla = $wpdb->prefix . 'empresa';

        // Validaciones básicas
        $id = isset($form['id_empresa']) ? intval($form['id_empresa']) : 0;
        $datos = array(
            'razon_social' => sanitize_text_field($form['razon_social']),
            'direccion' => sanitize_text_field($form['direccion']),
            'barrio' => sanitize_text_field($form['barrio']),
            'representante_legal' => sanitize_text_field($form['representante_legal']),
            'email' => sanitize_text_field($form['email']),
        );

        if ($id > 0) {
            // Actualizar empresa existente
            $actualizado = $wpdb->update(
                $tabla,
                $datos,
                ['id_empresa' => $id]
            );

            if ($actualizado == true) {
                return $this->listarEmpresas($request);
            }else{
                return $this->armarRespuesta('No se pudo actualizar la empresa o no hubo cambios.');
            }
        }
    }
    public function formularioCreacion($request)
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/formularioCrearEmpresa.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);
    }
    public function insertarEmpresa($request)
    {
        global $wpdb;
        $tabla = $wpdb->prefix . 'empresa';

        // Convertir form_data en array
        parse_str($request['form_data'], $data);

        // Validar campos requeridos
        $campos_obligatorios = ['nit', 'razon_social', 'direccion', 'barrio', 'representante_legal', 'email'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($data[$campo])) {
                return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
            }
        }

        // Verificar si ya existe una empresa con el mismo NIT
        $existe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla WHERE nit = %s", $data['nit']));
        if ($existe > 0) {
            return $this->armarRespuesta("Ya existe una empresa registrada con el NIT: {$data['nit']}", null, false);
        }

        // Insertar en la base de datos
        $insertado = $wpdb->insert(
            $tabla,
            [
                'nit' => sanitize_text_field($data['nit']),
                'razon_social' => sanitize_text_field($data['razon_social']),
                'direccion' => sanitize_text_field($data['direccion']),
                'barrio' => sanitize_text_field($data['barrio']),
                'representante_legal' => sanitize_text_field($data['representante_legal']),
                'email' => sanitize_email($data['email']),
            ]
        );

        if ($insertado === false) {
            return $this->armarRespuesta('Ocurrió un error al guardar');
        } else {
            return $this->listarEmpresas($request);
        }
    }

}