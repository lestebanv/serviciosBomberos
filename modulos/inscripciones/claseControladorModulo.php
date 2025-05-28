<?php
// modulos/inscripciones/claseControladorModulo.php
if (!defined('ABSPATH')) {
    exit;
}

class ControladorInscripciones extends ClaseControladorBaseBomberos
{
    protected $sanitization_rules = [
        'id_inscripcion' => 'int',
        'paged' => 'int',
        'form_data' => [ // Si tienes formularios en el admin para inscripciones
            'id_inscripcion' => 'int',
            'estado_inscripcion' => 'text',
            // ... otras reglas
        ],
    ];

    public function ejecutarFuncionalidad($request)
    {
        try {
            $sanitized_request = bomberos_sanitize_input($request, $this->sanitization_rules);
            $form_data = $sanitized_request['form_data'] ?? []; // Extraer form_data parseado
            $funcionalidad = $sanitized_request['funcionalidad'] ?? 'inicial';

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInscripcionesAdmin($form_data, $sanitized_request);
                // Aquí podrías añadir más funcionalidades para el admin:
                // case 'ver_detalle_inscripcion':
                // case 'cambiar_estado_inscripcion':
                // case 'eliminar_inscripcion_admin':
                default:
                    $this->enviarLog("Funcionalidad admin no encontrada en Inscripciones", ['funcionalidad' => $funcionalidad]);
                    $this->lanzarExcepcion("Funcionalidad no encontrada en el módulo de inscripciones (admin): " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->enviarLog("Error en ControladorInscripciones::ejecutarFuncionalidad: " . $e->getMessage(), $request);
            throw $e;
        }
    }

    public function listarInscripcionesAdmin($form_data, $request_original_sanitized)
    {
        try {
            global $wpdb;
            $tabla_inscripciones = $wpdb->prefix . 'inscripcion_curso';
            $tabla_cursos = $wpdb->prefix . 'cursos';

            $items_per_page = 10; // O el número que desees
            $current_page = isset($form_data['paged']) ? max(1, (int)$form_data['paged']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_inscripciones");
            if ($total_registros === null) {
                $this->enviarLog("Error al contar registros en $tabla_inscripciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al obtener el total de inscripciones."); // Este es el error que ves
            }
            $total_pages = ceil($total_registros / $items_per_page);

            $sql = $wpdb->prepare(
                "SELECT i.*, c.nombre_curso 
                 FROM $tabla_inscripciones i
                 JOIN $tabla_cursos c ON i.id_curso = c.id_curso
                 ORDER BY i.fecha_inscripcion DESC 
                 LIMIT %d OFFSET %d",
                $items_per_page,
                $offset
            );
            $lista_inscripciones = $wpdb->get_results($sql, ARRAY_A);

            if ($lista_inscripciones === null && $total_registros > 0) { // Solo lanzar si se esperaba algo
                $this->enviarLog("Error al obtener lista de inscripciones", [], $wpdb->last_error);
                $this->lanzarExcepcion("Error al cargar la lista de inscripciones.");
            }

            ob_start();
            // Variables que la vista necesita: $lista_inscripciones, $total_pages, $current_page
            // ASEGÚRATE DE QUE ESTA LÍNEA ES CORRECTA:
            include_once BOMBEROS_PLUGIN_DIR . 'modulos/inscripciones/vistas/listadoInscripcionesAdmin.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inscripciones cargada.', $html);
        } catch (Exception $e) {
            $this->enviarLog("Error en listarInscripcionesAdmin: " . $e->getMessage(), $form_data);
            throw $e; // Esto hará que el mensaje de error se muestre
        }
    }
}
?>