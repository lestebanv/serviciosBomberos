<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosPublico extends ClaseControladorBaseBomberos {
    // Registre en esta lista el nombre del submódulo
    public static $subModulos = ['registroInscripciones', 'registroPqr', 'solicitudInspecciones'];

    // Método estático para el encolado de todos los scripts manejadores de eventos
    public static function encolarManejadoresEventos() {
        $script_path = plugin_dir_path(__FILE__) . 'shortCodes-main.js';
        $script_url = BOMBEROS_PLUGIN_URL . '/modulos/publico/shortCodes-main.js';

        if (file_exists($script_path)) {
            wp_enqueue_script('bomberos-frontend-scripts', $script_url, ['jquery'], time(), true);
            wp_localize_script('bomberos-frontend-scripts', 'bomberosPublicoAjax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bomberos_publico_nonce')
            ]);
        }

        // Encolar scripts de submódulos
        foreach (self::$subModulos as $modulo) {
            $script_path = plugin_dir_path(__FILE__) . "{$modulo}/manejadorEventos.js";
            $script_url = plugin_dir_url(__FILE__) . "{$modulo}/manejadorEventos.js";
            if (file_exists($script_path)) {
                wp_enqueue_script(
                    "bomberos-shortcode-{$modulo}-scripts",
                    $script_url,
                    ['bomberos-frontend-scripts', 'jquery'],
                    time(),
                    true
                );
            }
        }
    }

    public function ejecutarShortCode($nombreShortCode) {
        try {
            if (!in_array($nombreShortCode, self::$subModulos)) {
                return $this->armarRespuesta("Short Code no registrado: {$nombreShortCode}");
            }

            $controlador_file = plugin_dir_path(__FILE__) . $nombreShortCode . "/claseControladorModulo.php";
            if (!file_exists($controlador_file)) {
                return $this->armarRespuesta("Controlador no encontrado: {$controlador_file}");
            }

            require_once $controlador_file;

            $class_name = 'ControladorBomberosShortCode' . ucfirst($nombreShortCode);
            if (!class_exists($class_name)) {
                return $this->armarRespuesta("Clase del controlador no encontrada: {$class_name}");
            }

            $controlador = new $class_name();
            if (!method_exists($controlador, 'ejecutarShortCode')) {
                return $this->armarRespuesta("Método ejecutarShortCode no encontrado en {$class_name}");
            }

            $respuesta = $controlador->ejecutarShortCode($nombreShortCode);
            return $respuesta;
        } catch (Throwable $e) {
            return $this->armarRespuesta("Error al ejecutar ShortCode '{$nombreShortCode}': " . $e->getMessage());
        }
    }

    public function ejecutarFuncionalidad($request) {
        try {
            $nombreShortCode = isset($request['funcionalidad']) ? $request['funcionalidad'] : '';

            if (!in_array($nombreShortCode, self::$subModulos)) {
                return $this->armarRespuesta("Short Code no registrado: {$nombreShortCode}");
            }

            $controlador_file = plugin_dir_path(__FILE__) . $nombreShortCode . "/claseControladorModulo.php";
            if (!file_exists($controlador_file)) {
                return $this->armarRespuesta("Controlador no encontrado: {$controlador_file}");
            }

            require_once $controlador_file;

            $class_name = 'ControladorBomberosShortCode' . ucfirst($nombreShortCode);
            if (!class_exists($class_name)) {
                return $this->armarRespuesta("Clase del controlador no encontrada: {$class_name}");
            }

            $controlador = new $class_name();
            if (!method_exists($controlador, 'ejecutarFuncionalidad')) {
                return $this->armarRespuesta("Método ejecutarFuncionalidad no encontrado en {$class_name}");
            }

            $respuesta = $controlador->ejecutarFuncionalidad($request);
            return $respuesta;
        } catch (Throwable $e) {
            return $this->armarRespuesta("Error al ejecutar funcionalidad '{$nombreShortCode}': " . $e->getMessage());
        }
    }
}
