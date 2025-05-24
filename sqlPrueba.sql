-- primero verifique el prefijo de su tabla
-- Insertar 5 empresas en la tabla wp_empresa
INSERT INTO wp_empresa (nit, razon_social, direccion, barrio, representante_legal, email) VALUES
('900123456-1', 'Constructora Sol S.A.S.', 'Calle 45 # 23-10', 'Centro', 'Juan Pérez Gómez', 'juan.perez@constructora-sol.com'),
('800654321-2', 'Distribuidora Luna Ltda.', 'Carrera 12 # 67-89', 'Laureles', 'María López Restrepo', 'maria.lopez@distribuidora-luna.com'),
('901234567-3', 'Tecnologías Avanzadas S.A.', 'Avenida 33 # 15-20', 'El Poblado', 'Carlos Ramírez Díaz', 'carlos.ramirez@tecnologias-avanzadas.com'),
('700987654-4', 'Comercializadora Estrella', 'Calle 10 # 50-30', 'Manrique', 'Ana María Torres', 'ana.torres@comercializadora-estrella.com'),
('850456789-5', 'Industrias Metálicas Beta', 'Carrera 25 # 8-15', 'Belén', 'Luis Fernando Castro', 'luis.castro@industrias-beta.com');

-- Insertar 2 inspecciones por cada empresa en la tabla wp_inspeccion
INSERT INTO wp_inspeccion (id_empresa, fecha_registro, fecha_programada, fecha_expedicion, estado, nombre_encargado, telefono_encargado) VALUES
-- Inspecciones para Constructora Sol S.A.S. (id_empresa = 1)
(1, '2025-05-24 09:00:00', '2025-06-01', NULL, 'Registrada', 'Pedro Gómez', '3001234567'),
(1, '2025-05-24 10:00:00', '2025-06-15', '2025-06-16', 'Cerrada', 'Sofía Martínez', '3009876543'),
-- Inspecciones para Distribuidora Luna Ltda. (id_empresa = 2)
(2, '2025-05-24 11:00:00', '2025-06-02', NULL, 'En Proceso', 'Andrés Salazar', '3012345678'),
(2, '2025-05-24 12:00:00', '2025-06-20', NULL, 'Registrada', 'Laura Sánchez', '3018765432'),
-- Inspecciones para Tecnologías Avanzadas S.A. (id_empresa = 3)
(3, '2025-05-24 13:00:00', '2025-06-05', '2025-06-06', 'Cerrada', 'Diego Fernández', '3023456789'),
(3, '2025-05-24 14:00:00', '2025-06-25', NULL, 'Registrada', 'Camila Rojas', '3027654321'),
-- Inspecciones para Comercializadora Estrella (id_empresa = 4)
(4, '2025-05-24 15:00:00', '2025-06-03', NULL, 'Registrada', 'Felipe Vargas', '3034567890'),
(4, '2025-05-24 16:00:00', '2025-06-18', NULL, 'En Proceso', 'Valentina Ortiz', '3031234567'),
-- Inspecciones para Industrias Metálicas Beta (id_empresa = 5)
(5, '2025-05-24 17:00:00', '2025-06-04', NULL, 'Registrada', 'Santiago Mejía', '3045678901'),
(5, '2025-05-24 18:00:00', '2025-06-22', '2025-06-23', 'Cerrada', 'Paula Ramírez', '3049876543');