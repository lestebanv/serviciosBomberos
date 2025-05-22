<?php
/*
Plugin Name: Bomberos Servicios
Description: Plugin modular para la gestión de servicios de bomberos.
Version: 1.0
Author: Tu Nombre
*/

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constante MODULOS (sin publico)
define('MODULOS_BOMBEROS', ['empresas', 'inspecciones', 'cursos', 'inscripciones']);

// Incluir archivos necesarios
require_once plugin_dir_path(__FILE__) . 'includes/activacion.php';
require_once plugin_dir_path(__FILE__) . 'includes/desactivacion.php';
require_once plugin_dir_path(__FILE__) . 'includes/utilidades.php';

// Registrar activación y desactivación
register_activation_hook(__FILE__, 'crear_tablas_plugin_bomberos');
register_deactivation_hook(__FILE__, 'limpiar_plugin_bomberos');

// Encolar scripts y estilos para la interfaz de administración
add_action('admin_enqueue_scripts', 'bomberos_enqueue_scripts');
function bomberos_enqueue_scripts($hook) {
    if ($hook !== 'toplevel_page_bomberos-servicios') {
        return;
    }
    wp_enqueue_style('bomberos-styles', plugin_dir_url(__FILE__) . 'assets/css/bomberos-styles.css', [], '1.0');
    wp_enqueue_script('bomberos-scripts', plugin_dir_url(__FILE__) . 'assets/js/bomberos-scripts.js', ['jquery'], '1.0', true);
    wp_localize_script('bomberos-scripts', 'bomberosAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bomberos_nonce')
    ]);
    foreach (MODULOS_BOMBEROS as $modulo) {
        $script_path = plugin_dir_path(__FILE__) . "modulos/{$modulo}/vistas/js/manejadorEventos.js";
        $script_url = plugin_dir_url(__FILE__) . "modulos/{$modulo}/vistas/js/manejadorEventos.js";
        if (file_exists($script_path)) {
            wp_enqueue_script("bomberos-{$modulo}-scripts", $script_url, ['jquery', 'bomberos-scripts'], time(), true);
        }
    }
}

// Encolar scripts y estilos para el frontend (módulo público)
add_action('wp_enqueue_scripts', 'bomberos_enqueue_scripts_publico');
function bomberos_enqueue_scripts_publico() {
    global $post;
    if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'bomberos_solicitud_inspeccion')) {
        return;
    }
    $script_path = plugin_dir_path(__FILE__) . 'modulos/publico/vistas/js/manejadorEventos.js';
    $script_url = plugin_dir_url(__FILE__) . 'modulos/publico/vistas/js/manejadorEventos.js';
    if (file_exists($script_path)) {
        wp_enqueue_script('bomberos-frontend-scripts', $script_url, ['jquery'], time(), true);
    }
    wp_localize_script('bomberos-frontend-scripts', 'bomberosPublicoAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bomberos_publico_nonce')
    ]);
    
}

// Agregar menú en el panel de administración
add_action('admin_menu', 'bomberos_registrar_menu');
function bomberos_registrar_menu() {
    add_menu_page(
        'Bomberos Servicios',
        'Bomberos Servicios',
        'manage_options',
        'bomberos-servicios',
        'bomberos_mostrar_pagina_principal',
        'dashicons-shield',
        6
    );
}

// Mostrar la página principal
function bomberos_mostrar_pagina_principal() {
    ?>
    <div class="wrap">
        <div id="bomberos-titulo"><h1>Bomberos Servicios</h1></div>
        <div id="bomberos-menu">
            <ul class="bomberos-tabs">
                <?php foreach (MODULOS_BOMBEROS as $modulo): ?>
                    <li class="bomberos-tab <?php echo $modulo === MODULOS_BOMBEROS[0] ? 'active' : ''; ?>" data-modulo="<?php echo esc_attr($modulo); ?>">
                        <?php echo esc_html(ucfirst($modulo)); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="bomberos-mensaje"></div>
        <div id="bomberos-cuerpo"></div>
    </div>
    <?php
}

// Manejar peticiones AJAX para la administración
add_action('wp_ajax_BomberosPlugin', 'bomberos_manejar_ajax');
function bomberos_manejar_ajax() {
    check_ajax_referer('bomberos_nonce', 'nonce');
    $modulo = isset($_POST['modulo']) ? sanitize_text_field($_POST['modulo']) : '';
    if (!in_array($modulo, MODULOS_BOMBEROS)) {
        wp_send_json_error(['mensaje' => 'Módulo no válido']);
    }
    $controlador_file = plugin_dir_path(__FILE__) . "modulos/{$modulo}/claseControladorModulo.php";
    if (!file_exists($controlador_file)) {
        wp_send_json_error(['mensaje' => 'Controlador no encontrado']);
    }
    require_once $controlador_file;
    $class_name = 'Controlador' . ucfirst($modulo);
    if (!class_exists($class_name)) {
        wp_send_json_error(['mensaje' => 'Clase del controlador no encontrada']);
    }
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        wp_send_json_error(['mensaje' => 'Funcionalidad no encontrada']);
    }
    $respuesta = $controlador->ejecutarFuncionalidad($_POST);
    wp_send_json_success($respuesta);
}

// Manejar peticiones AJAX para el módulo público
add_action('wp_ajax_BomberosPluginPublico', 'bomberosManejarAjaxPublico');
add_action('wp_ajax_nopriv_BomberosPluginPublico', 'bomberosManejarAjaxPublico');
function bomberosManejarAjaxPublico() {
    check_ajax_referer('bomberos_publico_nonce', 'nonce');
    $controlador_file = plugin_dir_path(__FILE__) . "modulos/publico/claseControladorModulo.php";
    if (!file_exists($controlador_file)) {
        wp_send_json_error(['mensaje' => 'Controlador no encontrado']);
    }
    require_once $controlador_file;
    $controlador = new ControladorBomberosPublico();
    $respuesta = $controlador->ejecutarFuncionalidad($_POST);
    wp_send_json_success($respuesta);
}

// Registrar shortcode para el módulo público
add_shortcode('bomberos_solicitud_inspeccion', 'bomberosSolicitudInspeccionShortcode');
function bomberosSolicitudInspeccionShortcode($atts) {
    $atts = shortcode_atts(['funcionalidad' => 'mostrar_formulario'], $atts, 'bomberos_solicitud_inspeccion');
    $controlador_file = plugin_dir_path(__FILE__) . 'modulos/publico/claseControladorModulo.php';
    if (!file_exists($controlador_file)) {
        return '<p>Error: Controlador del módulo público no encontrado.</p>';
    }
    require_once $controlador_file;
    $class_name = 'ControladorBomberosPublico';
    if (!class_exists($class_name)) {
        return '<p>Error: Clase ControladorBomberosPublico no encontrada.</p>';
    }
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        return '<p>Error: Método ejecutarFuncionalidad no encontrado.</p>';
    }
    $resultado = $controlador->ejecutarFuncionalidad(['funcionalidad' => $atts['funcionalidad']]);
    if (isset($resultado['html']) && is_string($resultado['html'])) {
        return $resultado['html'];
    }
    return '<p>Error: No se pudo renderizar el contenido del shortcode.</p>';
}
?>