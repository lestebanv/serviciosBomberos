<?php
function crear_tablas_plugin_bomberos() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Nombre de las tablas con prefijo
    $tabla_empresas = $wpdb->prefix . 'empresa';
    $tabla_inspecciones = $wpdb->prefix . 'inspeccion';

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
        PRIMARY KEY (id_inspeccion),
        FOREIGN KEY (id_empresa) REFERENCES $tabla_empresas(id_empresa) ON DELETE CASCADE
    ) $charset_collate;";

    // Incluir archivo necesario para dbDelta
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Crear las tablas
    dbDelta($sql_empresas);
    dbDelta($sql_inspecciones);
}
?>