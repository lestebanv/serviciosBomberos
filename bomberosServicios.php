<?php
/*
Plugin Name: Bomberos Servicios
Description: Plugin modular para la gestión de servicios de bomberos.
Version: 1.0
Author: Tu Nombre
*/

if (!defined('ABSPATH')) {
    exit;
}

define('BOMBEROS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOMBEROS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MODULOS_BOMBEROS', ['empresas', 'inspecciones', 'cursos', 'inscripciones','pqr','bomberos']);

require_once BOMBEROS_PLUGIN_DIR . 'includes/activacion.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/desactivacion.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/shortcodes.php'; 

register_activation_hook(__FILE__, 'activar_plugin_bomberos');
register_deactivation_hook(__FILE__, 'desactivar_plugin_bomberos');

// --- CORRECCIÓN SMTP ---
// Sacamos esto fuera de 'plugins_loaded' para asegurar que siempre se ejecute
add_action('phpmailer_init', 'configurarPHPMailer');

function configurarPHPMailer($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = 'tls';
    
    // TUS CREDENCIALES (Asegúrate que estén bien escritas)
    $phpmailer->Username   = 'luis.alberto.esteban.villamizar@gmail.com';
    $phpmailer->Password   = 'ntvk kbaq bfcp zerj'; // <--- VERIFICA ESTO
    
    // Forzamos el remitente para que coincida con la cuenta (Gmail es estricto con esto)
    $phpmailer->From       = 'luis.alberto.esteban.villamizar@gmail.com';
    $phpmailer->FromName   = 'Cuerpo de Bomberos';
    $phpmailer->Sender     = $phpmailer->From; // Importante para evitar que llegue a SPAM
}
// -----------------------

add_action('admin_enqueue_scripts', 'bomberos_enqueue_scripts');
function bomberos_enqueue_scripts($hook)
{
    if ($hook !== 'toplevel_page_bomberos-servicios') {
        return;
    }

    wp_enqueue_script('bomberos-global-js', BOMBEROS_PLUGIN_URL . 'assets/js/bomberos-global.js', ['jquery'], time(), true);
    wp_localize_script('bomberos-global-js', 'bomberosAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bomberos_nonce')
    ]);

    wp_enqueue_script('bomberos-scripts', BOMBEROS_PLUGIN_URL . 'assets/js/bomberos-main.js', ['bomberos-global-js', 'jquery'], time(), true);

    foreach (MODULOS_BOMBEROS as $modulo) {
        $script_path = BOMBEROS_PLUGIN_DIR . "modulos/{$modulo}/manejadorEventos.js";
        $script_url = BOMBEROS_PLUGIN_URL . "modulos/{$modulo}/manejadorEventos.js";
        if (file_exists($script_path)) {
            wp_enqueue_script("bomberos-{$modulo}-scripts", $script_url, ['bomberos-global-js', 'bomberos-scripts', 'jquery'], time(), true);
        }
    }
    wp_enqueue_style('bomberos-admin-css', BOMBEROS_PLUGIN_URL . 'assets/css/bomberos-styles.css', [], time());
}

function bomberos_mostrar_pagina_principal()
{
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
        <div id="bomberos-mensaje" style="height: 20px"></div>
        <hr>
        <div id="bomberos-cuerpo"></div>
    </div>
    <?php
}

add_action('wp_ajax_BomberosPlugin', 'bomberos_manejar_ajax');
function bomberos_manejar_ajax() {
    try {
        check_ajax_referer('bomberos_nonce', 'nonce');
        $modulo = isset($_POST['modulo']) ? sanitize_text_field($_POST['modulo']) : '';
        if (!in_array($modulo, MODULOS_BOMBEROS)) throw new Exception("Módulo no válido: {$modulo}");

        $controlador_file = BOMBEROS_PLUGIN_DIR . "modulos/{$modulo}/claseControladorModulo.php";
        if (!file_exists($controlador_file)) throw new Exception("Controlador no encontrado");

        require_once $controlador_file;
        $class_name = 'Controlador' . ucfirst($modulo);
        if (!class_exists($class_name)) throw new Exception("Clase no encontrada");

        $controlador = new $class_name();
        if (!method_exists($controlador, 'ejecutarFuncionalidad')) throw new Exception("Método no encontrado");

        $respuesta = $controlador->ejecutarFuncionalidad($_POST);
        wp_send_json_success($respuesta);

    } catch (Exception $e) {
        error_log("Bomberos Plugin: Error - {$e->getMessage()}");
        wp_send_json_error(['mensaje' => $e->getMessage(), 'html' => '']);
    }
}

add_action('admin_menu', 'bomberos_registrar_menu');
function bomberos_registrar_menu()
{
    add_menu_page('Bomberos Servicios', 'Bomberos Servicios', 'manage_options', 'bomberos-servicios', 'bomberos_mostrar_pagina_principal', 'dashicons-shield', 6);
    add_submenu_page('bomberos-servicios', 'PHP Info', 'PHP Info', 'manage_options', 'bomberos-phpinfo', 'bomberos_mostrar_phpinfo');
}

function bomberos_mostrar_phpinfo() {
    if (!current_user_can('manage_options')) wp_die('No tienes permisos.');
    echo '<div class="wrap"><h1>PHP Info</h1><div style="background:#fff; padding:10px;">';
    // Prueba rápida de correo visual
    echo '<h3>Estado de Correo</h3>';
    echo class_exists('PHPMailer') ? '<p style="color:green">PHPMailer está disponible.</p>' : '<p style="color:red">PHPMailer NO está cargado.</p>';
    echo '</div></div>';
}