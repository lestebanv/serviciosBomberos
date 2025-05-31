<?php

function desactivar_plugin_bomberos()
{
    // documentar esta linea para produccion
    limpiar_plugin_bomberos();
}


function limpiar_plugin_bomberos(){
    global $wpdb;
    $empresas_table = $wpdb->prefix . 'empresas';
    $inspecciones_table = $wpdb->prefix . 'inspecciones';
    $cursos_table = $wpdb->prefix . 'cursos';
    $pqrs_table = $wpdb->prefix . 'pqrs';
    $inscripciones_table = $wpdb->prefix . 'inscripciones';

    // Eliminar las tablas si existen
    $wpdb->query("DROP TABLE IF EXISTS $inspecciones_table");
    $wpdb->query("DROP TABLE IF EXISTS $empresas_table");
    $wpdb->query("DROP TABLE IF EXISTS $inscripciones_table");
    $wpdb->query("DROP TABLE IF EXISTS $cursos_table");
    $wpdb->query("DROP TABLE IF EXISTS $pqrs_table");
}
?>