<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroInscripciones extends ClaseControladorBaseBomberos
{
    private $tablaInscripciones;
    private $tablaCursos;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'nombre_asistente' => 'text',
            'email_asistente' => 'email',
            'telefono_asistente' => 'text',
            'actualpagina' => 'int'
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
        $this->tablaCursos = $wpdb->prefix . 'cursos';
    }

    public function ejecutarShortCode()
    {
        try {
            global $wpdb;
            $fechaHoy = current_time('Y-m-d');
            
            $sql = $wpdb->prepare(
                "SELECT * FROM {$this->tablaCursos} 
                 WHERE fecha_inicio >= %s 
                 AND estado NOT IN ('Cancelado', 'Finalizado', 'cancelado', 'finalizado')
                 ORDER BY fecha_inicio ASC",
                $fechaHoy
            );

            $cursosDisponibles = $wpdb->get_results($sql, ARRAY_A);
            
            if ($cursosDisponibles) {
                foreach ($cursosDisponibles as $key => $curso) {
                    $inscritos = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND estado_inscripcion != 'Cancelada'", 
                        $curso['id_curso']
                    ));
                    $cursosDisponibles[$key]['cupos_disponibles'] = $curso['capacidad_maxima'] - $inscritos;
                }
            }
            
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioInscripcionCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e);
        }
    }

    public function ejecutarFuncionalidad($peticion)
    {
        try {
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $plantilla = $peticionLimpia['plantilla'] ?? 'inicial';
            $datos = $peticionLimpia['form_data'] ?? [];
            
            switch ($plantilla) {
                case 'registrar_inscripcion':
                    return $this->registrarInscripcion($datos);
                default:
                    return $this->armarRespuesta('Funcionalidad no encontrada: ' . esc_html($plantilla));
            }
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function registrarInscripcion($datos)
    {
        try {
            global $wpdb;
            
            // Validaciones
            $camposObligatorios = ['nombre_asistente', 'email_asistente', 'id_curso'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio.");
                }
            }

            $idCurso = (int)$datos['id_curso'];
            $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tablaCursos} WHERE id_curso = %d", $idCurso), ARRAY_A);
            
            if (!$curso) {
                $this->lanzarExcepcion("El curso seleccionado no existe o fue eliminado.");
            }

            // Validar duplicados
            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND email_asistente = %s",
                $idCurso,
                $datos['email_asistente']
            ));

            if ($existe > 0) {
                 $msgHTML = '<div class="notice notice-warning inline"><p><strong>¡Atención!</strong> Ya existe una inscripción registrada con este correo electrónico para este curso.</p></div>';
                 return $this->armarRespuesta('Correo ya registrado', $msgHTML);
            }

            // Validar Capacidad
            $totalInscritos = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND estado_inscripcion != 'Cancelada'",
                $idCurso
            ));

            if ($totalInscritos >= $curso['capacidad_maxima']) {
                $msgHTML = '<div class="notice notice-error inline" style="background-color: #f8d7da; border-left-color: #dc3545; color: #721c24;">
                                <p><strong>Lo sentimos, cupos agotados.</strong></p>
                                <p>El curso seleccionado acaba de completar su aforo máximo.</p>
                            </div>';
                return $this->armarRespuesta('Cupos Agotados', $msgHTML);
            }

            // Insertar
            $dataInscripcion = [
                'id_curso'           => $idCurso,
                'nombre_asistente'   => $datos['nombre_asistente'],
                'telefono_asistente' => $datos['telefono_asistente'],
                'email_asistente'    => $datos['email_asistente'],
                'estado_inscripcion' => "Registrada",
                'fecha_inscripcion'  => current_time('mysql')
            ];

            $result = $wpdb->insert($this->tablaInscripciones, $dataInscripcion);
            
            if ($result === false) {
                $this->lanzarExcepcion("Hubo un problema técnico al guardar la inscripción.");
            }

            $id = $wpdb->insert_id;
            
            // RECUPERAR DATOS COMPLETOS (JOIN)
            $strsql = $wpdb->prepare("
                       SELECT i.*, c.nombre_curso, c.fecha_inicio, c.instructor, c.lugar 
                       FROM {$this->tablaInscripciones} AS i 
                       INNER JOIN {$this->tablaCursos} AS c ON i.id_curso = c.id_curso  
                       WHERE i.id_inscripcion = %d", $id);
            
            // Usamos $infoCompleta consistentemente
            $infoCompleta = $wpdb->get_row($strsql, ARRAY_A);
            
            // CORREO
            if ($infoCompleta) {
                $fechaLegible = date_i18n(get_option('date_format'), strtotime($infoCompleta['fecha_inicio']));
                $nombreInstructor = !empty($infoCompleta['instructor']) ? $infoCompleta['instructor'] : 'Por definir';
                $lugarCurso = !empty($infoCompleta['lugar']) ? $infoCompleta['lugar'] : 'Instalaciones del Cuerpo de Bomberos';

                $asunto = 'Confirmación de Inscripción - ' . $infoCompleta['nombre_curso'];
                $mensaje = '
                <div style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e5e5e5; padding: 20px;">
                    <h2 style="color: #d2232a; text-align: center;">¡Inscripción Exitosa!</h2>
                    <p>Hola <strong>' . esc_html($infoCompleta['nombre_asistente']) . '</strong>,</p>
                    <p>Hemos recibido tu inscripción correctamente. A continuación los detalles:</p>
                    
                    <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                        <tr style="background-color: #f9f9f9;">
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Curso:</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . esc_html($infoCompleta['nombre_curso']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Fecha:</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . $fechaLegible . '</td>
                        </tr>
                        <tr style="background-color: #f9f9f9;">
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Instructor:</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . esc_html($nombreInstructor) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Lugar:</strong></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . esc_html($lugarCurso) . '</td>
                        </tr>
                    </table>

                    <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-top: 20px; text-align: center;">
                        <strong>⚠️ NOTA IMPORTANTE SOBRE LA HORA:</strong><br>
                        Por favor estar atentos a este medio. Un día antes del evento les confirmaremos la hora exacta de inicio.
                    </div>

                    <p style="margin-top: 30px; font-size: 12px; color: #777; text-align: center;">
                        Cuerpo de Bomberos Voluntarios de Pamplona
                    </p>
                </div>';

                try {
                    $this->enviarCorreoPorGmail($infoCompleta['email_asistente'], $asunto, $mensaje);
                } catch (Exception $mailError) {
                    $this->logError("Error enviando correo: " . $mailError->getMessage());
                }
            }

            // PASAR DATOS A LA VISTA
            // Para compatibilidad con tu vista actual, creamos $objincripcion
            $objincripcion = $infoCompleta;
            
            ob_start();
            if (file_exists(plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php')) {
                include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            } else {
                echo "<div class='notice notice-success inline'><p><strong>¡Inscripción Exitosa!</strong></p></div>";
            }
            $html = ob_get_clean();
            
            return $this->armarRespuesta('Inscripción registrada con éxito', $html);

         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}