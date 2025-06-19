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

// Definir constantes
define('BOMBEROS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOMBEROS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MODULOS_BOMBEROS', ['empresas', 'inspecciones', 'cursos', 'inscripciones','pqr','bomberos']);

// Incluir archivos necesarios
require_once BOMBEROS_PLUGIN_DIR . 'includes/activacion.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/desactivacion.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';
require_once BOMBEROS_PLUGIN_DIR . 'includes/shortcodes.php'; 

// Registrar activación y desactivación
register_activation_hook(__FILE__, 'activar_plugin_bomberos');
register_deactivation_hook(__FILE__, 'desactivar_plugin_bomberos');


// Encolar scripts y estilos para la interfaz de administración
add_action('admin_enqueue_scripts', 'bomberos_enqueue_scripts');
function bomberos_enqueue_scripts($hook)
{
    if ($hook !== 'toplevel_page_bomberos-servicios') {
        return;
    }

    // Encolar script global con funciones reutilizables
    wp_enqueue_script(
        'bomberos-global-js',
        BOMBEROS_PLUGIN_URL . 'assets/js/bomberos-global.js',
        ['jquery'],
        time(),
        true
    );

    // Pasar datos globales (ajax_url, nonce)
    wp_localize_script('bomberos-global-js', 'bomberosAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bomberos_nonce')
    ]);

    // Encolar script de la página principal
    wp_enqueue_script(
        'bomberos-scripts',
        BOMBEROS_PLUGIN_URL . 'assets/js/bomberos-main.js',
        ['bomberos-global-js', 'jquery'],
        time(),
        true
    );

    // Encolar scripts de módulos
    foreach (MODULOS_BOMBEROS as $modulo) {
        $script_path = BOMBEROS_PLUGIN_DIR . "modulos/{$modulo}/manejadorEventos.js";
        $script_url = BOMBEROS_PLUGIN_URL . "modulos/{$modulo}/manejadorEventos.js";
        if (file_exists($script_path)) {
            wp_enqueue_script(
                "bomberos-{$modulo}-scripts",
                $script_url,
                ['bomberos-global-js', 'bomberos-scripts', 'jquery'],
                time(),
                true
            );
        }
    }

    // Encolar CSS para el indicador de carga
    wp_enqueue_style(
        'bomberos-admin-css',
        BOMBEROS_PLUGIN_URL . 'assets/css/bomberos-styles.css',
        [],
        time()
    );
}

// Agregar menú en el panel de administración
add_action('admin_menu', 'bomberos_registrar_menu');
function bomberos_registrar_menu()
{
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
function bomberos_mostrar_pagina_principal()
{
    ?>
    <div class="wrap">
        <div id="bomberos-titulo">
            <h1>Bomberos Servicios</h1>
        </div>
        <div id="bomberos-menu">
            <ul class="bomberos-tabs">
                <?php foreach (MODULOS_BOMBEROS as $modulo): ?>
                    <li class="bomberos-tab <?php echo $modulo === MODULOS_BOMBEROS[0] ? 'active' : ''; ?>"
                        data-modulo="<?php echo esc_attr($modulo); ?>">
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

// Manejar peticiones AJAX para la administración
add_action('wp_ajax_BomberosPlugin', 'bomberos_manejar_ajax');
function bomberos_manejar_ajax() {
    try {
        // Validar nonce
        check_ajax_referer('bomberos_nonce', 'nonce');

        // Validar módulo
        $modulo = isset($_POST['modulo']) ? sanitize_text_field($_POST['modulo']) : '';
        if (!in_array($modulo, MODULOS_BOMBEROS)) {
            throw new Exception("Módulo no válido: {$modulo}");
        }

        // Cargar controlador
        $controlador_file = BOMBEROS_PLUGIN_DIR . "modulos/{$modulo}/claseControladorModulo.php";
        if (!file_exists($controlador_file)) {
            throw new Exception("Controlador no encontrado: {$controlador_file}");
        }

        require_once $controlador_file;
        $class_name = 'Controlador' . ucfirst($modulo);
        if (!class_exists($class_name)) {
            throw new Exception("Clase del controlador no encontrada: {$class_name}");
        }

        $controlador = new $class_name();
        if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
            throw new Exception("Método ejecutarFuncionalidad no encontrado en {$class_name}");
        }

        // Ejecutar funcionalidad
        $respuesta = $controlador->ejecutarFuncionalidad($_POST);
        wp_send_json_success($respuesta);

    } catch (Exception $e) {
        // Registrar el error
        error_log("Bomberos Plugin: Error en AJAX - {$e->getMessage()}");
        // Devolver respuesta de error
        wp_send_json_error([
            'mensaje' => $e->getMessage(),
            'html' => ''
        ]);
    }
}


add_action('phpmailer_init', 'configurarPHPMailer');
function configurarPHPMailer($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Username   = 'luis.alberto.esteban.villamizar@gmail.com';
    $phpmailer->Password   = 'avms zwzy ualx dgf';
    $phpmailer->From       = 'luis.alberto.esteban.villamizar@gmail.com';
    $phpmailer->FromName   = 'Luis';

    // 🔍 Depuración PHPMailer
    $phpmailer->SMTPDebug = 2; // 1 = errores y mensajes; 2 = todo
    $phpmailer->Debugoutput = function($str, $level) {
        error_log("PHPMailer [nivel $level]: $str");
    };
}
