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
            $fechaHoy = date('Y-m-d');
            
            // Traer solo cursos futuros y activos
            $sql = $wpdb->prepare(
                "SELECT * FROM {$this->tablaCursos} 
                 WHERE fecha_inicio >= %s 
                 AND estado NOT IN ('Cancelado', 'Finalizado')
                 ORDER BY fecha_inicio ASC",
                $fechaHoy
            );

            $cursosDisponibles = $wpdb->get_results($sql, ARRAY_A);
            
            // Calcular cupos
            foreach ($cursosDisponibles as $key => $curso) {
                $inscritos = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND estado_inscripcion != 'Cancelada'", 
                    $curso['id_curso']
                ));
                $cursosDisponibles[$key]['cupos_disponibles'] = $curso['capacidad_maxima'] - $inscritos;
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
                 // Aquí retornamos un mensaje bonito en lugar de lanzar excepción técnica
                 $msgHTML = '<div class="notice notice-warning inline"><p><strong>¡Atención!</strong> Ya existe una inscripción registrada con este correo electrónico para este curso.</p></div>';
                 return $this->armarRespuesta('Correo ya registrado', $msgHTML);
            }

            // Validar Capacidad Máxima
            $totalInscritos = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND estado_inscripcion != 'Cancelada'",
                $idCurso
            ));

            if ($totalInscritos >= $curso['capacidad_maxima']) {
                // CORRECCIÓN CLAVE: No usamos lanzarExcepcion para evitar el prefijo de error del sistema.
                // Retornamos una respuesta "exitosa" (en transporte) pero con mensaje de aviso.
                $msgHTML = '<div class="notice notice-error inline" style="background-color: #f8d7da; border-left-color: #dc3545; color: #721c24;">
                                <p><strong>Lo sentimos, cupos agotados.</strong></p>
                                <p>El curso seleccionado acaba de completar su aforo máximo. Por favor intente seleccionar otro curso o contáctenos.</p>
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
            
            $strsql = $wpdb->prepare("
                       SELECT i.*, c.nombre_curso, c.fecha_inicio 
                       FROM {$this->tablaInscripciones} AS i 
                       INNER JOIN {$this->tablaCursos} AS c ON i.id_curso = c.id_curso  
                       WHERE i.id_inscripcion = %d", $id);
            
            $objincripcion = $wpdb->get_row($strsql, ARRAY_A);
            
            ob_start();
            if (file_exists(plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php')) {
                include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            } else {
                echo "<div class='notice notice-success inline'><p><strong>¡Inscripción Exitosa!</strong><br>Se ha registrado correctamente en: " . esc_html($objincripcion['nombre_curso']) . "</p></div>";
            }
            $html = ob_get_clean();
            
            return $this->armarRespuesta('Inscripción registrada con éxito', $html);

         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}