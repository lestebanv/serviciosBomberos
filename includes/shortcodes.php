<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Encolar scripts y estilos para el frontend (módulo público de inspección)
add_action('wp_enqueue_scripts', 'bomberos_enqueue_scripts_publico_inspeccion'); // Renombrado para claridad, pero podrías mantener el original si prefieres
function bomberos_enqueue_scripts_publico_inspeccion() { // Cambiado el nombre para evitar conflicto si decides renombrar el hook
    global $post;
    // Solo cargar si el shortcode de inspección está presente
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bomberos_solicitud_inspeccion')) {
        $script_path = BOMBEROS_PLUGIN_DIR . 'modulos/publico/vistas/js/manejadorEventos.js';
        $script_url = BOMBEROS_PLUGIN_URL . 'modulos/publico/vistas/js/manejadorEventos.js';
        if (file_exists($script_path)) {
            wp_enqueue_script('bomberos-frontend-scripts-inspeccion', $script_url, ['jquery'], time(), true); // Nombre de handle único
            wp_localize_script('bomberos-frontend-scripts-inspeccion', 'bomberosPublicoAjaxInspeccion', [ // Objeto AJAX único
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bomberos_publico_inspeccion_nonce') // Nonce específico
            ]);
        }
    }
}

// Manejar peticiones AJAX para el módulo público de inspección
add_action('wp_ajax_BomberosPluginPublicoInspeccion', 'bomberos_manejar_ajax_publico_inspeccion_accion'); // Nueva acción AJAX específica
add_action('wp_ajax_nopriv_BomberosPluginPublicoInspeccion', 'bomberos_manejar_ajax_publico_inspeccion_accion'); // Nueva acción AJAX específica
function bomberos_manejar_ajax_publico_inspeccion_accion() {
    check_ajax_referer('bomberos_publico_inspeccion_nonce', 'nonce'); // Verificar nonce específico
    
    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    if (!file_exists($controlador_file)) {
        error_log("Bomberos Plugin: Controlador público (inspección) no encontrado - {$controlador_file}");
        wp_send_json_error(['mensaje' => 'Controlador no encontrado']);
        return;
    }
    
    $sanitized_post = bomberos_sanitize_input($_POST, [/* tus reglas para inspecciones */]);
    
    require_once $controlador_file;
    $controlador = new ControladorBomberosPublico();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        error_log("Bomberos Plugin: Método ejecutarFuncionalidad no encontrado en ControladorBomberosPublico (inspección)");
        wp_send_json_error(['mensaje' => 'Funcionalidad no encontrada']);
        return;
    }
    
    $respuesta = $controlador->ejecutarFuncionalidad($sanitized_post);
    wp_send_json_success($respuesta);
}

// Registrar shortcode para el módulo público de inspección
add_shortcode('bomberos_solicitud_inspeccion', 'bomberos_solicitud_inspeccion_shortcode_render'); // Renombrado para claridad
function bomberos_solicitud_inspeccion_shortcode_render($atts) { // Renombrado para claridad
    $atts = shortcode_atts(['funcionalidad' => 'mostrar_formulario'], $atts, 'bomberos_solicitud_inspeccion');
    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/publico/claseControladorModulo.php';
    if (!file_exists($controlador_file)) {
        error_log("Bomberos Plugin: Controlador público (inspección) no encontrado para shortcode - {$controlador_file}");
        return '<p>Error: Controlador del módulo público no encontrado.</p>';
    }
    require_once $controlador_file;
    $class_name = 'ControladorBomberosPublico';
    if (!class_exists($class_name)) {
        error_log("Bomberos Plugin: Clase ControladorBomberosPublico (inspección) no encontrada");
        return '<p>Error: Clase ControladorBomberosPublico no encontrada.</p>';
    }
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
        error_log("Bomberos Plugin: Método ejecutarFuncionalidad no encontrado en ControladorBomberosPublico (inspección)");
        return '<p>Error: Método ejecutarFuncionalidad no encontrado.</p>';
    }
    $resultado = $controlador->ejecutarFuncionalidad(['funcionalidad' => $atts['funcionalidad']]);
    if (isset($resultado['html']) && is_string($resultado['html'])) {
        return $resultado['html'];
    }
    error_log("Bomberos Plugin: Error al renderizar el contenido del shortcode de inspección");
    return '<p>Error: No se pudo renderizar el contenido del shortcode.</p>';
}



//  'bomberos_inscripcion_curso'   


