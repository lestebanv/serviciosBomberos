<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Encolar scripts y estilos para el frontend (módulo público)
add_action('wp_enqueue_scripts', 'bomberos_enqueue_scripts_publico');
function bomberos_enqueue_scripts_publico() {
    global $post;
    if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'bomberosShortCode')) {
        return;
    }
    require_once BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    ControladorBomberosPublico::encolarManejadoresEventos();
}

add_shortcode('bomberosShortCode', 'bomberos_manejar_shortcode');
function bomberos_manejar_shortcode($atts) {
    // valores por defecto de los parametros recibidos los nombres de los atributos los convierte a minusculas
    $atts = shortcode_atts([
        'nombreshortcode' => '',
    ], $atts);
    // Limpiar el valor recibido
    $nombreShortCode = esc_html($atts['nombreshortcode']);

    // Retornar contenido que se mostrará en la página
   
    require_once BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    $controlador = new ControladorBomberosPublico();
    $respuesta = $controlador->ejecutarShortCode($nombreShortCode);
    ob_start();
    ?>
    <div class="bomberos-contenedor-shortcode">
        <div id='bomberos-shortcode-mensaje' style="height: 20px; border: 1px solid #ccc">
            <?php
               echo $respuesta['mensaje'];
            ?>
        </div>
        <div id='bomberos-shortcode-cuerpo'>
         <?php
            echo $respuesta['html'];
         ?>
        </div>
    </div>
    <?php
    return ob_get_clean(); 
}
// Manejar peticiones AJAX para el módulo público
add_action('wp_ajax_BomberosPluginPublico', 'bomberos_manejar_ajax_publico');
add_action('wp_ajax_nopriv_BomberosPluginPublico', 'bomberos_manejar_ajax_publico');
function bomberos_manejar_ajax_publico() {
    try{
        
        require_once BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
        $controlador = new ControladorBomberosPublico();
        $respuesta = $controlador->ejecutarFuncionalidad($_POST);
        wp_send_json_success($respuesta);
    } catch (Exception $e) {
        // Registrar el error
        error_log("Bomberos Plugin Publico: Error en AJAX - {$e->getMessage()}");
        // Devolver respuesta de error
        wp_send_json_error([
            'mensaje' => $e->getMessage(),
            'html' => ''
        ]);
    }
    
}