<?php
class ControladorCursos extends ClaseControladorBaseBomberos
{
    protected $tablaCursos;
    protected $tablaInscripciones;
   protected $reglasSanitizacion = [
    'form_data' => [
        'id_curso' => 'int',
        'duracion_horas' => 'int',
        'capacidad_maxima' => 'int',
        'fecha_inicio' => 'text',
        'hora_inicio' => 'text', // <--- AGREGAR ESTO
        'estado' => 'text',
    ],
];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaCursos = $wpdb->prefix . 'cursos';
        $this->tablaInscripciones = $wpdb->prefix . 'inscripciones';
    }

    public function ejecutarFuncionalidad($peticion)
    {
        try {
            $peticionLimpia = $this->sanitizarRequest($peticion, $this->reglasSanitizacion);
            $funcionalidad = $peticionLimpia['funcionalidad'] ?? '';
            $datos=$peticionLimpia['form_data'];
            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada en el modulo cursos");
            }

            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarCursos($datos);
                case 'form_crear':
                    return $this->formularioCreacion($datos);
                case 'registrar_curso':
                    return $this->insertarCurso($datos);
                case 'editar_curso':
                    return $this->formularioEdicion($datos);
                case 'actualizar_curso':
                    return $this->actualizarCurso($datos);
                case 'eliminar_curso':
                    return $this->eliminarCurso($datos);
                   
                 case 'enviar_correo_participantes':
                return $this->enviarCorreoParticipantes($datos);
             
                default:
                    $this->lanzarExcepcion("Funcionalidad no encontrada en el modulo cursos " . esc_html($funcionalidad));
            }
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $solicitud);
        }
    }

    public function listarCursos($datos)
    {
        try {
            global $wpdb;

            $elementosPorPagina = 5;
            $actualpagina = isset($datos['actualpagina']) ? max(1, (int) $datos['actualpagina']) : 1;
            $offset = ($actualpagina - 1) * $elementosPorPagina;

            $totalRegistros = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaCursos}");
            if ($totalRegistros === null) {
                $this->lanzarExcepcion("Error al obtener el total de registros en curso");
            }

            $totalpaginas = ceil($totalRegistros / $elementosPorPagina);

            $strSqlCursos = $wpdb->prepare(
                "SELECT * FROM {$this->tablaCursos} ORDER BY fecha_inicio DESC LIMIT %d OFFSET %d",
                $elementosPorPagina,
                $offset
            );
            $listaCursos = $wpdb->get_results($strSqlCursos, ARRAY_A);

            if ($listaCursos === null) {
                $this->lanzarExcepcion("Error al cargar la lista de cursos".$wpdb->last_error);
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoCursos.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de cursos cargada con éxito', $html);
          } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function formularioCreacion($datos)
    {
        try {

           
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de creación cargado correctamente', $html);



        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function insertarCurso($datos)
    {
        try {
            global $wpdb;
            $camposObligatorios = ['nombre_curso', 'fecha_inicio'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio en insertar curso");
                }
            }

            $fechaInicio = strtotime($datos['fecha_inicio']);
            $fechaHoy = strtotime(date('Y-m-d'));
            if ($fechaInicio < $fechaHoy) {
                $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            }

            $datosCurso = [
                'nombre_curso' => $datos['nombre_curso'],
                'descripcion' => $datos['descripcion'] ?? '',
                'fecha_inicio' => $datos['fecha_inicio'],
                'hora_inicio' => $datos['hora_inicio'] ?? '08:00',
                'duracion_horas' => isset($datos['duracion_horas']) ? (int) $datos['duracion_horas'] : null,
                'instructor' => $datos['instructor'] ?? '',
                'lugar' => $datos['lugar'] ?? '',
                'capacidad_maxima' => isset($datos['capacidad_maxima']) ? (int) $datos['capacidad_maxima'] : null,
                'estado' => 'Planificado',
            ];

            $resultado = $wpdb->insert($this->tablaCursos, $datosCurso);
            if ($resultado === false) {
                $this->lanzarExcepcion("Error al registrar un nuevo curso.");
            }

            return $this->listarCursos($datos);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    
    }

    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;
            $idCurso = isset($datos['id']) ? (int) $datos['id'] : 0;
            $actualpagina=$datos['actualpagina'];
            if ($idCurso <= 0) {
                $this->lanzarExcepcion('ID inválido para edición del curso');
            }

            $strSqlCurso = $wpdb->prepare("SELECT * FROM {$this->tablaCursos} WHERE id_curso = %d", $idCurso);
            $curso = $wpdb->get_row($strSqlCurso, ARRAY_A);

            if (!$curso) {
                $this->lanzarExcepcion('Curso no encontrado id '.$idCurso);
            }
            $strSqlInscripciones = $wpdb->prepare("SELECT * FROM {$this->tablaInscripciones} WHERE id_curso = %d", $idCurso);
            $listaInscripciones = $wpdb->get_results($strSqlInscripciones, ARRAY_A);
            $estadosValidos=$this->valoresUnicos($this->tablaCursos,'estado');
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado correctamente', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    
    }
public function actualizarCurso($datos)
{
    try {
        global $wpdb;
        $idCurso = isset($datos['id_curso']) ? (int) $datos['id_curso'] : 0;
        if ($idCurso <= 0) {
            $this->lanzarExcepcion('ID de curso inválido en actualizacion'.$idCurso);
        }

        if (empty($datos['nombre_curso']) || empty($datos['fecha_inicio'])) {
            $this->lanzarExcepcion("Los campos 'nombre_curso' y 'fecha_inicio' son obligatorios.",$datos);
        }
        $datosCurso = [
            'nombre_curso' => $datos['nombre_curso'],
            'descripcion' => $datos['descripcion'] ?? '',
            'fecha_inicio' => $datos['fecha_inicio'],
            'hora_inicio' => $datos['hora_inicio'] ?? '08:00',
            'duracion_horas' => isset($datos['duracion_horas']) ? (int) $datos['duracion_horas'] : null,
            'instructor' => $datos['instructor'] ?? '',
            'lugar' => $datos['lugar'] ?? '',
            'capacidad_maxima' => isset($datos['capacidad_maxima']) ? (int) $datos['capacidad_maxima'] : null,
            'estado' => $datos['estado'] ?? 'planificado',
        ];

        $resultado = $wpdb->update(
            $this->tablaCursos,
            $datosCurso,
            ['id_curso' => $idCurso],
            null,
            ['%d']
        );

        if ($resultado === false) {
            $this->lanzarExcepcion("Error al actualizar el curso".$idCurso);
        }

        return $this->listarCursos($datos);
      } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
}
public function eliminarCurso($datos)
{
    try {
        global $wpdb;
        $idCurso = isset($datos['id']) ? (int) $datos['id'] : 0;
        if ($idCurso <= 0) {
            $this->lanzarExcepcion('ID de curso inválido en eliminacion');
        }
        $resultado = $wpdb->delete($this->tablaCursos, ['id_curso' => $idCurso], ['%d']);
        if ($resultado === false) {
            $this->lanzarExcepcion('No se pudo eliminar el curso.');
        }
        return $this->listarCursos($datos);
     } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
     }
}



public function enviarCorreoParticipantes($datos)
{
    try {
        global $wpdb;

        // Validar datos recibidos del formulario
        $idCurso = isset($datos['id_curso']) ? (int)$datos['id_curso'] : 0;
        $destinatario = isset($datos['destinatario_correo']) ? $datos['destinatario_correo'] : '';
        $mensaje = isset($datos['mensaje_correo']) ? nl2br(stripslashes($datos['mensaje_correo'])) : ''; // nl2br para mantener saltos de línea

        if ($idCurso <= 0 || empty($destinatario) || empty($mensaje)) {
            $this->lanzarExcepcion("Faltan datos para enviar el correo (ID de curso, destinatario o mensaje).");
        }

        // Obtener información del curso para el asunto del correo
        $curso = $wpdb->get_row($wpdb->prepare("SELECT nombre_curso FROM {$this->tablaCursos} WHERE id_curso = %d", $idCurso));
        if (!$curso) {
            $this->lanzarExcepcion("Curso no encontrado.");
        }
        $asunto = "Comunicado sobre el curso: " . $curso->nombre_curso;
        
        $listaEmails = [];
        // Determinar a quién enviar el correo
        if ($destinatario === 'todos') {
            // Obtener todos los correos de los inscritos a este curso
            $emails = $wpdb->get_col($wpdb->prepare("SELECT email_asistente FROM {$this->tablaInscripciones} WHERE id_curso = %d", $idCurso));
            if ($emails) {
                $listaEmails = $emails;
            }
        } else {
            // Es un solo email, validarlo
            if (is_email($destinatario)) {
                $listaEmails[] = $destinatario;
            }
        }

        if (empty($listaEmails)) {
            $this->lanzarExcepcion("No se encontraron destinatarios válidos para enviar el correo.");
        }

        $enviados = 0;
        $fallidos = 0;
        // Enviar el correo a cada destinatario
        foreach ($listaEmails as $email) {
            try {
                $this->enviarCorreoPorGmail($email, $asunto, $mensaje);
                $enviados++;
            } catch (Exception $e) {
                $this->logError("Fallo al enviar correo a participante {$email} del curso {$idCurso}: " . $e->getMessage());
                $fallidos++;
            }
        }

        // Devolver una respuesta JSON con un mensaje de éxito, sin recargar la vista.
        $mensajeRespuesta = "Proceso finalizado. Correos enviados: {$enviados}. Correos fallidos: {$fallidos}.";
        // No retornamos una vista HTML, solo un mensaje de estado.
        return $this->armarRespuesta($mensajeRespuesta, ''); 

    } catch (Exception $e) {
        $this->manejarExcepcion($e, $datos);
    }
}


}
