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
        ('850450789-5', 'Industrias Metálicas Beta', 'Carrera 25 # 8-15', 'Belén', 'Luis Fernando Castro', 'luis.castro@industrias-beta.com')
    ");

    // Insertar inspecciones (usamos IDs 1 a 5 porque son consecutivos tras la inserción)
    $wpdb->query("
        INSERT INTO $inspecciones_table (id_empresa, fecha_registro, fecha_programada, fecha_expedicion, estado, nombre_encargado, telefono_encargado) VALUES
        (1, '2025-05-24 09:00:00', '2025-06-01', NULL, 'Registrada', 'Pedro Gómez', '3001234567'),
       (5, '2025-05-24 17:00:00', '2025-06-04', NULL, 'Registrada', 'Santiago Mejía', '3045678901'),
        (5, '2025-05-24 18:00:00', '2025-06-22', '2025-06-23', 'Cerrada', 'Paula Ramírez', '3049876543')
    ");

    // Insertar cursos
    $wpdb->query("
        INSERT INTO $cursos_table  (nombre_curso, descripcion, fecha_inicio, duracion_horas, instructor, lugar, capacidad_maxima, estado) VALUES
        ('Curso de Rescate en vehículos', 'Curso básico de Rescate en vehículos.', '2025-06-01', 8, 'Juan Pérez', 'Estación de Bomberos Central', 30, 'Planificado'),
        ('Curso manejo de extintores', 'Entrenamiento de  manejo de extintores.', '2025-06-05', 12, 'María Gómez', 'Estación de Bomberos Norte', 20, 'Planificado'),
        ('Curso de Operaciones de rescate con cuerdas', 'Operaciones de rescate con cuerdas', '2025-06-10', 10, 'Carlos Ruiz', 'Estación de Bomberos Sur', 25, 'Planificado'),
        ('Curso de capacitación Primeros Auxilios', 'Capacitación Primeros Auxilios.', '2025-06-12', 6, 'Lucía Martínez', 'Estación de Bomberos Este', 15, 'En_curso'),
        ('Curso Brigadas de Emergencias Básicas', 'Brigadas de Emergencias Básicas.', '2025-06-15', 9, 'Diego Torres', 'Estación de Bomberos Central', 40, 'Planificado')
        ");

    $wpdb->query("
        INSERT INTO $inscripciones_table 
        (id_curso, nombre_asistente, email_asistente, telefono_asistente, estado_inscripcion, notas)
        VALUES
        (1, 'Luis Mendoza', 'luis.mendoza@example.com', '3001112233', 'Registrada', 'Asistente puntual'),
        (10, 'Carolina Mejía', 'carolina.mejia@example.com', '3020001122', 'Pendiente', NULL);
        ");
    $wpdb->query("
        INSERT INTO $pqrs_table (nombre, telefono, email, tipo_solicitud, contenido, respuesta, estado_solicitud, fecha_registro)
        VALUES
        ('Juan Pérez', '3001234567', 'juan.perez@example.com', 'Petición', 'Solicito información sobre los horarios de atención.', NULL, 'Pendiente', NOW()),
      ('Laura Castillo', '3180123456', 'laura.castillo@example.com', 'Petición', 'Quisiera conocer los requisitos para afiliarme.', NULL, 'Pendiente', NOW()) 
        ");
    
         $wpdb->query("
           INSERT INTO {$bomberos_table} (
                nombres, apellidos, tipo_documento, numero_documento, fecha_nacimiento,
                genero, direccion, telefono, email, grupo_sanguineo, rango,
                estado, fecha_ingreso, observaciones ) VALUES
            ('Juan', 'Pérez', 'CC', '100000001', '1990-05-15', 'Masculino', 'Calle 10 #5-30', '3001234567', 'juan.perez@example.com', 'O+', 'Bombero', 'activo', '2020-01-10', ''),
            ('María', 'Gómez', 'CC', '100000002', '1985-03-22', 'Femenino', 'Cra 15 #23-45', '3012345678', 'maria.gomez@example.com', 'A+', 'Capitán', 'activo', '2019-06-20', ''),
            ('Carlos', 'Ramírez', 'TI', '100000003', '1998-11-05', 'Masculino', 'Av 1 #2-33', '3023456789', 'carlos.ramirez@example.com', 'B-', 'Teniente', 'activo', '2021-03-18', ''),
            ('Luisa', 'Fernández', 'CC', '100000004', '1992-07-11', 'Femenino', 'Calle 20 #10-22', '3034567890', 'luisa.fernandez@example.com', 'AB+', 'Bombero', 'activo', '2022-08-10', ''),
            ('Andrés', 'López', 'CE', '100000005', '1980-01-25', 'Masculino', 'Cra 8 #12-55', '3045678901', 'andres.lopez@example.com', 'O-', 'Mayor', 'activo', '2018-10-01', ''),
            ('Diana', 'Martínez', 'CC', '100000006', '1995-04-30', 'Femenino', 'Calle 45 #30-16', '3056789012', 'diana.martinez@example.com', 'A+', 'Bombero', 'activo', '2020-04-15', ''),
            ('Jorge', 'Torres', 'TI', '100000007', '1993-09-19', 'Masculino', 'Av 6 #9-70', '3067890123', 'jorge.torres@example.com', 'B+', 'Sargento', 'activo', '2017-12-12', ''),
            ('Paola', 'Mendoza', 'CC', '100000008', '1996-02-14', 'Femenino', 'Cra 18 #40-18', '3078901234', 'paola.mendoza@example.com', 'AB-', 'Teniente', 'activo', '2021-05-05', ''),
            ('Ricardo', 'Vargas', 'CE', '100000009', '1988-08-08', 'Masculino', 'Calle 50 #25-12', '3089012345', 'ricardo.vargas@example.com', 'O+', 'Capitán', 'activo', '2016-09-22', ''),
            ('Natalia', 'Cárdenas', 'CC', '100000010', '1991-12-03', 'Femenino', 'Cra 22 #15-48', '3090123456', 'natalia.cardenas@example.com', 'A-', 'Bombero', 'activo', '2019-11-30', '');
            ");



    echo "Tablas recreadas e información cargada correctamente.";
}
