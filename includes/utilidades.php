<?php
// includes/utilidades.php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizes an array of input data recursively based on key-specific rules.
 *
 * @param array $data Input data to sanitize (e.g., $_POST).
 * @param array $rules Optional sanitization rules for specific keys.
 * @return array Sanitized data.
 */
function bomberos_sanitize_input($data, $rules = []) {
    $sanitized = [];
    
    foreach ($data as $key => $value) {
        // Handle form_data specifically
        if ($key === 'form_data' && is_string($value)) {
            parse_str($value, $form_data);
            // Define rules for form_data fields
            $form_rules = isset($rules['form_data']) && is_array($rules['form_data']) ? $rules['form_data'] : [];
            $sanitized[$key] = bomberos_sanitize_input($form_data, $form_rules);
        } elseif (isset($rules[$key])) {
            // Apply specific sanitization rule
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
                    if (is_array($value)) {
                        $sanitized[$key] = bomberos_sanitize_input($value, $rules);
                    } else {
                        $sanitized[$key] = sanitize_text_field($value);
                    }
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        } elseif (is_array($value)) {
            // Recursively sanitize arrays
            $sanitized[$key] = bomberos_sanitize_input($value, $rules);
        } else {
            // Default to text field sanitization
            $sanitized[$key] = sanitize_text_field($value);
        }
    }
    
    return $sanitized;
}


function barraNavegacion($tabla, $total_pages, $current_page, $align = 'left')
{
    if ($total_pages > 1) {
        $style = '';
        if ($align === 'right') {
            $style = 'float: right;';
        } elseif ($align === 'center') {
            $style = 'margin: 0 auto; text-align: center; width: fit-content;';
        } else {
            $style = 'float: left;';
        }
        $style .= ' font-size: 14px; font-weight: bold; font-family: Arial, sans-serif;';
        $pagination_args = array(
            'base' => '#%#%',
            'format' => '',
            'total' => $total_pages,
            'current' => $current_page,
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
                echo '<a href="#" class="paginacion-'.$tabla.'" data-paged="' . esc_attr($page_num) . '" style="margin: 0 1px; padding: 6px 12px; background-color: #e2f0fb; color: #0073aa; text-decoration: none; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</a> ';
            } else {
                echo '<span class="current" style="margin: 0 1px; padding: 6px 12px; background-color: #0073aa; color: white; border-radius: 4px;">' . wp_kses_post(strip_tags($link)) . '</span> ';
            }
        }

        echo '</div></div><div style="clear: both;"></div>';
    }
}


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

    /**
     * Lanza una excepción personalizada para el plugin
     * 
     * @param string $mensaje Mensaje de error
     * @param int $codigo Código de error (opcional)
     * @throws Exception
     */
    public function lanzarExcepcion($mensaje, $codigo = 0) {
        throw new Exception($mensaje, $codigo);
    }
}

?>