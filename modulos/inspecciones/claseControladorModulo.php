<?php
class ControladorInspecciones extends ClaseControladorBaseBomberos
{
    protected $tablaEmpresas;
    protected $tablaInspecciones;
    protected $tablaBomberos;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_empresa' => 'int',
            'id_inspeccion'=>'int',
            'id_bombero_asignado' => 'int',
            'email' => 'email',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaEmpresas = $wpdb->prefix . 'empresas';
        $this->tablaInspecciones = $wpdb->prefix . 'inspecciones';
        $this->tablaBomberos = $wpdb->prefix . 'bomberos';
      
    }

    public function ejecutarFuncionalidad($solicitud)
    {
        try {
            $solicitudSanitizada = $this->sanitizarRequest($solicitud, $this->reglasSanitizacion);
            $funcionalidad = $solicitudSanitizada['funcionalidad'] ?? '';
            $datos = $solicitudSanitizada['form_data'] ?? [];

            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada en la solicitud");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInspecciones($datos);
                case 'editar_inspeccion':
                    return $this->formularioEdicion($datos);
                case 'actualizar_inspeccion':
                    return $this->actualizarInspeccion($datos);
                case 'eliminar_inspeccion':
                    return $this->eliminarInspeccion($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
            }
        } catch (Exception $e) {
            $this->manejarExcepcion( $e, $solicitud);
        }
    }

    public function listarInspecciones($datos)
    {
        try {
            global $wpdb;
            $elementosPorPagina = 5;
            $actualpagina = isset($datos['actualpagina']) ? max(1, (int) $datos['actualpagina']) : 1;
            $offset = ($actualpagina - 1) * $elementosPorPagina;

            $totalRegistros = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaInspecciones}");
            $totalpaginas = ceil($totalRegistros / $elementosPorPagina);


                 $sqlInspecciones = $wpdb->prepare("  
                      SELECT 
                       i.*,
                       e.razon_social, e.direccion, e.barrio,
                       CONCAT(b.apellidos,' ',b.nombres) AS nombre_bombero_asignado
                    FROM {$this->tablaInspecciones} i
                    INNER JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa
                    LEFT JOIN {$this->tablaBomberos} b ON i.id_inspector_asignado = b.id_bombero
                    ORDER BY i.fecha_registro DESC;");


            // $sqlInspecciones = $wpdb->prepare(
            //     "SELECT i.*, e.razon_social, e.direccion, e.barrio 
            //         FROM {$this->tablaInspecciones} i 
            //         LEFT JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa 
            //         ORDER BY i.estado DESC, i.fecha_registro ASC 
            //         LIMIT %d OFFSET %d;", 
            //     $elementosPorPagina,
            //     $offset
            // );
            $listaInspecciones = $wpdb->get_results($sqlInspecciones, ARRAY_A);
            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoInspecciones.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de inspecciones cargada con éxito', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;
            $id = isset($datos['id']) ? (int) $datos['id'] : 0;
            $actualpagina = isset($datos['actualpagina']) ? (int) $datos['actualpagina'] : 1;
            $sqlInspeccion=$wpdb->prepare(
                "SELECT i.*, e.razon_social, e.nit, e.direccion, e.barrio
                 FROM {$this->tablaInspecciones} i 
                 LEFT JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa 
                 WHERE i.id_inspeccion = %d",
                $id
            );
            $inspeccion = $wpdb->get_row($sqlInspeccion, ARRAY_A);
             $estadoValidos=$this->valoresUnicos($this->tablaInspecciones,'estado');
            // Obtener lista de bomberos activos para el desplegable
            $listaBomberos = $wpdb->get_results(
                "SELECT id_bombero, nombres, apellidos, telefono FROM {$this->tablaBomberos} WHERE estado = 'activo' ORDER BY apellidos ASC, nombres ASC", 
                ARRAY_A
            );

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarInspeccion.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado.', $html);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

   
        public function actualizarInspeccion($datos)
    {
        try {
            global $wpdb;
            $id_inspeccion = (int) $datos['id_inspeccion'];
            $id_inspector_asignado = !empty($datos['id_inspector_asignado']) ? (int)$datos['id_inspector_asignado'] : null;

            // Datos base para actualizar
            $datosActualizar = [
                'fecha_programada' => !empty($datos['fecha_programada']) ? $datos['fecha_programada'] : null,
                'fecha_expedicion' => !empty($datos['fecha_expedicion']) ? $datos['fecha_expedicion'] : null,
                'estado' => $datos['estado'],
                'nombre_encargado' => $datos['nombre_encargado'], 
                'telefono_encargado' => $datos['telefono_encargado'], // CORRECCIÓN: Usabas nombre_encargado aquí por error.
                'id_inspector_asignado' => $id_inspector_asignado
            ];
            
            $actualizado = $wpdb->update($this->tablaInspecciones, $datosActualizar, ['id_inspeccion' => $id_inspeccion]);
            
            if ($actualizado === false) {
                 $this->lanzarExcepcion("Error al actualizar la inspección.");
            }
            
            // Correo
            // Verificamos si el botón presionado fue 'Programar Inspeccion'
            if (isset($datos['btnaccion']) && $datos['btnaccion'] === 'Programar Inspeccion') {
                // Recolectamos toda la información necesaria para el correo en una sola consulta
                $infoCorreoSql = $wpdb->prepare("
                    SELECT 
                        i.fecha_programada, i.nombre_encargado, i.telefono_encargado,
                        e.razon_social, e.email AS email_empresa, e.representante_legal,
                        CONCAT(b.nombres, ' ', b.apellidos) AS nombre_inspector,
                        b.telefono AS telefono_inspector
                    FROM {$this->tablaInspecciones} i
                    INNER JOIN {$this->tablaEmpresas} e ON i.id_empresa = e.id_empresa
                    LEFT JOIN {$this->tablaBomberos} b ON i.id_inspector_asignado = b.id_bombero
                    WHERE i.id_inspeccion = %d
                ", $id_inspeccion);
                $infoCorreo = $wpdb->get_row($infoCorreoSql, ARRAY_A);

                if ($infoCorreo && !empty($infoCorreo['email_empresa'])) {
                    // Preparamos y enviamos el correo
                    $para = $infoCorreo['email_empresa'];
                    $asunto = 'Programación de Visita de Inspección - Bomberos Pamplona';

                    $cuerpo = '<h1>Visita de Inspección Programada</h1>';
                    $cuerpo .= '<p>Estimado(a) ' . esc_html($infoCorreo['representante_legal']) . ',</p>';
                    $cuerpo .= '<p>Le informamos que su visita de inspección para la empresa <strong>' . esc_html($infoCorreo['razon_social']) . '</strong> ha sido programada con los siguientes detalles:</p>';
                    $cuerpo .= '<ul>';
                    $cuerpo .= '<li><strong>Fecha Programada:</strong> ' . esc_html(date_i18n('l, j \d\e F \d\e Y', strtotime($infoCorreo['fecha_programada']))) . '</li>';
                    $cuerpo .= '<li><strong>Inspector Asignado:</strong> ' . esc_html($infoCorreo['nombre_inspector'] ?? 'No especificado') . '</li>';
                    $cuerpo .= '<li><strong>Teléfono del Inspector:</strong> ' . esc_html($infoCorreo['telefono_inspector'] ?? 'N/A') . '</li>';
                    $cuerpo .= '</ul>';
                    $cuerpo .= '<p>Por favor, asegúrese de que el señor/a <strong>' . esc_html($infoCorreo['nombre_encargado']) . '</strong> (Tel: ' . esc_html($infoCorreo['telefono_encargado']) . ') esté disponible para atender la visita.</p>';
                    $cuerpo .= '<p>Gracias por su cooperación.</p>';
                    $cuerpo .= '<hr><p>Cuerpo de Bomberos Voluntarios de Pamplona</p>';

                    try {
                        $this->enviarCorreoPorGmail($para, $asunto, $cuerpo);
                    } catch (Exception $e) {
                        $this->logError("Fallo al enviar correo de programación de inspección a {$para}: " . $e->getMessage());
                        
                    }
                }
            }
          

            // Finalmente, devolvemos la lista actualizada
            return $this->listarInspecciones($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }

    public function eliminarInspeccion($datos)
    {
        try {
            global $wpdb;
            $id = isset($datos['id']) ? (int) $datos['id'] : 0;
            $actualpagina = isset($datos['actualpagina']) ? (int) $datos['actualpagina'] : 1;
            $resultado = $wpdb->delete($this->tablaInspecciones, ['id_inspeccion' => $id]);
            return $this->listarInspecciones($datos);
       } catch (Exception $e) {
            $this->manejarExcepcion( $e, $datos);
        }
    }
}