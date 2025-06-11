<?php
require_once  plugin_dir_path(__FILE__).'insertarDemo.php';
function activar_plugin_bomberos(){
    crear_tablas_plugin_bomberos();
    // documentar esta linea para produccion
    insertar_datos_demo();
}

function crear_tablas_plugin_bomberos()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Nombre de las tablas con prefijo
    $tabla_empresas = $wpdb->prefix . 'empresas';
    $tabla_inspecciones = $wpdb->prefix . 'inspecciones';
    $tabla_cursos = $wpdb->prefix . 'cursos';
    $tabla_pqrs = $wpdb->prefix . 'pqrs';
    $tabla_inscripciones_cursos=$wpdb->prefix . 'inscripciones';
    $tabla_bomberos=$wpdb->prefix . 'bomberos';

    // SQL para crear tabla de empresas
    $sql_empresas = "CREATE TABLE $tabla_empresas (
        id_empresa INT NOT NULL AUTO_INCREMENT,
        nit VARCHAR(20) NOT NULL UNIQUE,
        razon_social VARCHAR(255) NOT NULL,
        direccion VARCHAR(255) NOT NULL,
        barrio VARCHAR(100) NOT NULL,
        representante_legal VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        PRIMARY KEY (id_empresa)
    ) $charset_collate;";

    // SQL para crear tabla de inspecciones
    $sql_inspecciones = "CREATE TABLE $tabla_inspecciones (
        id_inspeccion INT NOT NULL AUTO_INCREMENT,
        fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fecha_programada DATE DEFAULT NULL,
        fecha_expedicion DATE DEFAULT NULL,
        estado ENUM('Registrada', 'En Proceso', 'Cerrada') NOT NULL DEFAULT 'Registrada',
        nombre_encargado VARCHAR(255) NOT NULL,
        telefono_encargado VARCHAR(20) NOT NULL,
        id_empresa INT NOT NULL,
        id_bombero_asignado BIGINT(20) UNSIGNED DEFAULT NULL,
        PRIMARY KEY (id_inspeccion),
        FOREIGN KEY (id_empresa) REFERENCES $tabla_empresas(id_empresa) ON DELETE CASCADE,
        FOREIGN KEY (id_bombero_asignado) REFERENCES {$tabla_bomberos}(id_bombero) ON DELETE SET NULL
    ) $charset_collate;";

    
    $sql_cursos = "CREATE TABLE IF NOT EXISTS $tabla_cursos (
                    id_curso BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    nombre_curso VARCHAR(255) NOT NULL,
                    descripcion TEXT,
                    fecha_inicio DATE NOT NULL, -- Renombrado de fecha_prevista a fecha_inicio
                    duracion_horas INT(11),
                    instructor VARCHAR(100),
                    lugar VARCHAR(255),
                    capacidad_maxima INT(11),
                    estado ENUM('Planificado', 'En_curso', 'Finalizado', 'Cancelado') NOT NULL DEFAULT 'planificado',
                    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    fecha_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_curso)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
    $sql_inscripciones_cursos = "CREATE TABLE IF NOT EXISTS $tabla_inscripciones_cursos (
        id_inscripcion BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        id_curso BIGINT(20) UNSIGNED NOT NULL,
        nombre_asistente VARCHAR(255) NOT NULL,
        email_asistente VARCHAR(100) NOT NULL,
        telefono_asistente VARCHAR(20) DEFAULT NULL,
        fecha_inscripcion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        estado_inscripcion ENUM('Registrada', 'Pendiente','Cerrada') NOT NULL DEFAULT 'Registrada',
        notas TEXT DEFAULT NULL,
        PRIMARY KEY (id_inscripcion),
        FOREIGN KEY (id_curso) REFERENCES $tabla_cursos(id_curso) ON DELETE CASCADE,
        INDEX idx_email_asistente (email_asistente),
        INDEX idx_id_curso (id_curso)
    ) $charset_collate;";
   

        $sql_pqrs = "CREATE TABLE $tabla_pqrs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            nombre varchar(255) NOT NULL,
            telefono varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            tipo_solicitud varchar(50) NOT NULL,
            estado_solicitud ENUM('Registrada', 'Pendiente', 'Cerrada') NOT NULL DEFAULT 'Registrada',
            fecha_registro datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            contenido text NOT NULL,
            ip_address varchar(45) NOT NULL,
            respuesta text DEFAULT NULL,
            fecha_respuesta datetime DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

    $sql_bomberos = "CREATE TABLE {$tabla_bomberos} (
        id_bombero BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        nombres VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        tipo_documento VARCHAR(10) NOT NULL,
        numero_documento VARCHAR(20) NOT NULL UNIQUE,
        fecha_nacimiento DATE,
        genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
        direccion VARCHAR(255),
        telefono VARCHAR(20),
        email VARCHAR(100),
        grupo_sanguineo VARCHAR(5),
        rh VARCHAR(1),
        rango VARCHAR(50),
        estado VARCHAR(20) DEFAULT 'activo',
        fecha_ingreso DATE,
        observaciones TEXT,
        PRIMARY KEY (id_bombero)
    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // Crear las tablas
    dbDelta($sql_empresas);
    dbDelta($sql_bomberos); // Mover bomberos antes de inspecciones para la llave foránea
    dbDelta($sql_inspecciones);
    dbDelta($sql_cursos);
    dbDelta($sql_pqrs);
    dbDelta($sql_inscripciones_cursos);
}
?>