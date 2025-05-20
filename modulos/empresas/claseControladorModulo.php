<?php
class ControladorEmpresas extends ClaseControladorBaseBomberos {  

    public function ejecutarFuncionalidad($request) {
        $modulo=$request['modulo'];
        $funcionalidad=$request['funcionalidad'];
        $form_data = $request['form_data'];
        switch ($funcionalidad){
            case 'inicial':
                return $this->listarEmpresas($request);
            case 'editar':
                return $this->formularioEdicion($request);
            default:
                return $this->armarRespuesta('Funcionalidad '.$funcionalidad. ' no encontrada en el modulo '.$modulo);
        }
    }
    public function listarEmpresas($request) {
            global $wpdb;
            // Obtener lista de empresas
            $tabla_empresas = $wpdb->prefix . 'empresa';
            $empresas = $wpdb->get_results("SELECT * FROM $tabla_empresas", ARRAY_A);
            // Generar HTML para la lista
            ob_start();
            include plugin_dir_path(__FILE__) . 'vistas/listaEmpresas.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de empresas cargada con éxito', $html);
    }
    public function registrar_empresa($form_data = []) {
        global $wpdb;

        // Sanitizar y validar datos
        $nit = isset($form_data['nit']) ? sanitize_text_field($form_data['nit']) : '';
        $razon_social = isset($form_data['razon_social']) ? sanitize_text_field($form_data['razon_social']) : '';
        $direccion = isset($form_data['direccion']) ? sanitize_text_field($form_data['direccion']) : '';
        $barrio = isset($form_data['barrio']) ? sanitize_text_field($form_data['barrio']) : '';
        $representante_legal = isset($form_data['representante_legal']) ? sanitize_text_field($form_data['representante_legal']) : '';
        $email = isset($form_data['email']) ? sanitize_email($form_data['email']) : '';

        if (empty($nit) || empty($razon_social) || empty($direccion) || empty($barrio) || empty($representante_legal) || empty($email)) {
            return $this->armar_respuesta('Todos los campos son obligatorios');
        }

        // Insertar en la base de datos
        $tabla_empresas = $wpdb->prefix . 'empresa';
        $resultado = $wpdb->insert(
            $tabla_empresas,
            [
                'nit' => $nit,
                'razon_social' => $razon_social,
                'direccion' => $direccion,
                'barrio' => $barrio,
                'representante_legal' => $representante_legal,
                'email' => $email
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($resultado === false) {
            $this->enviar_log('Error al registrar empresa', $form_data);
            return $this->armar_respuesta('Error al registrar la empresa');
        }

        // Cargar la plantilla inicial como respuesta
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/plantilla01.php';
        $html = ob_get_clean();

        $this->enviar_log('Empresa registrada con éxito', $form_data);
        return $this->armar_respuesta('Empresa registrada con éxito', $html);
    }

   
}
?>