<?php
class ControladorBomberos extends ClaseControladorBaseBomberos
{
    protected $tablaBomberos;
    protected $reglasSanitizacion = [
        'form_data' => [
            'id_bombero' => 'int',
            'nombres' => 'textarea',
            'apellidos' => 'textarea',
            'tipo_dicumento' => 'textarea',
             'numero_documento' => 'textarea',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        global $wpdb;
        $this->tablaBomberos = $wpdb->prefix . 'bomberos';
    }

    public function ejecutarFuncionalidad($solicitud)
    {
        try {
            $solicitudSanitizada = $this->sanitizarRequest($solicitud, $this->reglasSanitizacion);
            $funcionalidad = $solicitudSanitizada['funcionalidad'] ?? '';
            $datos = $solicitudSanitizada['form_data'] ?? [];
            if (empty($funcionalidad)) {
                $this->lanzarExcepcion("Funcionalidad no especificada.");
            }
            
            switch ($funcionalidad) {
                case 'inicial':
                case 'pagina_inicial':
                    return $this->listarBomberos($datos);
                case 'editar_bombero':
                    return $this->formularioEdicion($datos);
                case 'actualizar_bombero':
                    return $this->actualizarBombero($datos);
                case 'form_crear':
                    return $this->formularioCreacion($datos);
                case 'registrar_bombero':
                    return $this->insertarBombero($datos);
                case 'eliminar_bombero':
                    return $this->eliminarBombero($datos);
                default:
                    $this->lanzarExcepcion("Funcionalidad '" . esc_html($funcionalidad) . "' no encontrada.");
            }
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $solicitud);
        }
    }

