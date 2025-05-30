<?php

function insertar_datos_demo() {
    global $wpdb;
    $empresa_table     = $wpdb->prefix . 'empresa';
    $inspeccion_table  = $wpdb->prefix . 'inspeccion';
    $cursos_table      = $wpdb->prefix . 'cursos';
    
    // Insertar empresas
    $wpdb->query("
        INSERT INTO $empresa_table (nit, razon_social, direccion, barrio, representante_legal, email) VALUES
        ('900123456-1', 'Constructora Sol S.A.S.', 'Calle 45 # 23-10', 'Centro', 'Juan Pérez Gómez', 'juan.perez@constructora-sol.com'),
        ('800654321-2', 'Distribuidora Luna Ltda.', 'Carrera 12 # 67-89', 'Laureles', 'María López Restrepo', 'maria.lopez@distribuidora-luna.com'),
        ('901234567-3', 'Tecnologías Avanzadas S.A.', 'Avenida 33 # 15-20', 'El Poblado', 'Carlos Ramírez Díaz', 'carlos.ramirez@tecnologias-avanzadas.com'),
        ('700987654-4', 'Comercializadora Estrella', 'Calle 10 # 50-30', 'Manrique', 'Ana María Torres', 'ana.torres@comercializadora-estrella.com'),
        ('850456789-5', 'Industrias Metálicas Beta', 'Carrera 25 # 8-15', 'Belén', 'Luis Fernando Castro', 'luis.castro@industrias-beta.com')
    ");

    // Insertar inspecciones (usamos IDs 1 a 5 porque son consecutivos tras la inserción)
    $wpdb->query("
        INSERT INTO $inspeccion_table (id_empresa, fecha_registro, fecha_programada, fecha_expedicion, estado, nombre_encargado, telefono_encargado) VALUES
        (1, '2025-05-24 09:00:00', '2025-06-01', NULL, 'Registrada', 'Pedro Gómez', '3001234567'),
        (1, '2025-05-24 10:00:00', '2025-06-15', '2025-06-16', 'Cerrada', 'Sofía Martínez', '3009876543'),
        (2, '2025-05-24 11:00:00', '2025-06-02', NULL, 'En Proceso', 'Andrés Salazar', '3012345678'),
        (2, '2025-05-24 12:00:00', '2025-06-20', NULL, 'Registrada', 'Laura Sánchez', '3018765432'),
        (3, '2025-05-24 13:00:00', '2025-06-05', '2025-06-06', 'Cerrada', 'Diego Fernández', '3023456789'),
        (3, '2025-05-24 14:00:00', '2025-06-25', NULL, 'Registrada', 'Camila Rojas', '3027654321'),
        (4, '2025-05-24 15:00:00', '2025-06-03', NULL, 'Registrada', 'Felipe Vargas', '3034567890'),
        (4, '2025-05-24 16:00:00', '2025-06-18', NULL, 'En Proceso', 'Valentina Ortiz', '3031234567'),
        (5, '2025-05-24 17:00:00', '2025-06-04', NULL, 'Registrada', 'Santiago Mejía', '3045678901'),
        (5, '2025-05-24 18:00:00', '2025-06-22', '2025-06-23', 'Cerrada', 'Paula Ramírez', '3049876543')
    ");

    // Insertar cursos
    $wpdb->query("
        INSERT INTO $cursos_table (nombre_curso, descripcion, fecha_inicio, duracion_horas, instructor, lugar, capacidad_maxima, estado) VALUES
        ('Prevención de Incendios', 'Técnicas para identificar y mitigar riesgos de incendios en entornos residenciales y comerciales.', '2025-06-01', 24, 'Juan Morales', 'Estación de Bomberos Central', 20, 'planificado'),
        ('Primeros Auxilios Avanzados', 'Capacitación en técnicas de primeros auxilios para emergencias médicas en el campo.', '2025-06-10', 16, 'María López', 'Sala de Entrenamiento 1', 15, 'planificado'),
        ('Manejo de Equipos de Rescate', 'Uso y mantenimiento de herramientas de rescate hidráulico y mecánico.', '2025-06-15', 32, 'Carlos Vargas', 'Patio de Maniobras', 12, 'en_curso'),
        ('Respuesta a Materiales Peligrosos', 'Protocolos para manejar derrames y exposiciones a sustancias peligrosas.', '2025-07-01', 40, 'Ana Ramírez', 'Centro de Simulación', 10, 'planificado'),
        ('Técnicas de Extinción de Incendios', 'Entrenamiento en métodos de extinción para diferentes tipos de fuegos.', '2025-07-10', 48, 'Luis Fernández', 'Torre de Entrenamiento', 18, 'en_curso'),
        ('Rescate en Alturas', 'Técnicas de rescate en estructuras elevadas y espacios confinados.', '2025-08-01', 36, 'Sofía Torres', 'Polígono de Entrenamiento', 8, 'planificado'),
        ('Manejo de Vehículos de Emergencia', 'Conducción segura y eficiente de vehículos de bomberos en situaciones críticas.', '2025-08-15', 20, 'Pedro Gómez', 'Pista de Conducción', 10, 'planificado'),
        ('Gestión de Emergencias Masivas', 'Coordinación de respuestas ante desastres naturales y emergencias masivas.', '2025-09-01', 64, 'Elena Díaz', 'Aula de Capacitación', 25, 'finalizado'),
        ('Mantenimiento de Equipos SCBA', 'Uso y mantenimiento de equipos de respiración autónoma.', '2025-09-10', 16, 'Miguel Ángel', 'Taller de Equipos', 15, 'planificado'),
        ('Entrenamiento en Incendios Forestales', 'Estrategias para combatir incendios en entornos naturales.', '2025-10-01', 56, 'Clara Sánchez', 'Campo de Entrenamiento Externo', 12, 'cancelado')
    ");

    $pqr_table      = $wpdb->prefix . 'pqr';
    $wpdb->query("
                INSERT INTO $pqr_table (nombre, telefono, email, tipo_solicitud, contenido, respuesta, estado_solicitud, fecha_registro)
                VALUES
                ('Juan Pérez', '3001234567', 'juan.perez@example.com', 'Petición', 'Solicito información sobre los horarios de atención.', NULL, 'Pendiente', NOW()),
                ('María Gómez', '3102345678', 'maria.gomez@example.com', 'Queja', 'El servicio recibido fue deficiente y poco profesional.', NULL, 'Pendiente', NOW()),
                ('Carlos Rodríguez', '3113456789', 'carlos.rodriguez@example.com', 'Reclamo', 'Mi factura presenta un valor erróneo. Solicito revisión.', NULL, 'Pendiente', NOW()),
                ('Ana Martínez', '3124567890', 'ana.martinez@example.com', 'Petición', '¿Pueden indicarme cómo solicitar un certificado?', NULL, 'Pendiente', NOW()),
                ('Luis Herrera', '3135678901', 'luis.herrera@example.com', 'Queja', 'No responden los correos enviados desde hace una semana.', NULL, 'Pendiente', NOW()),
                ('Paula Díaz', '3146789012', 'paula.diaz@example.com', 'Reclamo', 'No se respetaron los términos del contrato.', 'Hemos revisado su caso y procederemos a corregir la situación.', 'Resuelto', NOW() - INTERVAL 2 DAY),
                ('Jorge Ruiz', '3157890123', 'jorge.ruiz@example.com', 'Petición', 'Solicito una copia del reglamento interno.', 'El reglamento ha sido enviado a su correo.', 'Resuelto', NOW() - INTERVAL 5 DAY),
                ('Camila Torres', '3168901234', 'camila.torres@example.com', 'Queja', 'El personal de atención al cliente fue grosero.', NULL, 'Pendiente', NOW()),
                ('Ricardo Mendoza', '3179012345', 'ricardo.mendoza@example.com', 'Reclamo', 'No me han hecho la devolución prometida.', NULL, 'Pendiente', NOW()),
                ('Laura Castillo', '3180123456', 'laura.castillo@example.com', 'Petición', 'Quisiera conocer los requisitos para afiliarme.', NULL, 'Pendiente', NOW()) 
                ");


    echo "Tablas recreadas e información cargada correctamente.";
}
