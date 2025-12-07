-- ============================================
-- Migración: Agregar campos tipo_cliente y vigencia a configuraciones_tarifas
-- Fecha: 2025-11-29
-- Descripción: Convierte sistema simple a sistema híbrido de tramos por tipo de cliente
-- ============================================

USE `sistema_apr`;

-- 1. Agregar nuevas columnas a la tabla configuraciones_tarifas
ALTER TABLE `configuraciones_tarifas`
ADD COLUMN `tipo_cliente` ENUM('residencial','comercial','industrial') NOT NULL DEFAULT 'residencial' AFTER `nombre`,
ADD COLUMN `nombre_tarifa` VARCHAR(100) NULL COMMENT 'Ej: Tarifa Residencial 2025' AFTER `tipo_cliente`,
ADD COLUMN `vigente_desde` DATE NOT NULL DEFAULT '2025-01-01' AFTER `iva`,
ADD COLUMN `vigente_hasta` DATE NULL AFTER `vigente_desde`;

-- 2. Crear índices para optimización
ALTER TABLE `configuraciones_tarifas`
ADD INDEX `idx_tipo_vigencia` (`tipo_cliente`, `vigente_desde`, `vigente_hasta`),
ADD INDEX `idx_tipo_orden` (`tipo_cliente`, `orden`),
ADD INDEX `idx_activo_tipo` (`activo`, `tipo_cliente`);

-- 3. Actualizar comentarios de columnas para mayor claridad
ALTER TABLE `configuraciones_tarifas`
MODIFY COLUMN `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del tramo: Ej. Tramo 1, Tramo 2',
MODIFY COLUMN `consumo_desde` DECIMAL(10,2) NOT NULL COMMENT 'M³ de inicio del tramo',
MODIFY COLUMN `consumo_hasta` DECIMAL(10,2) NULL COMMENT 'M³ fin del tramo (NULL = sin límite superior)',
MODIFY COLUMN `monto` DECIMAL(10,2) NOT NULL COMMENT 'Monto total a cobrar cuando el consumo cae en este tramo',
MODIFY COLUMN `cargo_fijo` DECIMAL(10,2) NULL COMMENT 'Cargo fijo mensual (ya incluido en el monto)',
MODIFY COLUMN `iva` DECIMAL(5,2) NULL DEFAULT 19.00 COMMENT 'Porcentaje de IVA a aplicar';

-- 4. Limpiar datos existentes (si hay) para evitar conflictos
TRUNCATE TABLE `configuraciones_tarifas`;

-- 5. Insertar tramos para TARIFA RESIDENCIAL 2025
INSERT INTO `configuraciones_tarifas`
(`nombre`, `tipo_cliente`, `nombre_tarifa`, `consumo_desde`, `consumo_hasta`, `monto`, `cargo_fijo`, `iva`, `orden`, `vigente_desde`, `activo`)
VALUES
('Tramo 1 (0-10 m³)', 'residencial', 'Tarifa Residencial 2025', 0.00, 10.00, 5000.00, 2500.00, 19.00, 1, '2025-01-01', 1),
('Tramo 2 (11-20 m³)', 'residencial', 'Tarifa Residencial 2025', 11.00, 20.00, 8000.00, 2500.00, 19.00, 2, '2025-01-01', 1),
('Tramo 3 (21-30 m³)', 'residencial', 'Tarifa Residencial 2025', 21.00, 30.00, 12000.00, 2500.00, 19.00, 3, '2025-01-01', 1),
('Tramo 4 (31+ m³)', 'residencial', 'Tarifa Residencial 2025', 31.00, NULL, 18000.00, 2500.00, 19.00, 4, '2025-01-01', 1);

-- 6. Insertar tramos para TARIFA COMERCIAL 2025
INSERT INTO `configuraciones_tarifas`
(`nombre`, `tipo_cliente`, `nombre_tarifa`, `consumo_desde`, `consumo_hasta`, `monto`, `cargo_fijo`, `iva`, `orden`, `vigente_desde`, `activo`)
VALUES
('Tramo 1 (0-15 m³)', 'comercial', 'Tarifa Comercial 2025', 0.00, 15.00, 8000.00, 5000.00, 19.00, 1, '2025-01-01', 1),
('Tramo 2 (16-30 m³)', 'comercial', 'Tarifa Comercial 2025', 16.00, 30.00, 15000.00, 5000.00, 19.00, 2, '2025-01-01', 1),
('Tramo 3 (31-50 m³)', 'comercial', 'Tarifa Comercial 2025', 31.00, 50.00, 25000.00, 5000.00, 19.00, 3, '2025-01-01', 1),
('Tramo 4 (51+ m³)', 'comercial', 'Tarifa Comercial 2025', 51.00, NULL, 40000.00, 5000.00, 19.00, 4, '2025-01-01', 1);

-- 7. Insertar tramos para TARIFA INDUSTRIAL 2025
INSERT INTO `configuraciones_tarifas`
(`nombre`, `tipo_cliente`, `nombre_tarifa`, `consumo_desde`, `consumo_hasta`, `monto`, `cargo_fijo`, `iva`, `orden`, `vigente_desde`, `activo`)
VALUES
('Tramo 1 (0-20 m³)', 'industrial', 'Tarifa Industrial 2025', 0.00, 20.00, 12000.00, 8000.00, 19.00, 1, '2025-01-01', 1),
('Tramo 2 (21-40 m³)', 'industrial', 'Tarifa Industrial 2025', 21.00, 40.00, 22000.00, 8000.00, 19.00, 2, '2025-01-01', 1),
('Tramo 3 (41-70 m³)', 'industrial', 'Tarifa Industrial 2025', 41.00, 70.00, 38000.00, 8000.00, 19.00, 3, '2025-01-01', 1),
('Tramo 4 (71+ m³)', 'industrial', 'Tarifa Industrial 2025', 71.00, NULL, 60000.00, 8000.00, 19.00, 4, '2025-01-01', 1);

-- ============================================
-- Verificación de datos insertados
-- ============================================
SELECT
    tipo_cliente,
    nombre_tarifa,
    COUNT(*) as total_tramos,
    MIN(monto) as monto_minimo,
    MAX(monto) as monto_maximo
FROM configuraciones_tarifas
WHERE activo = 1
GROUP BY tipo_cliente, nombre_tarifa
ORDER BY tipo_cliente;

-- ============================================
-- FIN DE LA MIGRACIÓN
-- ============================================
