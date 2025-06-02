<?php
if (!defined('ABSPATH'))   exit;

function insertar_datos_demo()
{
    global $wpdb;
    $empresa_table = $wpdb->prefix . 'empresas';
    $inspecciones_table = $wpdb->prefix . 'inspecciones';
    $cursos_table = $wpdb->prefix . 'cursos';
    $pqrs_table = $wpdb->prefix . 'pqrs';
    $inscripciones_table= $wpdb->prefix . 'inscripciones';

    // Insertar empresas
    $wpdb->query("
        INSERT INTO $empresa_table (nit, razon_social, direccion, barrio, representante_legal, email) VALUES
        ('900123456-1', 'Constructora Sol S.A.S.', 'Calle 45 # 23-10', 'Centro', 'Juan Pérez Gómez', 'juan.perez@constructora-sol.com'),
        ('800654321-2', 'Distribuidora Luna Ltda.', 'Carrera 12 # 67-89', 'Laureles', 'María López Restrepo', 'maria.lopez@distribuidora-luna.com'),
        ('901234567-3', 'Tecnologías Avanzadas S.A.', 'Avenida 33 # 15-20', 'El Poblado', 'Carlos Ramírez Díaz', 'carlos.ramirez@tecnologias-avanzadas.com'),
        ('700987654-4', 'Comercializadora Estrella', 'Calle 10 # 50-30', 'Manrique', 'Ana María Torres', 'ana.torres@comercializadora-estrella.com'),
        ('850456789-5', 'Industrias Metálicas Beta', 'Carrera 25 # 8-15', 'Belén', 'Luis Fernando Castro', 'luis.castro@industrias-beta.com'),
        ('900123456-6', 'Constructora Sol S.A.S.', 'Calle 45 # 23-10', 'Centro', 'Juan Pérez Gómez', 'juan.perez@constructora-sol.com'),
        ('800654321-7', 'Distribuidora Luna Ltda.', 'Carrera 12 # 67-89', 'Laureles', 'María López Restrepo', 'maria.lopez@distribuidora-luna.com'),
        ('901234567-9', 'Tecnologías Avanzadas S.A.', 'Avenida 33 # 15-20', 'El Poblado', 'Carlos Ramírez Díaz', 'carlos.ramirez@tecnologias-avanzadas.com'),
        ('700907654-4', 'Comercializadora Estrella', 'Calle 10 # 50-30', 'Manrique', 'Ana María Torres', 'ana.torres@comercializadora-estrella.com'),
        ('850450789-5', 'Industrias Metálicas Beta', 'Carrera 25 # 8-15', 'Belén', 'Luis Fernando Castro', 'luis.castro@industrias-beta.com')
    ");

    // Insertar inspecciones (usamos IDs 1 a 5 porque son consecutivos tras la inserción)
    $wpdb->query("
        INSERT INTO $inspecciones_table (id_empresa, fecha_registro, fecha_programada, fecha_expedicion, estado, nombre_encargado, telefono_encargado) VALUES
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
        INSERT INTO $cursos_table  (nombre_curso, descripcion, fecha_inicio, duracion_horas, instructor, lugar, capacidad_maxima, estado) VALUES
        ('Curso de primeros auxilios', 'Curso básico de primeros auxilios.', '2025-06-01', 8, 'Juan Pérez', 'Estación de Bomberos Central', 30, 'Planificado'),
        ('Técnicas de rescate en altura', 'Entrenamiento intensivo en técnicas de rescate vertical.', '2025-06-05', 12, 'María Gómez', 'Estación de Bomberos Norte', 20, 'Planificado'),
        ('Manejo de materiales peligrosos', 'Identificación y tratamiento de materiales peligrosos.', '2025-06-10', 10, 'Carlos Ruiz', 'Estación de Bomberos Sur', 25, 'Planificado'),
        ('Uso avanzado de extintores', 'Técnicas de uso avanzado en fuego controlado.', '2025-06-12', 6, 'Lucía Martínez', 'Estación de Bomberos Este', 15, 'En_curso'),
        ('Evacuación en incendios', 'Protocolos de evacuación para situaciones de incendio.', '2025-06-15', 9, 'Diego Torres', 'Estación de Bomberos Central', 40, 'Planificado'),
        ('RCP y desfibrilación', 'Reanimación cardiopulmonar y uso de desfibrilador.', '2025-06-18', 7, 'Ana Ramírez', 'Estación de Bomberos Norte', 30, 'Planificado'),
        ('Taller de comunicación en crisis', 'Mejorar la comunicación en emergencias.', '2025-06-20', 5, 'Pedro Salinas', 'Estación de Bomberos Sur', 20, 'Planificado'),
        ('Simulacro de emergencia', 'Simulacro completo con evaluación.', '2025-06-22', 6, 'Laura Contreras', 'Estación de Bomberos Este', 25, 'Planificado'),
        ('Uso de mangueras de alta presión', 'Capacitación práctica con equipo real.', '2025-06-25', 4, 'Miguel Herrera', 'Estación de Bomberos Central', 18, 'En_curso'),
        ('Rescate acuático', 'Técnicas de rescate en ríos y piscinas.', '2025-06-28', 10, 'Valeria Núñez', 'Estación de Bomberos Norte', 12, 'Planificado');    
    ");

    // CORRECCIÓN: Corregido "Rgistrada" a "Registrada" y se asegura que los estados coincidan con el ENUM.
    // Se asume que el ENUM será ENUM('Registrada', 'Aprobada', 'Pendiente', 'Cerrada')
    $wpdb->query("
        INSERT INTO $inscripciones_table 
        (id_curso, nombre_asistente, email_asistente, telefono_asistente, estado_inscripcion, notas)
        VALUES
        (1, 'Luis Mendoza', 'luis.mendoza@example.com', '3001112233', 'Aprobada', 'Asistente puntual'), /* Cambiado a Aprobada para demo */
        (2, 'Sandra López', 'sandra.lopez@example.com', '3002223344', 'Pendiente', NULL),
        (3, 'Andrés Pérez', 'andres.perez@example.com', '3003334455', 'Cerrada', 'Requiere material adicional'),
        (4, 'Marcela Torres', 'marcela.torres@example.com', '3004445566', 'Cerrada', 'No podrá asistir'),
        (5, 'Raúl García', 'raul.garcia@example.com', '3005556677', 'Pendiente', NULL),
        (6, 'Natalia Sánchez', 'natalia.sanchez@example.com', '3006667788', 'Registrada', NULL),
        (7, 'Diego Vargas', 'diego.vargas@example.com', '3007778899', 'Pendiente', NULL),
        (8, 'Paola Romero', 'paola.romero@example.com', '3008889900', 'Aprobada', 'Vegetariana'), /* Cambiado a Aprobada para demo */
        (9, 'Esteban Cruz', 'esteban.cruz@example.com', '3009990011', 'Registrada', NULL),
        (10, 'Diana Castro', 'diana.castro@example.com', '3010001122', 'Pendiente', NULL),
        (1, 'José Rojas', 'jose.rojas@example.com', '3011112233', 'Pendiente', NULL),
        (2, 'Laura Morales', 'laura.morales@example.com', '3012223344', 'Pendiente', NULL),
        (3, 'Camilo Ayala', 'camilo.ayala@example.com', '3013334455', 'Registrada', NULL),
        (4, 'Lucía Peña', 'lucia.pena@example.com', '3014445566', 'Registrada', NULL), /* Corregido de 'Rgistrada' */
        (5, 'Julián Herrera', 'julian.herrera@example.com', '3015556677', 'Cerrada', 'Por enfermedad'),
        (6, 'Tatiana Gómez', 'tatiana.gomez@example.com', '3016667788', 'Pendiente', NULL),
        (7, 'Cristian Beltrán', 'cristian.beltran@example.com', '3017778899', 'Pendiente', NULL),
        (8, 'Vanessa Díaz', 'vanessa.diaz@example.com', '3018889900', 'Cerrada', NULL),
        (9, 'Felipe Acosta', 'felipe.acosta@example.com', '3019990011', 'Cerrada', NULL),
        (10, 'Carolina Mejía', 'carolina.mejia@example.com', '3020001122', 'Pendiente', NULL);
        ");
    $wpdb->query("
        INSERT INTO $pqrs_table (nombre, telefono, email, tipo_solicitud, contenido, respuesta, estado_solicitud, fecha_registro)
        VALUES
        ('Juan Pérez', '3001234567', 'juan.perez@example.com', 'Petición', 'Solicito información sobre los horarios de atención.', NULL, 'Pendiente', NOW()),
        ('María Gómez', '3102345678', 'maria.gomez@example.com', 'Queja', 'El servicio recibido fue deficiente y poco profesional.', NULL, 'Pendiente', NOW()),
        ('Carlos Rodríguez', '3113456789', 'carlos.rodriguez@example.com', 'Reclamo', 'Mi factura presenta un valor erróneo. Solicito revisión.', NULL, 'Pendiente', NOW()),
        ('Ana Martínez', '3124567890', 'ana.martinez@example.com', 'Petición', '¿Pueden indicarme cómo solicitar un certificado?', NULL, 'Pendiente', NOW()),
        ('Luis Herrera', '3135678901', 'luis.herrera@example.com', 'Queja', 'No responden los correos enviados desde hace una semana.', NULL, 'Pendiente', NOW()),
        ('Paula Díaz', '3146789012', 'paula.diaz@example.com', 'Reclamo', 'No se respetaron los términos del contrato.', 'Hemos revisado su caso y procederemos a corregir la situación.', 'Cerrada', NOW() - INTERVAL 2 DAY), /* Cambiado a Cerrada para coincidir con ENUM */
        ('Jorge Ruiz', '3157890123', 'jorge.ruiz@example.com', 'Petición', 'Solicito una copia del reglamento interno.', 'El reglamento ha sido enviado a su correo.', 'Cerrada', NOW() - INTERVAL 5 DAY), /* Cambiado a Cerrada */
        ('Camila Torres', '3168901234', 'camila.torres@example.com', 'Queja', 'El personal de atención al cliente fue grosero.', NULL, 'Pendiente', NOW()),
        ('Ricardo Mendoza', '3179012345', 'ricardo.mendoza@example.com', 'Reclamo', 'No me han hecho la devolución prometida.', NULL, 'Pendiente', NOW()),
        ('Laura Castillo', '3180123456', 'laura.castillo@example.com', 'Petición', 'Quisiera conocer los requisitos para afiliarme.', NULL, 'Pendiente', NOW()) 
        ");


    echo "Tablas recreadas e información cargada correctamente.";
}