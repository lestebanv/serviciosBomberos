<?php
require_once BOMBEROS_PLUGIN_DIR . 'includes/utilidades.php';

class ControladorBomberosShortCodeRegistroInscripciones extends ClaseControladorBaseBomberos
{
    private $tablaInscripciones;
    private $tablaCursos;

    // CORRECCIÓN 1: Agregamos los campos del formulario a la lista blanca.
    // Si no están aquí, la función sanitizarRequest los borra y llegan vacíos.
    protected $reglasSanitizacion = [
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'nombre_asistente' => 'text',    // Agregado
            'email_asistente' => 'email',    // Agregado
            'telefono_asistente' => 'text',  // Agregado
            'notas' => 'textarea',
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
            
            // CORRECCIÓN 2: Filtramos los cursos.
            // Solo mostramos cursos cuya fecha de inicio sea hoy o futuro (>=)
            // Y que el estado NO sea 'Cancelado' ni 'Finalizado'.
            $fechaHoy = date('Y-m-d');
            
            $sql = $wpdb->prepare(
                "SELECT * FROM {$this->tablaCursos} 
                 WHERE fecha_inicio >= %s 
                 AND estado NOT IN ('Cancelado', 'Finalizado')
                 ORDER BY fecha_inicio ASC",
                $fechaHoy
            );

            $cursosDisponibles = $wpdb->get_results($sql, ARRAY_A);
            
            // Opcional: Calcular cupos disponibles para mostrarlos en el select
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

            // 1. Obtener datos del curso (Capacidad y Estado)
            $curso = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tablaCursos} WHERE id_curso = %d", $idCurso), ARRAY_A);
            
            if (!$curso) {
                $this->lanzarExcepcion("El curso seleccionado no existe o fue eliminado.");
            }

            // 2. Verificar duplicados (misma persona, mismo curso)
            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND email_asistente = %s",
                $idCurso,
                $datos['email_asistente']
            ));

            if ($existe > 0) {
                $this->lanzarExcepcion("Ya existe una inscripción con este correo electrónico para este curso.");
            }

            // CORRECCIÓN 3: Validar Capacidad Máxima
            $totalInscritos = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaInscripciones} WHERE id_curso = %d AND estado_inscripcion != 'Cancelada'",
                $idCurso
            ));

            if ($totalInscritos >= $curso['capacidad_maxima']) {
                $this->lanzarExcepcion("Lo sentimos, no se puede completar el registro. El curso ha alcanzado su capacidad máxima ({$curso['capacidad_maxima']} cupos).");
            }

            // Si pasa todas las validaciones, insertamos
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
                $this->lanzarExcepcion("Error al guardar en la base de datos.");
            }

            $id = $wpdb->insert_id;
            
            // Obtenemos datos combinados para el mensaje de respuesta
            $strsql = $wpdb->prepare("
                       SELECT i.*, c.nombre_curso, c.fecha_inicio 
                       FROM {$this->tablaInscripciones} AS i 
                       INNER JOIN {$this->tablaCursos} AS c ON i.id_curso = c.id_curso  
                       WHERE i.id_inscripcion = %d", $id);
            
            $objincripcion = $wpdb->get_row($strsql, ARRAY_A);
            
            ob_start();
            // Asegúrate de tener este archivo creado o usa un echo simple si no existe
            if (file_exists(plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php')) {
                include plugin_dir_path(__FILE__) . 'mensajeRespuestaInscripcion.php';
            } else {
                echo "<div class='notice notice-success'><p>Inscripción realizada con éxito al curso " . esc_html($objincripcion['nombre_curso']) . "</p></div>";
            }
            $html = ob_get_clean();
            
            return $this->armarRespuesta('Inscripción registrada con éxito', $html);

         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}