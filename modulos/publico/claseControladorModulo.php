<?php
class ControladorBomberosPublico extends ClaseControladorBaseBomberos
{
    public function ejecutarFuncionalidad($request)
    {
        // Asumimos que 'funcionalidad' ya está sanitizado como texto
        $funcionalidad = isset($request['funcionalidad']) ? $request['funcionalidad'] : '';

        switch ($funcionalidad) {
            case 'mostrar_formulario':
                ob_start();
                include plugin_dir_path(__FILE__) . 'vistas/frmBuscarEmpresa.php';
                $html = ob_get_clean();
                return $this->armarRespuesta('', $html);
            case 'buscar_empresa':
                return $this->enviarFrmEmpresa($request);
            case 'registrar_empresa_solicitud':
                return $this->registrarSolicitudInspeccion($request);
            default:
                return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($funcionalidad));
        }
    }

    public function enviarFrmEmpresa($request)
    {
        global $wpdb;
        $tabla_empresas = $wpdb->prefix . 'empresa';

        // Usar form_data directamente como array sanitizado
        $form_data = $request['form_data'] ?? [];
        $nit = $form_data['nit'] ?? '';

        if (empty($nit)) {
            return $this->armarRespuesta('El campo NIT es obligatorio.');
        }

        $empresa = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_empresas WHERE nit = %s", $nit), ARRAY_A);

        if ($empresa) {
            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/frmRegistrarSoloSolicitud.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario completo enviado', $html); // Empresa ya registrada
        } else {
            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/frmRegistrarEmpresaSolicitud.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario completo enviado', $html); // Empresa no encontrada
        }
    }

    public function registrarSolicitudInspeccion($request)
    {
        // Usar form_data directamente como array sanitizado
        $form_data = $request['form_data'] ?? [];

        if (isset($form_data['id_empresa'])) {
            // Actualizar empresa e insertar solicitud
            return $this->actualizarEmpresaInsertarSolicitud($form_data);
        } else {
            // Insertar empresa e insertar solicitud
            return $this->insertarEmpresaInsertarSolicitud($form_data);
        }
    }

    public function insertarEmpresaInsertarSolicitud($form_data)
    {
        global $wpdb;
        $tabla_empresas = $wpdb->prefix . 'empresa';
        $tabla_inspecciones = $wpdb->prefix . 'inspeccion';

        // Validar campos requeridos
        $campos_obligatorios = ['nit', 'razon_social', 'direccion', 'barrio', 'representante_legal', 'email', 'nombre_encargado', 'telefono_encargado'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($form_data[$campo])) {
                return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
            }
        }

        // Verificar si ya existe una empresa con el mismo NIT
        $existe = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla_empresas WHERE nit = %s", $form_data['nit']));
        if ($existe > 0) {
            return $this->armarRespuesta("Ya existe una empresa registrada con el NIT: {$form_data['nit']}", null, false);
        }

        // Insertar datos en la tabla empresa
        $dataEmpresa = [
            'nit' => $form_data['nit'],
            'razon_social' => $form_data['razon_social'],
            'direccion' => $form_data['direccion'],
            'barrio' => $form_data['barrio'],
            'representante_legal' => $form_data['representante_legal'],
            'email' => $form_data['email'],
        ];
        $result = $wpdb->insert($tabla_empresas, $dataEmpresa);
        if ($result === false) {
            error_log("Bomberos Plugin: Error al insertar empresa, NIT: {$form_data['nit']}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al registrar la empresa: ' . esc_html($wpdb->last_error), null, false);
        }
        $id_empresa = $wpdb->insert_id;

        // Insertar datos en la tabla inspección
        $dataInspeccion = [
            'id_empresa' => $id_empresa,
            'nombre_encargado' => $form_data['nombre_encargado'],
            'telefono_encargado' => $form_data['telefono_encargado'],
        ];
        $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
        if ($result === false) {
            error_log("Bomberos Plugin: Error al insertar inspección para empresa ID: {$id_empresa}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al registrar la inspección: ' . esc_html($wpdb->last_error), null, false);
        }
        $id_inspeccion = $wpdb->insert_id;

        // Consultar los datos registrados para confirmación
        $strSql = $wpdb->prepare("
            SELECT 
                e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                e.representante_legal, e.email,
                i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
            FROM $tabla_empresas AS e
            INNER JOIN $tabla_inspecciones AS i 
                ON e.id_empresa = i.id_empresa
            WHERE e.id_empresa = %d AND i.id_inspeccion = %d
        ", $id_empresa, $id_inspeccion);
        $resultado = $wpdb->get_row($strSql, ARRAY_A);

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Empresa y solicitud registradas con éxito', $html);
    }

    public function actualizarEmpresaInsertarSolicitud($form_data)
    {
        global $wpdb;
        $tabla_empresas = $wpdb->prefix . 'empresa';
        $tabla_inspecciones = $wpdb->prefix . 'inspeccion';

        // Validar campos requeridos
        $campos_obligatorios = ['id_empresa', 'representante_legal', 'email', 'nombre_encargado', 'telefono_encargado'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($form_data[$campo])) {
                return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
            }
        }

        $id_empresa = (int)$form_data['id_empresa'];
        $dataEmpresa = [
            'representante_legal' => $form_data['representante_legal'],
            'email' => $form_data['email'],
        ];
        $data_where = ['id_empresa' => $id_empresa];

        // Actualizar datos en la tabla empresa
        $result = $wpdb->update($tabla_empresas, $dataEmpresa, $data_where);
        if ($result === false) {
            error_log("Bomberos Plugin: Error al actualizar empresa ID: {$id_empresa}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al actualizar la empresa: ' . esc_html($wpdb->last_error), null, false);
        }

        // Insertar datos en la tabla inspección
        $dataInspeccion = [
            'id_empresa' => $id_empresa,
            'nombre_encargado' => $form_data['nombre_encargado'],
            'telefono_encargado' => $form_data['telefono_encargado'],
        ];
        $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
        if ($result === false) {
            error_log("Bomberos Plugin: Error al insertar inspección para empresa ID: {$id_empresa}, Error: {$wpdb->last_error}");
            return $this->armarRespuesta('Error al registrar la inspección: ' . esc_html($wpdb->last_error), null, false);
        }
        $id_inspeccion = $wpdb->insert_id;

        // Consultar los datos registrados para confirmación
        $strSql = $wpdb->prepare("
            SELECT 
                e.id_empresa, e.nit, e.razon_social, e.direccion, e.barrio, 
                e.representante_legal, e.email,
                i.id_inspeccion, i.fecha_registro, i.fecha_programada, 
                i.fecha_expedicion, i.estado, i.nombre_encargado, i.telefono_encargado
            FROM $tabla_empresas AS e
            INNER JOIN $tabla_inspecciones AS i 
                ON e.id_empresa = i.id_empresa
            WHERE e.id_empresa = %d AND i.id_inspeccion = %d
        ", $id_empresa, $id_inspeccion);
        $resultado = $wpdb->get_row($strSql, ARRAY_A);

        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Solicitud registrada con éxito', $html);
    }
}