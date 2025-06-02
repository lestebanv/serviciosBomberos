<?php
if (!defined('ABSPATH')) {
    exit;
}

class ControladorInscripciones extends ClaseControladorBaseBomberos
{
    private $tablaInscripciones;
    private $tablaCursos;

    protected $reglasSanitizacion = [
        'form_data' => [
            'id_inscripcion' => 'int',
            'id_curso' => 'int',
            'email_asistente' => 'email',
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

    public function ejecutarFuncionalidad($peticion)
    {
        try {
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $funcionalidad = $peticionLimpia['funcionalidad'] ?? ($peticionLimpia['funcionalidad'] ?? 'inicial');
            $datos = $peticionLimpia['form_data'] ?? [];

            if (!$funcionalidad) {
                $this->lanzarExcepcion("Funcionalidad no especificada en Inscripciones.");
            }

    switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarInscripciones($datos);
                case 'eliminar_inscripcion':
                    return $this->eliminarInscripcion($datos);
                case 'form_editar_inscripcion':
                    return $this->formularioEditarInscripcion($datos);
                case 'actualizar_inscripcion': 
                    return $this->actualizarInscripcion($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad no encontrada: " . esc_html($funcionalidad));
        }

        } catch (Exception $e) {
            $this->manejarExcepcion("Error en ejecutar Funcionalidad de inscripciones", $datos, $e->getMessage());
        }
    }

    private function listarInscripciones($datos)
    {
        try {
            global $wpdb;
            $elementosPorPagina = 5;
            $actualpagina = max(1, (int)($datos['actualpagina'] ?? 1));
            $offset = ($actualpagina - 1) * $elementosPorPagina;
            $sqlContarInscripciones = "SELECT COUNT(*) FROM {$this->tablaInscripciones}";
            $totalRegistros = $wpdb->get_var($sqlContarInscripciones);
            $totalpaginas = ceil($totalRegistros / $elementosPorPagina);

            if ($totalRegistros === null) {
                $this->lanzarExcepcion("Error al contar inscripciones.");
            }

            $sqlListarInscripciones = $wpdb->prepare(
                "SELECT i.*, c.nombre_curso
                 FROM {$this->tablaInscripciones} i
                 JOIN {$this->tablaCursos} c ON i.id_curso = c.id_curso
                 ORDER BY i.fecha_inscripcion DESC
                 LIMIT %d OFFSET %d",
                $elementosPorPagina,
                $offset
            );

            $listaInscripciones = $wpdb->get_results($sqlListarInscripciones, ARRAY_A);

            if ($listaInscripciones === null && $totalInscripciones > 0) {
                $this->lanzarExcepcion("Error al obtener inscripciones.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoInscripciones.php';
            $html = ob_get_clean();
            return $this->armarRespuesta("Lista cargada correctamente", $html);
        } catch (Exception $e) {
           $this->manejarExcepcion("Error en ejecutar Funcionalidad de inscripciones", $datos, $e->getMessage());
        }
    }

    private function eliminarInscripcion($datos)
    {
        try {
            global $wpdb;
            $idInscripcion=$datos['id'];
            if ($idInscripcion <= 0) {
                $this->lanzarExcepcion("ID de inscripción no válido.");
            }

            $sqlEliminarInscripcion = $wpdb->prepare(
                "DELETE FROM {$this->tablaInscripciones} WHERE id_inscripcion = %d",
                $idInscripcion
            );

            $resultado = $wpdb->query($sqlEliminarInscripcion);

            if ($resultado === false) {
                $this->lanzarExcepcion("Error al eliminar la inscripción.");
            }
            return $this->listarInscripciones($datos);
        } catch (Exception $e) {
             $this->manejarExcepcion("Error en ejecutar Funcionalidad de eliminar inscripciones", $datos, $e->getMessage());
        }
    }

    private function formularioEditarInscripcion($datos)
    {
        try {
            global $wpdb;
            $idInscripcion=$datos['id'];
            $actualpagina=$datos["actualpagina"];
            if ($idInscripcion <= 0) {
                $this->lanzarExcepcion("ID de inscripción no válido.");
            }
            $sqlObtenerInscripcion = $wpdb->prepare(
                "SELECT i.*, c.nombre_curso
                 FROM {$this->tablaInscripciones} i
                 JOIN {$this->tablaCursos} c ON i.id_curso = c.id_curso
                 WHERE i.id_inscripcion = %d",
                $idInscripcion
            );
            $inscripcion = $wpdb->get_row($sqlObtenerInscripcion, ARRAY_A);

            if (!$inscripcion) {
                $this->lanzarExcepcion("Inscripción no encontrada.");
            }
            $estadosPosibles=["Registrada","Pendiente","Cerrada"];
            ob_start();
            include plugin_dir_path(__FILE__) .'formularioEditarInscripcion.php';
            $html = ob_get_clean();

            return $this->armarRespuesta("Formulario cargado correctamente", $html);
        } catch (Exception $e) {
            $this->manejarExcepcion("Error en ejecutar Funcionalidad de enviar formulario de inscripciones", $datos, $e->getMessage());
        }
    }

    private function actualizarInscripcion($datos)
    {
        try {
            global $wpdb;
            $idInscripcion = $datos['id_inscripcion'] ?? 0;

            if ($idInscripcion <= 0) {
                $this->lanzarExcepcion("ID de inscripción no válido.");
            }

            $datosActualizados = [
                'email_asistente' =>$datos['email_asistente'],
                'telefono_asistente' => $datos['telefono_asistente'] ?? null,
                'estado_inscripcion' => $datos['estado_inscripcion'],
                'notas' => $datos['notas'] ?? null,
            ];

            $formatos = ['%s', '%s', '%s'];

            $resultado = $wpdb->update(
                $this->tablaInscripciones,
                $datosActualizados,
                ['id_inscripcion' => $idInscripcion],
                $formatos,
                ['%d']
            );

            if ($resultado === false) {
                $this->lanzarExcepcion("Error al actualizar la inscripción.");
            }
            return $this->listarInscripciones($datos);
        } catch (Exception $e) {
             $this->manejarExcepcion("Error en ejecutar Funcionalidad de actualizar inscripciones", $datos, $e->getMessage());
        }
    }
}
