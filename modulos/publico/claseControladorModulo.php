<?php
class ControladorBomberosPublico extends ClaseControladorBaseBomberos {  

    public function ejecutarFuncionalidad($request) {
        $funcionalidad=$request['funcionalidad'];
        switch ($funcionalidad){
            case 'mostrar_formulario':
                  ob_start();
                  include plugin_dir_path(__FILE__).'vistas/frmBuscarEmpresa.php';
                  $html=ob_get_clean();
                  return $this->armarRespuesta('',$html);
            case 'buscar_empresa':
                return $this->enviarFrmEmpresa($request);
            case 'registrar_empresa_solicitud':
                return $this->registrarSolicitud($request);
            default:
                return $this->armarRespuesta('Funcionalidad no encontrada '.$funcionalidad);
        }
    }
    public function enviarFrmEmpresa($request) {
            global $wpdb;
            $tabla_empresas = $wpdb->prefix . 'empresa';
            parse_str($request['form_data'], $form_data);
            $nit=$form_data['nit'];
            $empresa = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $tabla_empresas WHERE nit = %s", $nit), ARRAY_A );
            if ($empresa) {
                ob_start();
                include plugin_dir_path(__FILE__) . 'vistas/frmRegistrarSoloSolicitud.php';
                $html = ob_get_clean();
                return $this->armarRespuesta('formulario completo enviado', $html);// Empresa ya registrada
            } else {
                ob_start();
                include plugin_dir_path(__FILE__) . 'vistas/frmRegistrarEmpresaSolicitud.php';
                $html = ob_get_clean();
                return $this->armarRespuesta('formulario completo enviado', $html);// Empresa no encontrada
            }
            
    }
    public function registrarSolicitudEmpresa($request) {
        global $wpdb;
        parse_str($request['form_data'], $form_data);
        $tabla_empresas = $wpdb->prefix.'empresa';
        $tabla_inspecciones = $wpdb->prefix.'inspeccion';
        $nit=form_data['nit'];
        $dataEmpresa = array(
            'nit'         => sanitize_text_field($form_data['nit']),
            'razon_social'       => sanitize_text_field($form_data['razon_social']),
            'direccion'       => sanitize_text_field($form_data['direccion']),
            'barrio'       => sanitize_text_field($form_data['barrio']),
            'representante_legal'       => sanitize_text_field($form_data['representante_legal']),
            'email'          => sanitize_email($form_data['email']),
        );
        //Insertar datos en la tabla empresa
        $result = $wpdb->insert($tabla_empresas, $dataEmpresa);
        $id_empresa = $wpdb->insert_id;

       $dataInspeccion = array(
            'id_empresa'         => $id_empresa,
            'nombre_encargado'       => sanitize_text_field($form_data['nombre_encargado']),
            'telefono_encargado'       => sanitize_text_field($form_data['telefono_encargado']),
        );
        // Insertar datos en la tabla inspeccion
        $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
        $id_inspeccion = $wpdb->insert_id;
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Empresa registrada con éxito', $html);
    }

   
}
?>