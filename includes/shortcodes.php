<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Encolar scripts y estilos para el frontend (módulo público)
add_action('wp_enqueue_scripts', 'bomberos_enqueue_scripts_publico');
function bomberos_enqueue_scripts_publico() {
    global $post;
    if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'bomberos_solicitud_inspeccion')) {
        return;
    }
    $script_path = BOMBEROS_PLUGIN_DIR . 'modulos/publico/vistas/js/manejadorEventos.js';
    $script_url = BOMBEROS_PLUGIN_URL . 'modulos/publico/vistas/js/manejadorEventos.js';
    if (file_exists($script_path)) {
        wp_enqueue_script('bomberos-frontend-scripts', $script_url, ['jquery'], time(), true);
        wp_localize_script('bomberos-frontend-scripts', 'bomberosPublicoAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bomberos_publico_nonce')
        ]);
    }
}

// Manejar peticiones AJAX para el módulo público
add_action('wp_ajax_BomberosPluginPublico', 'bomberos_manejar_ajax_publico');
add_action('wp_ajax_nopriv_BomberosPluginPublico', 'bomberos_manejar_ajax_publico');
function bomberos_manejar_ajax_publico() {
    check_ajax_referer('bomberos_publico_nonce', 'nonce');
    
    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    if (!file_exists($controlador_file)) {
        error_log("Bomberos Plugin: Controlador público no encontrado - {$controlador_file}");
        wp_send_json_error(['mensaje' => 'Controlador no encontrado']);
    }
    
    // Sanitize $_POST data
    $sanitized_post = bomberos_sanitize_input($_POST, [
        // Define specific rules for public module, e.g.:
        // 'user_email' => 'email',
        // 'form_id' => 'int',
        // 'message' => 'textarea',
    ]);
    
    require_once $controlador_file;
    $controlador = new ControladorBomberosPublico();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        error_log("Bomberos Plugin: Método ejecutarFuncionalidad no encontrado en ControladorBomberosPublico");
        wp_send_json_error(['mensaje' => 'Funcionalidad no encontrada']);
    }
    
    $respuesta = $controlador->ejecutarFuncionalidad($sanitized_post);
    wp_send_json_success($respuesta);
}

// Registrar shortcode para el módulo público
add_shortcode('bomberos_solicitud_inspeccion', 'bomberos_solicitud_inspeccion_shortcode');
function bomberos_solicitud_inspeccion_shortcode($atts) {
    $atts = shortcode_atts(['funcionalidad' => 'mostrar_formulario'], $atts, 'bomberos_solicitud_inspeccion');
    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    if (!file_exists($controlador_file)) {
        error_log("Bomberos Plugin: Controlador público no encontrado para shortcode - {$controlador_file}");
        return '<p>Error: Controlador del módulo público no encontrado.</p>';
    }
    require_once $controlador_file;
    $class_name = 'ControladorBomberosPublico';
    if (!class_exists($class_name)) {
        error_log("Bomberos Plugin: Clase ControladorBomberosPublico no encontrada");
        return '<p>Error: Clase ControladorBomberosPublico no encontrada.</p>';
    }
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        error_log("Bomberos Plugin: Método ejecutarFuncionalidad no encontrado en ControladorBomberosPublico");
        return '<p>Error: Método ejecutarFuncionalidad no encontrado.</p>';
    }
    $resultado = $controlador->ejecutarFuncionalidad(['funcionalidad' => $atts['funcionalidad']]);
    if (isset($resultado['html']) && is_string($resultado['html'])) {
        return $resultado['html'];
    }
    error_log("Bomberos Plugin: Error al renderizar el contenido del shortcode");
    return '<p>Error: No se pudo renderizar el contenido del shortcode.</p>';
}