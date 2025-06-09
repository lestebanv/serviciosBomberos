<?php
// includes/utilidades.php

if (!defined('ABSPATH')) {
    exit;
}



function barraNavegacion($tabla, $totalpaginas, $actualpagina, $alineacion = 'left')
{
    if ($totalpaginas > 1) {
        $style = '';
        if ($alineacion === 'right') {
            $style = 'float: right;';
        } elseif ($alineacion === 'center') {
            $style = 'margin: 0 auto; text-align: center; width: fit-content;';
        } else {
            $style = 'float: left;';
        }
        $style .= ' font-size: 14px; font-weight: bold; font-family: Arial, sans-serif;';
        $pagination_args = array(
            'base' => '#%#%',
            'format' => '',
            'total' => $totalpaginas,
            'current' => $actualpagina,
            'prev_text' => __('« Anterior'),
            'next_text' => __('Siguiente »'),
            'type' => 'array',
            'aria_current' => 'page',
        );

        $links = paginate_links($pagination_args);

        echo '<div class="tablenav"><div class="tablenav-pages" style="' . esc_attr($style) . '">';

        foreach ($links as $link) {
            if (preg_match('/\#(\d+)/', $link, $matches)) {
                $page_num = intval($matches[1]);
                echo '<a href="#" class="paginacion-'.$tabla.'" data-actualpagina="' . esc_attr($page_num) . '" style="margin: 0 1px; padding: 6px 12px; background-color: #e2f0fb; color: #0073aa; text-decoration: none; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</a> ';
            }else {
                echo '<span class="current" style="margin: 0 1px; padding: 6px 12px; background-color: #0073aa; color: white; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</span> ';
            }
        }
        echo '</div></div><div style="clear: both;"></div>';
    }
}



/**
 * Logger simple para plugins de WordPress.
 * Guarda logs en el directorio de uploads, soporta niveles de log y contexto adicional.
 */
class Logger
{
    protected $logFilePath;

    const NIVELES = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];

    /**
     * Constructor.
     *
     * @param string $pluginSlug Identificador del plugin (se usará como parte del nombre del archivo).
     */
    public function __construct(string $pluginSlug = 'miplugin')
    {
        $uploadDir = wp_upload_dir();
        $this->logFilePath = trailingslashit($uploadDir['basedir']) . $pluginSlug . '-log.log';

        // Verifica que el archivo sea escribible
        if (!file_exists($this->logFilePath)) {
            file_put_contents($this->logFilePath, "=== Inicio del log de $pluginSlug ===" . PHP_EOL);
        }
    }

    /**
     * Registra un mensaje en el log.
     *
     * @param string $mensaje El mensaje principal.
     * @param string $nivel Nivel del log: DEBUG, INFO, WARNING, ERROR.
     * @param array $contexto Datos opcionales para agregar más contexto.
     * @return void
     */
    public function log(string $mensaje, string $nivel = 'INFO', array $contexto = []): void
    {
        $nivel = strtoupper($nivel);
        if (!in_array($nivel, self::NIVELES)) {
            $nivel = 'INFO';
        }

        $fecha = date('Y-m-d H:i:s');
        $linea = "[$fecha][$nivel] $mensaje";

        if (!empty($contexto)) {
            $linea .= ' | Contexto: ' . json_encode($contexto);
        }

        file_put_contents($this->logFilePath, $linea . PHP_EOL, FILE_APPEND);
    }

    /**
     * Acceso rápido a los niveles de log.
     */
    public function debug(string $mensaje, array $contexto = []): void   { $this->log($mensaje, 'DEBUG', $contexto); }
    public function info(string $mensaje, array $contexto = []): void    { $this->log($mensaje, 'INFO', $contexto); }
    public function warning(string $mensaje, array $contexto = []): void { $this->log($mensaje, 'WARNING', $contexto); }
    public function error(string $mensaje, array $contexto = []): void   { $this->log($mensaje, 'ERROR', $contexto); }
}



class ClaseControladorBaseBomberos
{
    protected $logger;
    public function __construct()
    {
        $this->logger =  new Logger('bomberos');
    }


    /**
     * Log de error.
     */
    public function logError(string $mensaje, array $contexto = []): void
    {
        $this->logger->error($mensaje, $contexto);
    }

    /**
     * Log de advertencia.
     */
    public function logWarning(string $mensaje, array $contexto = []): void
    {
        $this->logger->warning($mensaje, $contexto);
    }

    /**
     * Log de información.
     */
    public function logInfo(string $mensaje, array $contexto = []): void
    {
        $this->logger->info($mensaje, $contexto);
    }

    /**
     * Log de depuración.
     */
    public function logDebug(string $mensaje, array $contexto = []): void
    {
        $this->logger->debug($mensaje, $contexto);
    }

    /**
     * Arma una respuesta estandarizada para ser enviada al frontend o al cliente AJAX.
     *
     * @param string $mensaje Mensaje informativo o de estado.
     * @param string $html HTML renderizado o contenido dinámico a retornar.
     * @return array Arreglo con las claves 'mensaje' y 'html'.
     */
    public function armarRespuesta(string $mensaje, string $html = ''): array
    {
        return [
            'mensaje' => $mensaje,
            'html' => $html
        ];
    }

    /**
     * Maneja una excepción, registrando el error y relanzándola.
     *
     * @param string $mensajeLog Mensaje contextual del error para el log.
     * @param Exception $e Excepción capturada.
     * @param array $contexto Datos del request.
     * @return void
     * @throws Exception
     */
    public function manejarExcepcion(Exception $e, $contexto = []): void
    {
        $this->logError($e->getMessage(), [
            'archivo' => $e->getFile(),
            'línea'   => $e->getLine(),
            'traza'   => $e->getTraceAsString(),
            'contexto' => $contexto
        ]);
        throw $e;
    }

    /**
 * Lanza una excepción con un mensaje personalizado.
 *
 * Este método se utiliza para centralizar la generación de excepciones
 * en los controladores del plugin. Permite lanzar errores controlados
 * que pueden ser capturados por el manejador AJAX o registrados en el log.
 *
 * @param string $mensaje Mensaje descriptivo del error que será lanzado como excepción.
 *
 * @throws Exception Lanza una excepción estándar con el mensaje proporcionado.
 */
protected function lanzarExcepcion($mensaje)
{
    throw new Exception($mensaje);
}


    /**
     * Sanitiza un arreglo de datos según reglas dadas.
     */
    protected function sanitizarRequest(array $data, array $rules = []): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if ($key === 'form_data' && is_string($value)) {
                parse_str($value, $formData);
                $formRules = $rules['form_data'] ?? [];
                $sanitized[$key] = $this->sanitizarRequest($formData, $formRules);
            } elseif (isset($rules[$key])) {
                switch ($rules[$key]) {
                    case 'email':
                        $sanitized[$key] = sanitize_email($value);
                        break;
                    case 'int':
                        $sanitized[$key] = absint($value);
                        break;
                    case 'textarea':
                        $sanitized[$key] = sanitize_textarea_field($value);
                        break;
                    case 'html':
                        $sanitized[$key] = wp_kses_post($value);
                        break;
                    case 'array':
                        $sanitized[$key] = is_array($value)
                            ? $this->sanitizarRequest($value, $rules)
                            : sanitize_text_field($value);
                        break;
                    default:
                        $sanitized[$key] = sanitize_text_field($value);
                        break;
                }
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizarRequest($value, $rules);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }
}

?>