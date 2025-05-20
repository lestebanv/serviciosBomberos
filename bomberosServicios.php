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

// Definir constante MODULOS
define('MODULOS_BOMBEROS', [ 'empresas','inspecciones','cursos','inscripciones']);

// Incluir archivos necesarios
require_once plugin_dir_path(__FILE__) . 'includes/activacion.php';
require_once plugin_dir_path(__FILE__) . 'includes/desactivacion.php';
require_once plugin_dir_path(__FILE__) . 'includes/utilidades.php';

// Registrar activación y desactivación
register_activation_hook(__FILE__, 'crear_tablas_plugin_bomberos');
register_deactivation_hook(__FILE__, 'limpiar_plugin_bomberos');

// Encolar scripts y estilos
add_action('admin_enqueue_scripts', 'bomberos_enqueue_scripts');
function bomberos_enqueue_scripts($hook) {
    // Solo en la página del plugin
    if ($hook !== 'toplevel_page_bomberos-servicios') {
        return;
    }

    // Encolar CSS
    wp_enqueue_style(
        'bomberos-styles',
        plugin_dir_url(__FILE__) . 'assets/css/bomberos-styles.css',
        [],
        '1.0'
    );

    // Encolar JS principal
    wp_enqueue_script(
        'bomberos-scripts',
        plugin_dir_url(__FILE__) . 'assets/js/bomberos-scripts.js',
        ['jquery'],
        '1.0',
        true
    );

    // Localizar script principal para AJAX (único wp_localize_script)
    wp_localize_script(
        'bomberos-scripts',
        'bomberosAjax',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bomberos_nonce')
        ]
    );

    // Encolar JS de cada módulo usando un ciclo con validación
    foreach (MODULOS_BOMBEROS as $modulo) {
        $script_path = plugin_dir_path(__FILE__) . "modulos/{$modulo}/vistas/js/manejadorEventos.js";
        $script_url = plugin_dir_url(__FILE__) . "modulos/{$modulo}/vistas/js/manejadorEventos.js";
        // Validar la existencia del archivo
        if (file_exists($script_path)) {
            wp_enqueue_script(
                "bomberos-{$modulo}-scripts",
                $script_url,
                ['jquery', 'bomberos-scripts'],
                time(),
                true
            );
        }
    }
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
        <div id="bomberos-titulo">
            <h1>Bomberos Servicios</h1>
        </div>
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
        <div id="bomberos-cuerpo">
            <!-- Contenido inicial cargado vía AJAX -->
        </div>
    </div>
    <?php
}

// Manejar peticiones AJAX
add_action('wp_ajax_BomberosPlugin', 'bomberos_manejar_ajax');
function bomberos_manejar_ajax() {
    // Verificar nonce
    check_ajax_referer('bomberos_nonce', 'nonce');

    // Obtener datos de la petición respecto al modulo solicitrado
    $modulo = isset($_POST['modulo']) ? sanitize_text_field($_POST['modulo']) : '';
    
    // Validar módulo
    if (!in_array($modulo, MODULOS_BOMBEROS)) {
        wp_send_json_error(['mensaje' => 'Módulo no válido']);
    }

    // Incluir el controlador del módulo
    $controlador_file = plugin_dir_path(__FILE__) . "modulos/{$modulo}/claseControladorModulo.php";
    if (!file_exists($controlador_file)) {
        wp_send_json_error(['mensaje' => 'Controlador no encontrado']);
    }

    require_once $controlador_file;
    $class_name = 'Controlador' . ucfirst($modulo);
    if (!class_exists($class_name)) {
        wp_send_json_error(['mensaje' => 'Clase del controlador no encontrada']);
    }

    // Instanciar controlador y ejecutar funcionalidad
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        wp_send_json_error(['mensaje' => 'Funcionalidad no encontrada']);
    }

    // Ejecutar la funcionalidad y obtener respuesta
    $respuesta = $controlador->ejecutarFuncionalidad($_POST);
    wp_send_json_success($respuesta);
}
?>