<?php

function insertar_datos_demo()
{
    global $wpdb;
    $empresa_table = $wpdb->prefix . 'empresas';
    $inspecciones_table = $wpdb->prefix . 'inspecciones';
    $cursos_table = $wpdb->prefix . 'cursos';
    $pqrs_table = $wpdb->prefix . 'pqrs';
    $inscripciones_table= $wpdb->prefix . 'inscripciones';
    $bomberos_table=$wpdb->prefix . 'bomberos';

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

    $wpdb->query("
        INSERT INTO $inscripciones_table 
        (id_curso, nombre_asistente, email_asistente, telefono_asistente, estado_inscripcion, notas)
        VALUES
        (1, 'Luis Mendoza', 'luis.mendoza@example.com', '3001112233', 'Registrada', 'Asistente puntual'),
        (2, 'Sandra López', 'sandra.lopez@example.com', '3002223344', 'Pendiente', NULL),
        (3, 'Andrés Pérez', 'andres.perez@example.com', '3003334455', 'Cerrada', 'Requiere material adicional'),
        (4, 'Marcela Torres', 'marcela.torres@example.com', '3004445566', 'Cerrada', 'No podrá asistir'),
        (5, 'Raúl García', 'raul.garcia@example.com', '3005556677', 'Pendiente', NULL),
        (6, 'Natalia Sánchez', 'natalia.sanchez@example.com', '3006667788', 'Registrada', NULL),
        (7, 'Diego Vargas', 'diego.vargas@example.com', '3007778899', 'Pendiente', NULL),
        (8, 'Paola Romero', 'paola.romero@example.com', '3008889900', 'Registrada', 'Vegetariana'),
        (9, 'Esteban Cruz', 'esteban.cruz@example.com', '3009990011', 'Registrada', NULL),
        (10, 'Diana Castro', 'diana.castro@example.com', '3010001122', 'Pendiente', NULL),
        (1, 'José Rojas', 'jose.rojas@example.com', '3011112233', 'Pendiente', NULL),
        (2, 'Laura Morales', 'laura.morales@example.com', '3012223344', 'Pendiente', NULL),
        (3, 'Camilo Ayala', 'camilo.ayala@example.com', '3013334455', 'Registrada', NULL),
        (4, 'Lucía Peña', 'lucia.pena@example.com', '3014445566', 'Rgistrada', NULL),
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
        ('Paula Díaz', '3146789012', 'paula.diaz@example.com', 'Reclamo', 'No se respetaron los términos del contrato.', 'Hemos revisado su caso y procederemos a corregir la situación.', 'Resuelto', NOW() - INTERVAL 2 DAY),
        ('Jorge Ruiz', '3157890123', 'jorge.ruiz@example.com', 'Petición', 'Solicito una copia del reglamento interno.', 'El reglamento ha sido enviado a su correo.', 'Resuelto', NOW() - INTERVAL 5 DAY),
        ('Camila Torres', '3168901234', 'camila.torres@example.com', 'Queja', 'El personal de atención al cliente fue grosero.', NULL, 'Pendiente', NOW()),
        ('Ricardo Mendoza', '3179012345', 'ricardo.mendoza@example.com', 'Reclamo', 'No me han hecho la devolución prometida.', NULL, 'Pendiente', NOW()),
        ('Laura Castillo', '3180123456', 'laura.castillo@example.com', 'Petición', 'Quisiera conocer los requisitos para afiliarme.', NULL, 'Pendiente', NOW()) 
        ");
    
        $wpdb->query("
           INSERT INTO {$bomberos_table} (
                nombres, apellidos, tipo_documento, numero_documento, fecha_nacimiento,
                genero, direccion, telefono, email, grupo_sanguineo, rh, rango,
                estado, fecha_ingreso, observaciones ) VALUES
            ('Juan', 'Pérez', 'CC', '100000001', '1990-05-15', 'Masculino', 'Calle 10 #5-30', '3001234567', 'juan.perez@example.com', 'O', '+', 'Bombero', 'activo', '2020-01-10', ''),
            ('María', 'Gómez', 'CC', '100000002', '1985-03-22', 'Femenino', 'Cra 15 #23-45', '3012345678', 'maria.gomez@example.com', 'A', '+', 'Capitán', 'activo', '2019-06-20', ''),
            ('Carlos', 'Ramírez', 'TI', '100000003', '1998-11-05', 'Masculino', 'Av 1 #2-33', '3023456789', 'carlos.ramirez@example.com', 'B', '-', 'Teniente', 'activo', '2021-03-18', ''),
            ('Luisa', 'Fernández', 'CC', '100000004', '1992-07-11', 'Femenino', 'Calle 20 #10-22', '3034567890', 'luisa.fernandez@example.com', 'AB', '+', 'Bombero', 'activo', '2022-08-10', ''),
            ('Andrés', 'López', 'CE', '100000005', '1980-01-25', 'Masculino', 'Cra 8 #12-55', '3045678901', 'andres.lopez@example.com', 'O', '-', 'Mayor', 'activo', '2018-10-01', ''),
            ('Diana', 'Martínez', 'CC', '100000006', '1995-04-30', 'Femenino', 'Calle 45 #30-16', '3056789012', 'diana.martinez@example.com', 'A', '+', 'Bombero', 'activo', '2020-04-15', ''),
            ('Jorge', 'Torres', 'TI', '100000007', '1993-09-19', 'Masculino', 'Av 6 #9-70', '3067890123', 'jorge.torres@example.com', 'B', '+', 'Sargento', 'activo', '2017-12-12', ''),
            ('Paola', 'Mendoza', 'CC', '100000008', '1996-02-14', 'Femenino', 'Cra 18 #40-18', '3078901234', 'paola.mendoza@example.com', 'AB', '-', 'Teniente', 'activo', '2021-05-05', ''),
            ('Ricardo', 'Vargas', 'CE', '100000009', '1988-08-08', 'Masculino', 'Calle 50 #25-12', '3089012345', 'ricardo.vargas@example.com', 'O', '+', 'Capitán', 'activo', '2016-09-22', ''),
            ('Natalia', 'Cárdenas', 'CC', '100000010', '1991-12-03', 'Femenino', 'Cra 22 #15-48', '3090123456', 'natalia.cardenas@example.com', 'A', '-', 'Bombero', 'activo', '2019-11-30', '');
            ");



    echo "Tablas recreadas e información cargada correctamente.";
}