// Encolar scripts para el frontend (módulo público de inscripción a cursos)
add_action('wp_enqueue_scripts', 'bomberos_enqueue_scripts_inscripciones_publico');
function bomberos_enqueue_scripts_inscripciones_publico() {
    global $post;
    // Solo cargar si el shortcode de inscripción está presente
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bomberos_inscripcion_curso')) {
        $script_path_inscripcion = BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/js/manejadorEventosInscripcionesPublico.js';
        $script_url_inscripcion = BOMBEROS_PLUGIN_URL . 'modulos/inscripciones/vistas/js/manejadorEventosInscripcionesPublico.js';
        if (file_exists($script_path_inscripcion)) {
            wp_enqueue_script('bomberos-inscripcion-curso-scripts', $script_url_inscripcion, ['jquery'], time(), true);
            // Localizar un objeto AJAX específico para este script si es necesario, o puedes reutilizar el nonce general.
            // Por simplicidad y seguridad, usaremos un nonce específico para esta acción.
            wp_localize_script('bomberos-inscripcion-curso-scripts', 'bomberosInscripcionAjax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bomberos_inscripcion_curso_nonce_action') // Nonce específico para inscripciones
            ]);
        }
    }
}

// Manejar peticiones AJAX para el módulo público de INSCRIPCIONES A CURSOS
add_action('wp_ajax_bomberos_procesar_inscripcion_publica', 'bomberos_manejar_ajax_inscripciones_publico_accion');
add_action('wp_ajax_nopriv_bomberos_procesar_inscripcion_publica', 'bomberos_manejar_ajax_inscripciones_publico_accion');
function bomberos_manejar_ajax_inscripciones_publico_accion() {
    // Verificar el nonce específico para la acción de inscripción
    check_ajax_referer('bomberos_inscripcion_curso_nonce_action', 'nonce_inscripcion'); // 'nonce_inscripcion' es el campo que el JS debe enviar
    
    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/claseControladorInscripcionesPublico.php';
    if (!file_exists($controlador_file)) {
        error_log("Bomberos Plugin: Controlador de Inscripciones no encontrado - {$controlador_file}");
        wp_send_json_error(['mensaje' => 'Controlador de inscripciones no disponible.'], 404);
        return;
    }
    
    require_once $controlador_file;
    $controlador_class_name = 'ControladorInscripcionesPublico';
    if (!class_exists($controlador_class_name)) {
        error_log("Bomberos Plugin: Clase '{$controlador_class_name}' no encontrada.");
        wp_send_json_error(['mensaje' => 'Clase de inscripciones no disponible.'], 500);
        return;
    }
    
    $controlador = new $controlador_class_name();
    $metodo_ejecutar = 'ejecutarFuncionalidadPublica';
    if (!method_exists($controlador, $metodo_ejecutar)) {
        error_log("Bomberos Plugin: Método '{$metodo_ejecutar}' no encontrado en '{$controlador_class_name}'.");
        wp_send_json_error(['mensaje' => 'Operación de inscripción no disponible.'], 500);
        return;
    }
    
    // El controlador de inscripciones espera 'form_data' serializado y parseará 'funcionalidad_publica' de ahí.
    // Y también el nonce específico del formulario si lo pones ahí.
    // $_POST se pasa directamente.
    $respuesta = $controlador->{$metodo_ejecutar}($_POST);

    if (is_array($respuesta) && isset($respuesta['success'])) {
        if ($respuesta['success']) {
            wp_send_json_success($respuesta['data'] ?? ['mensaje' => 'Inscripción procesada.']);
        } else {
            wp_send_json_error($respuesta['data'] ?? ['mensaje' => 'Error al procesar inscripción.']);
        }
    } else {
        wp_send_json_error(['mensaje' => 'Respuesta inesperada del servidor.']);
    }
}

// Registrar shortcode para inscripción a cursos
add_shortcode('bomberos_inscripcion_curso', 'bomberos_render_inscripcion_curso_shortcode_nuevo');
function bomberos_render_inscripcion_curso_shortcode_nuevo($atts) {
    $funcionalidad_inicial = 'mostrar_formulario_inscripcion'; 

    $controlador_file = BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/claseControladorInscripcionesPublico.php';
    if (!file_exists($controlador_file)) {
        return '<p>Error: Controlador del módulo de inscripción no encontrado.</p>';
    }
    require_once $controlador_file;
    $class_name = 'ControladorInscripcionesPublico';
    if (!class_exists($class_name)) {
        return '<p>Error: Clase ControladorInscripcionesPublico no encontrada.</p>';
    }
    $controlador = new $class_name();
    if (!method_exists($controlador, 'ejecutarFuncionalidadPublica')) {
        return '<p>Error: Método de ejecución no encontrado en controlador de inscripción.</p>';
    }
    
    $resultado = $controlador->ejecutarFuncionalidadPublica(['funcionalidad_publica' => $funcionalidad_inicial]);
    
    return $resultado['html'] ?? '<p>Error al renderizar el formulario de inscripción.</p>';
}
?>