-- ============================================
-- Procedimiento Almacenado: sp_generar_boletas_mes (VERSION 2 - Sistema de Tramos)
-- Fecha: 2025-11-29
-- Descripción: Genera boletas masivas usando sistema de tramos por tipo de cliente
-- ============================================

USE `sistema_apr`;

-- Eliminar procedimiento anterior si existe
DROP PROCEDURE IF EXISTS `sp_generar_boletas_mes`;

DELIMITER $$

CREATE PROCEDURE `sp_generar_boletas_mes`(IN p_mes VARCHAR(7))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_socio INT;
    DECLARE v_numero_socio VARCHAR(20);
    DECLARE v_tipo_cliente VARCHAR(20);
    DECLARE v_consumo DECIMAL(10,2);
    DECLARE v_cargo_fijo DECIMAL(10,2);
    DECLARE v_monto_base DECIMAL(10,2);
    DECLARE v_iva_porcentaje DECIMAL(5,2);
    DECLARE v_monto_iva DECIMAL(10,2);
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_id_lectura INT;
    DECLARE v_nombre_tramo VARCHAR(100);
    DECLARE v_numero_boleta VARCHAR(50);
    DECLARE v_fecha_emision DATE;
    DECLARE v_fecha_vencimiento DATE;

    -- Cursor para recorrer todos los socios activos
    DECLARE cur_socios CURSOR FOR
        SELECT s.id, s.numero_socio, s.tipo_cliente
        FROM socios s
        WHERE s.activo = 1 AND s.estado != 'desconectado';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Calcular fechas de emisión y vencimiento
    SET v_fecha_emision = LAST_DAY(CONCAT(p_mes, '-01'));
    SET v_fecha_vencimiento = DATE_ADD(v_fecha_emision, INTERVAL 15 DAY);

    OPEN cur_socios;

    read_loop: LOOP
        FETCH cur_socios INTO v_id_socio, v_numero_socio, v_tipo_cliente;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Reiniciar variables por cada iteración
        SET v_id_lectura = NULL;
        SET v_consumo = 0;
        SET v_cargo_fijo = 0;
        SET v_monto_base = 0;
        SET v_iva_porcentaje = 0;
        SET v_monto_iva = 0;
        SET v_total = 0;
        SET v_nombre_tramo = NULL;

        -- 1. Obtener lectura del mes para este socio
        SELECT id, consumo_m3 INTO v_id_lectura, v_consumo
        FROM lecturas
        WHERE id_socio = v_id_socio AND mes = p_mes
        LIMIT 1;

        -- Si no hay lectura registrada, usar consumo = 0
        IF v_id_lectura IS NULL THEN
            SET v_consumo = 0;
        END IF;

        -- 2. Buscar el tramo correspondiente según tipo de cliente y consumo
        -- El tramo se determina por: consumo >= consumo_desde AND (consumo <= consumo_hasta OR consumo_hasta IS NULL)
        SELECT
            ct.nombre,
            ct.monto,
            ct.cargo_fijo,
            ct.iva
        INTO
            v_nombre_tramo,
            v_monto_base,
            v_cargo_fijo,
            v_iva_porcentaje
        FROM configuraciones_tarifas ct
        WHERE ct.tipo_cliente = v_tipo_cliente
          AND ct.activo = 1
          AND ct.vigente_desde <= v_fecha_emision
          AND (ct.vigente_hasta IS NULL OR ct.vigente_hasta >= v_fecha_emision)
          AND v_consumo >= ct.consumo_desde
          AND (ct.consumo_hasta IS NULL OR v_consumo <= ct.consumo_hasta)
        ORDER BY ct.orden ASC
        LIMIT 1;

        -- Si no se encontró tramo (error de configuración), usar valores por defecto
        IF v_nombre_tramo IS NULL THEN
            SET v_nombre_tramo = 'Sin tarifa configurada';
            SET v_monto_base = 0;
            SET v_cargo_fijo = 0;
            SET v_iva_porcentaje = 0;
        END IF;

        -- 3. Calcular IVA si aplica
        IF v_iva_porcentaje IS NOT NULL AND v_iva_porcentaje > 0 THEN
            SET v_monto_iva = ROUND(v_monto_base * (v_iva_porcentaje / 100), 0);
        ELSE
            SET v_monto_iva = 0;
        END IF;

        -- 4. Calcular total (monto base + IVA)
        -- Nota: El monto_base YA incluye el cargo_fijo, según diseño de tramos
        SET v_total = v_monto_base + v_monto_iva;

        -- 5. Generar número de boleta único
        SET v_numero_boleta = CONCAT('BOL-', p_mes, '-', LPAD(v_id_socio, 4, '0'));

        -- 6. Insertar la boleta en la base de datos
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
            otros_cargos,
            descuentos,
            total,
            estado,
            observaciones,
            activo
        ) VALUES (
            v_numero_boleta,
            v_id_socio,
            v_id_lectura,
            p_mes,
            v_fecha_emision,
            v_fecha_vencimiento,
            v_consumo,
            v_cargo_fijo,                -- Cargo fijo del tramo
            v_monto_base - v_cargo_fijo, -- Cargo por consumo = monto_base - cargo_fijo
            v_monto_iva,                 -- IVA va en otros_cargos
            0,                           -- Sin descuentos por defecto
            v_total,
            'pendiente',
            CONCAT('Generada automáticamente - ', v_nombre_tramo),
            1
        );

    END LOOP;

    CLOSE cur_socios;

    -- Retornar cantidad de boletas generadas
    SELECT COUNT(*) as boletas_generadas
    FROM boletas
    WHERE mes = p_mes AND activo = 1;

END$$

DELIMITER ;

-- ============================================
-- TESTING del procedimiento
-- ============================================
-- Para probar: CALL sp_generar_boletas_mes('2025-12');
-- ============================================
