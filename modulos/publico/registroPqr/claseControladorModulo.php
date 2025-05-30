<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroPqr extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'form_data' => [
            'id' => 'int',
            'email' => 'email',
        ],
    ];

    public function ejecutarShortCode()
    {
        try {
            ob_start();
            include plugin_dir_path(__FILE__) . 'formPqr.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar el shortcode: ' . $e->getMessage(), null, false);
        }
    }

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $plantilla = $request['plantilla'] ?? '';

            if (empty($plantilla)) {
                $this->enviarLog("Plantilla no especificada en la solicitud", $request);
                $this->lanzarExcepcion("Plantilla no especificada.");
            }

            switch ($plantilla) {
                case 'registrar_pqr':
                    return $this->registrarPqr($sanitized_request);
                default:
                    return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($plantilla));
            }
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error al ejecutar la funcionalidad: ' . $e->getMessage(), null, false);
        }
    }

    public function registrarPqr($request)
    {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'pqr';
            $form_data = $request['form_data'] ?? [];

            $campos_obligatorios = ['nombre', 'telefono', 'email', 'tipo_solicitud', 'contenido'];
            foreach ($campos_obligatorios as $campo) {
                if (empty($form_data[$campo])) {
                    return $this->armarRespuesta("El campo '$campo' es obligatorio.", null, false);
                }
            }

            $data = [
                'nombre'         => $form_data['nombre'],
                'telefono'       => $form_data['telefono'],
                'email'          => $form_data['email'],
                'tipo_solicitud' => $form_data['tipo_solicitud'],
                'contenido'      => $form_data['contenido'],
                'ip_address'     => $_SERVER['REMOTE_ADDR'],
                'fecha_registro' => current_time('mysql'),
            ];
            $this->enviarLog("insertando pqr",$data);
            $result = $wpdb->insert($table_name, $data);
            if ($result === false) {
                return $this->armarRespuesta('Error al registrar la PQR: ' . esc_html($wpdb->last_error), null, false);
            }

            $id = $wpdb->insert_id;
            $objpqr = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $id), ARRAY_A);

            ob_start();
            include plugin_dir_path(__FILE__) . 'confirmarRegistro.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('PQR registrada con éxito', $html);
        } catch (\Throwable $e) {
            return $this->armarRespuesta('Error inesperado al registrar PQR: ' . $e->getMessage(), null, false);
        }
    }
}
