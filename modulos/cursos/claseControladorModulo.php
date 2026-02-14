<?php
class ControladorCursos extends ClaseControladorBaseBomberos
{
    protected $tablaCursos;
    protected $tablaInscripciones;
    
    // CORRECCIÓN 1: Agregamos los campos de texto que faltaban (nombre, descripcion, instructor, lugar)
    protected $reglasSanitizacion = [
        'form_data' => [
            'id_curso' => 'int',
            'nombre_curso' => 'text',    // Faltaba
            'descripcion' => 'textarea', // Faltaba (usamos textarea o text)
            'instructor' => 'text',      // Faltaba
            'lugar' => 'text',           // Faltaba
            'duracion_horas' => 'int',
            'capacidad_maxima' => 'int',
            'fecha_inicio' => 'text',
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
            $datos = $peticionLimpia['form_data'];
            
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
                default:
                    $this->lanzarExcepcion("Funcionalidad no encontrada en el modulo cursos " . esc_html($funcionalidad));
            }
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $peticion); // Corregido $solicitud a $peticion o $datos
        }
    }

    // ... (Mantén tu método listarCursos y formularioCreacion igual) ...
    public function listarCursos($datos)
    {
        // ... (Tu código existente de listarCursos está bien) ...
        // Solo asegúrate de copiarlo tal cual lo tenías
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
                    // Si fallaba la sanitización, esto saltaba porque el campo llegaba vacío
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio en insertar curso");
                }
            }

            // CORRECCIÓN 2: Validación de fecha más robusta (compara solo fechas, ignora horas)
            $fechaInicio = strtotime($datos['fecha_inicio']);
            $fechaHoy = strtotime(date('Y-m-d')); // Esto toma las 00:00:00 de hoy
            
            // Si la fecha de inicio es menor estricta a hoy (ayer o antes), falla. 
            // Si es hoy, pasa.
            if ($fechaInicio < $fechaHoy) {
                $this->lanzarExcepcion('La fecha de inicio no puede ser anterior a la fecha actual.');
            }

            $datosCurso = [
                'nombre_curso' => $datos['nombre_curso'],
                'descripcion' => $datos['descripcion'] ?? '',
                'fecha_inicio' => $datos['fecha_inicio'],
                'duracion_horas' => isset($datos['duracion_horas']) ? (int) $datos['duracion_horas'] : null,
                'instructor' => $datos['instructor'] ?? '',
                'lugar' => $datos['lugar'] ?? '',
                'capacidad_maxima' => isset($datos['capacidad_maxima']) ? (int) $datos['capacidad_maxima'] : null,
                // OJO: Aquí se guarda como 'Planificado'. 
                // Asegúrate que tu web pública muestre los planificados o cámbialo a 'Abierto'
                'estado' => 'Planificado', 
            ];

            $resultado = $wpdb->insert($this->tablaCursos, $datosCurso);
            if ($resultado === false) {
                $this->lanzarExcepcion("Error al registrar un nuevo curso.");
            }

            // Al insertar, no tenemos 'actualpagina', así que por defecto listará la página 1.
            return $this->listarCursos($datos);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    // ... (El resto de métodos formularioEdicion, actualizarCurso, eliminarCurso déjalos igual) ...
    public function formularioEdicion($datos) { /* ... Tu código original ... */ return parent::formularioEdicion($datos) ?? $this->formularioEdicionOriginal($datos); }
    // Nota: Para no pegar todo el archivo gigante, asumo que mantienes el resto igual.
    // Solo asegurate de que actualizarCurso use la nueva variable $reglasSanitizacion definida arriba.
    
    // Pego actualizarCurso y eliminarCurso y formularioEdicion para que tengas el bloque completo si lo necesitas
    public function formularioEdicionOriginal($datos)
    {
        // (Pega aquí el contenido de tu función formularioEdicion original)
        // Ya que la clase usa las variables globales, solo necesitas asegurarte de que
        // $reglasSanitizacion al principio de la clase esté corregido.
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
            
            // CORRECCION PEQUEÑA: Si valoresUnicos no existe en la clase padre, defínelo o hazlo manual
            // Asumo que existe en ClaseControladorBaseBomberos. Si no, usa un array manual.
            $estadosValidos = ['Planificado', 'Abierto', 'En Curso', 'Finalizado', 'Cancelado']; 
            // O usa $this->valoresUnicos($this->tablaCursos,'estado') si funciona.

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarCurso.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Formulario de edición cargado correctamente', $html);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
    
    // Necesitas estos métodos en la clase para que funcione el resto
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
                'duracion_horas' => isset($datos['duracion_horas']) ? (int) $datos['duracion_horas'] : null,
                'instructor' => $datos['instructor'] ?? '',
                'lugar' => $datos['lugar'] ?? '',
                'capacidad_maxima' => isset($datos['capacidad_maxima']) ? (int) $datos['capacidad_maxima'] : null,
                'estado' => $datos['estado'] ?? 'Planificado', // Unificamos casing
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
}