<?php
class ClaseControladorBaseBomberos {
    
    public function enviarLog($mensaje,$arreglo=[],$obj=null) {
        error_log($mensaje);
        error_log(print_r($arreglo, true));
        error_log(var_export($obj, true));
    }
    public function armarRespuesta($mensaje,$html=""){
        return [
                'mensaje' => $mensaje,
                'html' => $html
               ];
    }
}

?>