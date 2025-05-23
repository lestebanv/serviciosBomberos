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
                return $this->registrarSolicitudInspeccion($request);
            default:
                return $this->armarRespuesta('Funcionalidad no encontrada '.$funcionalidad);
        }
    }
    public function enviarFrmEmpresa($request) {
            global $wpdb;
            $tabla_empresas = $wpdb->prefix . 'empresa';
            parse_str($request['form_data'], $form_data);
            $nit=$form_data['nit'];
            $empresa = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_empresas WHERE nit = %s", $nit), ARRAY_A );
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
    public function registrarSolicitudInspeccion($request) {
        global $wpdb;
        parse_str($request['form_data'], $form_data);
        if(isset($form_data['id_empresa'])){
            // actualizar empresa e insertar en inspeccion
            return $this->actualizarEmpresaInsertarSolicitud($form_data);
        }else{
            // insertar empresa e insertar en inspeccion
            return $this->insertarEmpresaInsertarSolicitud($form_data);
        };
    }
    public function insertarEmpresaInsertarSolicitud($form_data){
        global $wpdb;
        $tabla_empresas = $wpdb->prefix.'empresa';
        $tabla_inspecciones = $wpdb->prefix.'inspeccion';
        $this->enviarLog("antes de insertar".$nit);
        $dataEmpresa = array(
            'nit'         => $form_data['nit'],
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
        

        // sql select con where de id_empresa e id_inspeccion para garantizar que se hizo el registro
        $strSql= $wpdb->prepare("
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
        $resultado=$wpdb->get_row($strSql,ARRAY_A);
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Empresa registrada con éxito', $html);
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Empresa registrada con éxito', $html);

    }
    
    public function actualizarEmpresaInsertarSolicitud($form_data){
        global $wpdb;
        $tabla_empresas = $wpdb->prefix.'empresa';
        $tabla_inspecciones = $wpdb->prefix.'inspeccion';
        $id_empresa=$form_data['id_empresa'];
        $dataEmpresa = array(
            'representante_legal'       => sanitize_text_field($form_data['representante_legal']),
            'email'          => sanitize_email($form_data['email']),
        );
        $data_where=array(
             'id_empresa'=>$id_empresa
        );
        //Insertar datos en la tabla empresa
        $result = $wpdb->update($tabla_empresas, $dataEmpresa,$data_where);
       

       $dataInspeccion = array(
            'id_empresa'         => $id_empresa,
            'nombre_encargado'       => sanitize_text_field($form_data['nombre_encargado']),
            'telefono_encargado'       => sanitize_text_field($form_data['telefono_encargado']),
        );
        // Insertar datos en la tabla inspeccion
        $result = $wpdb->insert($tabla_inspecciones, $dataInspeccion);
        $id_inspeccion = $wpdb->insert_id;
      
        // sql select con where de id_empresa e id_inspeccion 

        $strSql= $wpdb->prepare("
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
        $resultado=$wpdb->get_row($strSql,ARRAY_A);
        ob_start();
        include plugin_dir_path(__FILE__) . 'vistas/confirmarRegistro.php';
        $html = ob_get_clean();
        return $this->armarRespuesta('Empresa registrada con éxito', $html);
    }
};
?>