    public function listarBomberos($datos)
    {
        try {
            global $wpdb;
            $itemsPorPagina = 4;
            
            $actualpagina = isset($datos['actualpagina']) ? max(1, (int) $datos['actualpagina']) : 1;
            $offset = ($actualpagina - 1) * $itemsPorPagina;

            $totalRegistros = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tablaBomberos}");
            if ($totalRegistros === null) {
                $this->lanzarExcepcion("Error al obtener el total de registros.");
            }

            $totalpaginas = ceil($totalRegistros / $itemsPorPagina);

            $strSqlBomberos = $wpdb->prepare(
                "SELECT * FROM {$this->tablaBomberos} ORDER BY UPPER(apellidos) ASC LIMIT %d OFFSET %d",
                $itemsPorPagina,
                $offset
            );
            $listaBomberos = $wpdb->get_results($strSqlBomberos, ARRAY_A);

            if ($listaBomberos === null) {
                $this->lanzarExcepcion("Error al cargar la lista de empresas.");
            }

            ob_start();
            include plugin_dir_path(__FILE__) . 'listadoBomberos.php';
            $html = ob_get_clean();
            return $this->armarRespuesta('Lista de Bomberos', $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function formularioEdicion($datos)
    {
        try {
            global $wpdb;

            $idBombero = (int) ($datos['id'] ?? 0);
            $actualpagina=$datos['actualpagina'];
            if ($idBombero <= 0) {
                $this->lanzarExcepcion("ID inválido de Bombero ");
            }

            $strSqlBombero = $wpdb->prepare(
                "SELECT * FROM {$this->tablaBomberos} WHERE id_bombero = %d",
                $idBombero
            );
            $bombero = $wpdb->get_row($strSqlBombero, ARRAY_A);

            if (!$bombero) {
                $this->lanzarExcepcion("Bombero no encontrada:".$idBombero);
            }

            $strSqlBombero = $wpdb->prepare(
                "SELECT * FROM {$this->tablaBomberos} WHERE id_bombero = %d",
                $idBombero
            );
            
            $generosValidos=$this->valoresUnicos($this->tablaBomberos,'genero');
            $tiposSangreValidos=$this->valoresUnicos($this->tablaBomberos,'grupo_sanguineo');
            $rangoValidos=$this->valoresUnicos($this->tablaBomberos,'rango');
            $estadosValidos=$this->valoresUnicos($this->tablaBomberos,'estado');
            
            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioEditarBombero.php';
            $html = ob_get_clean();

            return $this->armarRespuesta('Formulario de edición cargado', $html);
       } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function eliminarBombero($datos)
    {
        try {
            global $wpdb;
            $idBombero = (int) ($datos['id'] ?? 0);
            if ($idBombero <= 0) {
                $this->lanzarExcepcion("ID de Bombero no válido:".$idBombero);
            }

            $resultado = $wpdb->delete($this->tablaBomberos, ['id_bombero' => $idBombero]);
            if ($resultado === false) {
                $this->lanzarExcepcion("Error al eliminar la bombero por id".$idBombero);
            }

            return $this->listarBomberos($datos);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function actualizarBombero($datos)
    {
        try {
            global $wpdb;
            $idBombero = (int) ($datos['id_bombero'] ?? 0);
            $actualpagina=$datos['actualpagina'];
            $datosBomberos = [
                'nombres' => $datos['nombres'],
                'apellidos' => $datos['apellidos'],
            
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
            
                'direccion' => $datos['direccion'],
                'telefono' => $datos['telefono'],
                'email' => $datos['email'],
                
                'rango' => $datos['rango'],
                'estado' => $datos['estado'],
                'fecha_ingreso' => $datos['fecha_ingreso'],
                'observaciones' => $datos['observaciones'],
            ];

           

            if ($idBombero <= 0) {
                $this->lanzarExcepcion("ID de Bombero no válido.");
            }

            $resultado = $wpdb->update($this->tablaBomberos, $datosBomberos, ['id_bombero' => $idBombero]);
            if ($resultado === false) {
                $this->lanzarExcepcion("No se pudo actualizar Bombero.");
            }
            return $this->listarBomberos($datos);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function formularioCreacion($datos)
    {
        try {
          //  $actualpagina=$datos['actualpagina'];

           $tipoDocumentoValidos=$this->valoresUnicos($this->tablaBomberos,'tipo_documento');
        
           $generosValidos=$this->valoresUnicos($this->tablaBomberos,'genero');
            $tiposSangreValidos=$this->valoresUnicos($this->tablaBomberos,'grupo_sanguineo');
            $estadosValidos=$this->valoresUnicos($this->tablaBomberos,'estado');
            $rangoValidos=$this->valoresUnicos($this->tablaBomberos,'rango');

            ob_start();
            include plugin_dir_path(__FILE__) . 'formularioCrearBombero.php';
            $html = ob_get_clean();
            return $this->armarRespuesta("Formulario de creación cargado", $html);
        } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }

    public function insertarBombero($datos)
    {
        try {
            global $wpdb;
            $camposObligatorios = ['nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'fecha_nacimiento'
            , 'genero','direccion','telefono','email','grupo_sanguineo','rango','estado','fecha_ingreso'];

            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    $this->lanzarExcepcion("El campo '$campo' es obligatorio en insertar empresa.");
                }
            }

            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tablaBomberos} WHERE numero_documento = %s",
                $datos['numero_documento']
            ));
            if ($existe > 0) {
                $this->lanzarExcepcion("Ya existe un Bombero registrada con el numero de documento: {$datos['numero_documento']}");
            }

            $resultado = $wpdb->insert($this->tablaBomberos, [
                'nombres' => $datos['nombres'],
                'apellidos' => $datos['apellidos'],
                'tipo_documento' => $datos['tipo_documento'],
                'numero_documento' => $datos['numero_documento'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'genero' => $datos['genero'],
                'direccion' => $datos['direccion'],
                'telefono' => $datos['telefono'],
                'email' => $datos['email'],
                'grupo_sanguineo' => $datos['grupo_sanguineo'],                
                'rango' => $datos['rango'],
                'estado' => $datos['estado'],
                'fecha_ingreso' => $datos['fecha_ingreso'],
                'observaciones' => $datos['observaciones'],
                
            ]);

            if ($resultado === false) {
                $this->lanzarExcepcion("Error al guardar: " . esc_html($wpdb->last_error));
            }

            return $this->listarBomberos($datos);
         } catch (Exception $e) {
            $this->manejarExcepcion($e, $datos);
        }
    }
}
