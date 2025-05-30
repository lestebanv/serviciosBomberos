<?php

function desactivar_plugin_bomberos(){
    // documentar esta linea para produccion
    limpiar_plugin_bomberos();
};

function limpiar_plugin_bomberos(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $empresa_table     = $wpdb->prefix . 'empresa';
    $inspeccion_table  = $wpdb->prefix . 'inspeccion';
    $cursos_table      = $wpdb->prefix . 'cursos';
    $pqr_table      = $wpdb->prefix . 'pqr';

    // Eliminar las tablas si existen
    $wpdb->query("DROP TABLE IF EXISTS $inspeccion_table");
    $wpdb->query("DROP TABLE IF EXISTS $empresa_table");
    $wpdb->query("DROP TABLE IF EXISTS $cursos_table");
    $wpdb->query("DROP TABLE IF EXISTS $pqr_table");
}
?>