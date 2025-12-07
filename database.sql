-- Base de datos: Sistema de Gestión APR (Agua Potable Rural)
-- Versión: 1.0
-- Fecha: 2025

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `sistema_apr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_apr`;

-- ============================================
-- Tabla: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('Administrador','Operador','Tesorero','Secretario','usuario') NOT NULL DEFAULT 'usuario',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_rol` (`rol`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: socios (Usuarios del servicio de agua)
-- ============================================
CREATE TABLE IF NOT EXISTS `socios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_socio` varchar(20) NOT NULL UNIQUE,
  `rut` varchar(12) NOT NULL UNIQUE,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `sector` varchar(100) DEFAULT NULL COMMENT 'Sector o zona geográfica',
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `tipo_cliente` enum('residencial','comercial','industrial') NOT NULL DEFAULT 'residencial',
  `numero_medidor` varchar(50) DEFAULT NULL,
  `estado` enum('activo','suspendido','moroso','desconectado') NOT NULL DEFAULT 'activo',
  `fecha_ingreso` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero_socio` (`numero_socio`),
  KEY `idx_rut` (`rut`),
  KEY `idx_nombre` (`nombre`, `apellido_paterno`),
  KEY `idx_estado` (`estado`),
  KEY `idx_sector` (`sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: lecturas (Lecturas mensuales de medidores)
-- ============================================
CREATE TABLE IF NOT EXISTS `lecturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_socio` int(11) NOT NULL,
  `mes` varchar(7) NOT NULL COMMENT 'Formato: YYYY-MM',
  `lectura_anterior` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lectura_actual` decimal(10,2) NOT NULL,
  `consumo_m3` decimal(10,2) NOT NULL COMMENT 'Metros cúbicos consumidos',
  `fecha_lectura` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `id_usuario_registro` int(11) DEFAULT NULL COMMENT 'Usuario que registró la lectura',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_socio_mes` (`id_socio`, `mes`),
  KEY `idx_socio` (`id_socio`),
  KEY `idx_mes` (`mes`),
  KEY `idx_fecha_lectura` (`fecha_lectura`),
  CONSTRAINT `fk_lecturas_socio` FOREIGN KEY (`id_socio`) REFERENCES `socios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lecturas_usuario` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: tarifas (Tarifas de cobro por consumo)
-- ============================================
CREATE TABLE IF NOT EXISTS `tarifas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo_cliente` enum('residencial','comercial','industrial') NOT NULL,
  `consumo_minimo` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'M3 incluidos en tarifa base',
  `tarifa_base` decimal(10,2) NOT NULL COMMENT 'Cargo fijo mensual',
  `precio_m3_excedente` decimal(10,2) NOT NULL COMMENT 'Precio por m3 adicional',
  `vigente_desde` date NOT NULL,
  `vigente_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo_cliente`),
  KEY `idx_vigencia` (`vigente_desde`, `vigente_hasta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: boletas (Boletas de cobro mensuales)
-- ============================================
CREATE TABLE IF NOT EXISTS `boletas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_boleta` varchar(50) NOT NULL UNIQUE,
  `id_socio` int(11) NOT NULL,
  `id_lectura` int(11) DEFAULT NULL,
  `mes` varchar(7) NOT NULL COMMENT 'Formato: YYYY-MM',
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `consumo_m3` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cargo_fijo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cargo_consumo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `otros_cargos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuentos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagada','vencida','anulada') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero_boleta` (`numero_boleta`),
  KEY `idx_socio` (`id_socio`),
  KEY `idx_lectura` (`id_lectura`),
  KEY `idx_mes` (`mes`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `fk_boletas_socio` FOREIGN KEY (`id_socio`) REFERENCES `socios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_boletas_lectura` FOREIGN KEY (`id_lectura`) REFERENCES `lecturas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: pagos (Pagos realizados por los socios)
-- ============================================
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_recibo` varchar(50) NOT NULL UNIQUE,
  `id_boleta` int(11) NOT NULL,
  `id_socio` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','cheque','debito','credito') NOT NULL,
  `numero_comprobante` varchar(100) DEFAULT NULL COMMENT 'Número de transferencia, cheque, etc.',
  `observaciones` text DEFAULT NULL,
  `id_usuario_registro` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero_recibo` (`numero_recibo`),
  KEY `idx_boleta` (`id_boleta`),
  KEY `idx_socio` (`id_socio`),
  KEY `idx_fecha_pago` (`fecha_pago`),
  CONSTRAINT `fk_pagos_boleta` FOREIGN KEY (`id_boleta`) REFERENCES `boletas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_socio` FOREIGN KEY (`id_socio`) REFERENCES `socios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: mantenciones (Registro de mantenciones de infraestructura)
-- ============================================
CREATE TABLE IF NOT EXISTS `mantenciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('preventiva','correctiva','emergencia') NOT NULL,
  `descripcion` text NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `fecha_programada` date DEFAULT NULL,
  `fecha_realizada` date DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT 0.00,
  `responsable` varchar(150) DEFAULT NULL,
  `estado` enum('programada','en_proceso','completada','cancelada') NOT NULL DEFAULT 'programada',
  `observaciones` text DEFAULT NULL,
  `id_usuario_registro` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_programada` (`fecha_programada`),
  CONSTRAINT `fk_mantenciones_usuario` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: incidentes (Reporte de problemas y emergencias)
-- ============================================
CREATE TABLE IF NOT EXISTS `incidentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('fuga','corte','baja_presion','contaminacion','otro') NOT NULL,
  `descripcion` text NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `id_socio_reporta` int(11) DEFAULT NULL,
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `estado` enum('reportado','en_atencion','resuelto','cerrado') NOT NULL DEFAULT 'reportado',
  `fecha_reporte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_atencion` datetime DEFAULT NULL,
  `fecha_resolucion` datetime DEFAULT NULL,
  `solucion` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `id_usuario_asignado` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_prioridad` (`prioridad`),
  KEY `idx_fecha_reporte` (`fecha_reporte`),
  KEY `idx_sector` (`sector`),
  CONSTRAINT `fk_incidentes_socio` FOREIGN KEY (`id_socio_reporta`) REFERENCES `socios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_incidentes_usuario` FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: actividad_reciente
-- ============================================
CREATE TABLE IF NOT EXISTS `actividad_reciente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_modulo` (`modulo`),
  KEY `idx_fecha` (`fecha_creacion`),
  KEY `idx_usuario` (`id_usuario`),
  CONSTRAINT `fk_actividad_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Insertar usuario administrador por defecto
-- Usuario: admin
-- Contraseña: admin123 (cambiar en producción)
INSERT INTO `usuarios` (`username`, `password`, `nombre`, `apellido`, `email`, `rol`, `activo`) VALUES
('admin', '$2y$10$zk.kgVI6G.wpXZhnrrFWBecfBb16w2TX5WEKGHAXpIk2abgB5dK4C', 'Administrador', 'Sistema', 'admin@apr.local', 'Administrador', 1),
('operador', '$2y$10$zk.kgVI6G.wpXZhnrrFWBecfBb16w2TX5WEKGHAXpIk2abgB5dK4C', 'Juan', 'Pérez', 'operador@apr.local', 'Operador', 1),
('tesorero', '$2y$10$zk.kgVI6G.wpXZhnrrFWBecfBb16w2TX5WEKGHAXpIk2abgB5dK4C', 'María', 'González', 'tesorero@apr.local', 'Tesorero', 1);

-- Nota: La contraseña es 'admin123' para todos los usuarios de prueba (password_hash('admin123', PASSWORD_DEFAULT))
-- ¡IMPORTANTE! Cambiar estas contraseñas en producción

-- Insertar tarifas por defecto
INSERT INTO `tarifas` (`nombre`, `tipo_cliente`, `consumo_minimo`, `tarifa_base`, `precio_m3_excedente`, `vigente_desde`, `activo`) VALUES
('Tarifa Residencial 2025', 'residencial', 10.00, 5000.00, 500.00, '2025-01-01', 1),
('Tarifa Comercial 2025', 'comercial', 15.00, 8000.00, 700.00, '2025-01-01', 1),
('Tarifa Industrial 2025', 'industrial', 20.00, 12000.00, 900.00, '2025-01-01', 1);

-- Insertar algunos socios de ejemplo
INSERT INTO `socios` (`numero_socio`, `rut`, `nombre`, `apellido_paterno`, `apellido_materno`, `direccion`, `sector`, `telefono`, `tipo_cliente`, `numero_medidor`, `estado`, `fecha_ingreso`) VALUES
('SOC-001', '12345678-9', 'Pedro', 'Martínez', 'López', 'Calle Principal 123', 'Centro', '+56912345678', 'residencial', 'MED-001', 'activo', '2024-01-15'),
('SOC-002', '98765432-1', 'Ana', 'Silva', 'Torres', 'Avenida Los Pinos 456', 'Norte', '+56987654321', 'residencial', 'MED-002', 'activo', '2024-02-20'),
('SOC-003', '11223344-5', 'Carlos', 'Rojas', 'Muñoz', 'Camino Rural KM 2', 'Sur', '+56911223344', 'residencial', 'MED-003', 'activo', '2024-03-10');

-- Insertar actividad reciente inicial
INSERT INTO `actividad_reciente` (`modulo`, `descripcion`, `id_usuario`) VALUES
('Sistema', 'Sistema APR inicializado correctamente', 1),
('Usuarios', 'Usuarios de prueba creados', 1),
('Socios', 'Socios de ejemplo registrados', 1),
('Tarifas', 'Tarifas iniciales configuradas', 1);

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Socios con información completa
CREATE OR REPLACE VIEW `vista_socios` AS
SELECT
    s.*,
    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', IFNULL(s.apellido_materno, '')) AS nombre_completo
FROM socios s
WHERE s.activo = 1;

-- Vista: Lecturas con información del socio
CREATE OR REPLACE VIEW `vista_lecturas` AS
SELECT
    l.*,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno) AS nombre_socio,
    s.direccion,
    s.sector,
    s.numero_medidor
FROM lecturas l
INNER JOIN socios s ON l.id_socio = s.id;

-- Vista: Boletas pendientes de pago
CREATE OR REPLACE VIEW `vista_boletas_pendientes` AS
SELECT
    b.*,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno) AS nombre_socio,
    s.telefono,
    s.direccion,
    DATEDIFF(CURDATE(), b.fecha_vencimiento) AS dias_vencidos
FROM boletas b
INNER JOIN socios s ON b.id_socio = s.id
WHERE b.estado IN ('pendiente', 'vencida')
ORDER BY b.fecha_vencimiento;

-- Vista: Resumen de consumo por socio
CREATE OR REPLACE VIEW `vista_consumo_socios` AS
SELECT
    s.id,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno) AS nombre_socio,
    s.direccion,
    s.sector,
    COUNT(l.id) AS total_lecturas,
    SUM(l.consumo_m3) AS consumo_total,
    AVG(l.consumo_m3) AS consumo_promedio,
    MAX(l.fecha_lectura) AS ultima_lectura
FROM socios s
LEFT JOIN lecturas l ON s.id = l.id_socio
WHERE s.activo = 1
GROUP BY s.id;

-- Vista: Incidentes activos por sector
CREATE OR REPLACE VIEW `vista_incidentes_activos` AS
SELECT
    i.*,
    CONCAT(s.nombre, ' ', s.apellido_paterno) AS nombre_reportante,
    s.telefono AS telefono_reportante,
    CONCAT(u.nombre, ' ', u.apellido) AS nombre_asignado
FROM incidentes i
LEFT JOIN socios s ON i.id_socio_reporta = s.id
LEFT JOIN usuarios u ON i.id_usuario_asignado = u.id
WHERE i.estado IN ('reportado', 'en_atencion')
ORDER BY i.prioridad DESC, i.fecha_reporte;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger: Actualizar estado de boleta al registrar pago completo
DELIMITER $$
CREATE TRIGGER `trg_actualizar_estado_boleta`
AFTER INSERT ON `pagos`
FOR EACH ROW
BEGIN
    DECLARE total_boleta DECIMAL(10,2);
    DECLARE total_pagado DECIMAL(10,2);

    -- Obtener el total de la boleta
    SELECT total INTO total_boleta
    FROM boletas
    WHERE id = NEW.id_boleta;

    -- Calcular el total pagado
    SELECT IFNULL(SUM(monto_pagado), 0) INTO total_pagado
    FROM pagos
    WHERE id_boleta = NEW.id_boleta;

    -- Si el total pagado es mayor o igual al total de la boleta, marcarla como pagada
    IF total_pagado >= total_boleta THEN
        UPDATE boletas
        SET estado = 'pagada'
        WHERE id = NEW.id_boleta;
    END IF;
END$$
DELIMITER ;

-- Trigger: Registrar actividad al crear nuevo socio
DELIMITER $$
CREATE TRIGGER `trg_registrar_actividad_socio`
AFTER INSERT ON `socios`
FOR EACH ROW
BEGIN
    INSERT INTO actividad_reciente (modulo, descripcion, id_usuario)
    VALUES ('Socios',
            CONCAT('Nuevo socio registrado: ', NEW.numero_socio, ' - ', NEW.nombre, ' ', NEW.apellido_paterno),
            NULL);
END$$
DELIMITER ;

-- Trigger: Registrar actividad al crear incidente
DELIMITER $$
CREATE TRIGGER `trg_registrar_actividad_incidente`
AFTER INSERT ON `incidentes`
FOR EACH ROW
BEGIN
    INSERT INTO actividad_reciente (modulo, descripcion, id_usuario)
    VALUES ('Incidentes',
            CONCAT('Nuevo incidente reportado: ', NEW.tipo, ' en ', NEW.ubicacion),
            NEW.id_usuario_asignado);
END$$
DELIMITER ;

-- Trigger: Actualizar estado de socio a moroso si tiene boletas vencidas
DELIMITER $$
CREATE TRIGGER `trg_verificar_morosidad`
AFTER UPDATE ON `boletas`
FOR EACH ROW
BEGIN
    DECLARE boletas_vencidas INT;

    -- Contar boletas vencidas del socio
    SELECT COUNT(*) INTO boletas_vencidas
    FROM boletas
    WHERE id_socio = NEW.id_socio
    AND estado = 'vencida'
    AND fecha_vencimiento < CURDATE();

    -- Si tiene 2 o más boletas vencidas, marcar como moroso
    IF boletas_vencidas >= 2 THEN
        UPDATE socios
        SET estado = 'moroso'
        WHERE id = NEW.id_socio;
    END IF;
END$$
DELIMITER ;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

-- Procedimiento: Generar boletas mensuales para todos los socios
DELIMITER $$
CREATE PROCEDURE `sp_generar_boletas_mes`(IN p_mes VARCHAR(7))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_socio INT;
    DECLARE v_numero_socio VARCHAR(20);
    DECLARE v_tipo_cliente VARCHAR(20);
    DECLARE v_consumo DECIMAL(10,2);
    DECLARE v_cargo_fijo DECIMAL(10,2);
    DECLARE v_cargo_consumo DECIMAL(10,2);
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_id_lectura INT;
    DECLARE v_consumo_minimo DECIMAL(10,2);
    DECLARE v_precio_m3 DECIMAL(10,2);

    DECLARE cur_socios CURSOR FOR
        SELECT s.id, s.numero_socio, s.tipo_cliente
        FROM socios s
        WHERE s.activo = 1 AND s.estado != 'desconectado';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur_socios;

    read_loop: LOOP
        FETCH cur_socios INTO v_id_socio, v_numero_socio, v_tipo_cliente;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Obtener lectura del mes
        SELECT id, consumo_m3 INTO v_id_lectura, v_consumo
        FROM lecturas
        WHERE id_socio = v_id_socio AND mes = p_mes
        LIMIT 1;

        -- Si no hay lectura, usar consumo 0
        IF v_id_lectura IS NULL THEN
            SET v_consumo = 0;
        END IF;

        -- Obtener tarifa vigente
        SELECT tarifa_base, consumo_minimo, precio_m3_excedente
        INTO v_cargo_fijo, v_consumo_minimo, v_precio_m3
        FROM tarifas
        WHERE tipo_cliente = v_tipo_cliente
        AND activo = 1
        AND vigente_desde <= LAST_DAY(CONCAT(p_mes, '-01'))
        AND (vigente_hasta IS NULL OR vigente_hasta >= LAST_DAY(CONCAT(p_mes, '-01')))
        LIMIT 1;

        -- Calcular cargo por consumo
        IF v_consumo > v_consumo_minimo THEN
            SET v_cargo_consumo = (v_consumo - v_consumo_minimo) * v_precio_m3;
        ELSE
            SET v_cargo_consumo = 0;
        END IF;

        -- Calcular total
        SET v_total = v_cargo_fijo + v_cargo_consumo;

        -- Insertar boleta
        INSERT INTO boletas (
            numero_boleta,
            id_socio,
            id_lectura,
            mes,
            fecha_emision,
            fecha_vencimiento,
            consumo_m3,
            cargo_fijo,
            cargo_consumo,
            total,
            estado
        ) VALUES (
            CONCAT('BOL-', p_mes, '-', LPAD(v_id_socio, 4, '0')),
            v_id_socio,
            v_id_lectura,
            p_mes,
            LAST_DAY(CONCAT(p_mes, '-01')),
            DATE_ADD(LAST_DAY(CONCAT(p_mes, '-01')), INTERVAL 15 DAY),
            v_consumo,
            v_cargo_fijo,
            v_cargo_consumo,
            v_total,
            'pendiente'
        );

    END LOOP;

    CLOSE cur_socios;
END$$
DELIMITER ;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

-- Índices compuestos para búsquedas frecuentes
CREATE INDEX idx_lecturas_socio_mes ON lecturas(id_socio, mes);
CREATE INDEX idx_boletas_socio_estado ON boletas(id_socio, estado);
CREATE INDEX idx_pagos_fecha ON pagos(fecha_pago);
CREATE INDEX idx_incidentes_sector_estado ON incidentes(sector, estado);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
