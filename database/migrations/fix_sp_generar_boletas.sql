-- ============================================
-- FIX: Procedimiento Almacenado sp_generar_boletas_mes
-- Problema: Handler NOT FOUND anidado causa que cursor principal se detenga
-- Solución: Eliminar BLOCK_TRAMOS y handler anidado
-- ============================================

USE `sistema_apr`;

DROP PROCEDURE IF EXISTS `sp_generar_boletas_mes`;

DELIMITER $$

CREATE PROCEDURE `sp_generar_boletas_mes`(IN p_mes VARCHAR(7))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_socio INT;
    DECLARE v_numero_socio VARCHAR(20);
    DECLARE v_tipo_cliente VARCHAR(20);
    DECLARE v_exento_iva TINYINT;
    DECLARE v_subsidio_porcentaje DECIMAL(5,2);
    DECLARE v_descuento_monto DECIMAL(10,2);
    DECLARE v_consumo DECIMAL(10,2);
    DECLARE v_cargo_fijo DECIMAL(10,2);
    DECLARE v_cargo_consumo DECIMAL(10,2);
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_monto_subsidio DECIMAL(10,2);
    DECLARE v_monto_descuento DECIMAL(10,2);
    DECLARE v_iva_porcentaje DECIMAL(5,2);
    DECLARE v_monto_iva DECIMAL(10,2);
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_id_lectura INT;
    DECLARE v_numero_boleta VARCHAR(50);
    DECLARE v_fecha_emision DATE;
    DECLARE v_fecha_vencimiento DATE;
    DECLARE v_observaciones TEXT;
    DECLARE v_total_socios INT;
    DECLARE v_total_lecturas INT;
    DECLARE v_socios_sin_lectura TEXT;

    -- Cursor para recorrer todos los socios activos
    DECLARE cur_socios CURSOR FOR
        SELECT s.id, s.numero_socio,
               CAST(s.tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci,
               s.exento_iva,
               IFNULL(s.subsidio_porcentaje, 0),
               IFNULL(s.descuento_monto, 0)
        FROM socios s
        WHERE s.activo = 1 AND s.estado != 'desconectado';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Calcular fechas de emisión y vencimiento
    SET v_fecha_emision = LAST_DAY(CONCAT(p_mes, '-01'));
    SET v_fecha_vencimiento = CONCAT(DATE_FORMAT(DATE_ADD(v_fecha_emision, INTERVAL 1 MONTH), '%Y-%m-'), '25');

    -- VALIDACIÓN: Verificar que todos los socios activos tengan lecturas
    SELECT COUNT(DISTINCT s.id) INTO v_total_socios
    FROM socios s
    WHERE s.activo = 1 AND s.estado != 'desconectado';

    SELECT COUNT(DISTINCT l.id_socio) INTO v_total_lecturas
    FROM lecturas l
    INNER JOIN socios s ON l.id_socio = s.id
    WHERE s.activo = 1 AND s.estado != 'desconectado' AND l.mes = p_mes;

    -- Si faltan lecturas, generar lista de socios sin lectura
    IF v_total_lecturas < v_total_socios THEN
        SELECT GROUP_CONCAT(CONCAT(s.numero_socio, ' - ', s.nombre, ' ', s.apellido_paterno) SEPARATOR ', ')
        INTO v_socios_sin_lectura
        FROM socios s
        LEFT JOIN lecturas l ON s.id = l.id_socio AND l.mes = p_mes
        WHERE s.activo = 1 AND s.estado != 'desconectado' AND l.id IS NULL
        LIMIT 10;

        -- Lanzar error con la lista de socios
        SET @error_msg = CONCAT('FALTAN LECTURAS: ', (v_total_socios - v_total_lecturas),
                                ' socios sin lectura. Primeros 10: ', IFNULL(v_socios_sin_lectura, 'N/A'));
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_msg;
    END IF;

    OPEN cur_socios;

    read_loop: LOOP
        FETCH cur_socios INTO v_id_socio, v_numero_socio, v_tipo_cliente, v_exento_iva,
                              v_subsidio_porcentaje, v_descuento_monto;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Reiniciar variables por cada iteración
        SET v_id_lectura = NULL;
        SET v_consumo = 0;
        SET v_cargo_fijo = 0;
        SET v_cargo_consumo = 0;
        SET v_subtotal = 0;
        SET v_monto_subsidio = 0;
        SET v_monto_descuento = 0;
        SET v_iva_porcentaje = 0;
        SET v_monto_iva = 0;
        SET v_total = 0;
        SET v_observaciones = '';

        -- 1. Obtener lectura del mes para este socio
        SELECT id, consumo_m3 INTO v_id_lectura, v_consumo
        FROM lecturas
        WHERE id_socio = v_id_socio AND mes = p_mes
        LIMIT 1;

        -- Si no hay lectura registrada, usar consumo = 0
        IF v_id_lectura IS NULL THEN
            SET v_consumo = 0;
        END IF;

        -- 2. CALCULO PROGRESIVO POR TRAMOS usando tabla temporal
        -- Obtener cargo_fijo e IVA del primer tramo
        SELECT IFNULL(cargo_fijo, 0), IFNULL(iva, 0) INTO v_cargo_fijo, v_iva_porcentaje
        FROM configuraciones_tarifas ct
        WHERE CAST(ct.tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci =
              CAST(v_tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci
          AND ct.activo = 1
          AND ct.vigente_desde <= v_fecha_emision
          AND (ct.vigente_hasta IS NULL OR ct.vigente_hasta >= v_fecha_emision)
        ORDER BY ct.orden ASC
        LIMIT 1;

        -- Calcular cargo de consumo progresivo usando subquery
        SELECT
            IFNULL(SUM(
                CASE
                    WHEN v_consumo < ct.consumo_desde THEN 0
                    WHEN ct.consumo_hasta IS NULL THEN (v_consumo - ct.consumo_desde) * ct.monto
                    WHEN v_consumo <= ct.consumo_hasta THEN (v_consumo - ct.consumo_desde) * ct.monto
                    ELSE (ct.consumo_hasta - ct.consumo_desde) * ct.monto
                END
            ), 0),
            GROUP_CONCAT(
                CONCAT(
                    ct.nombre, ': ',
                    ROUND(
                        CASE
                            WHEN v_consumo < ct.consumo_desde THEN 0
                            WHEN ct.consumo_hasta IS NULL THEN v_consumo - ct.consumo_desde
                            WHEN v_consumo <= ct.consumo_hasta THEN v_consumo - ct.consumo_desde
                            ELSE ct.consumo_hasta - ct.consumo_desde
                        END, 2
                    ), 'm³ × $',
                    FORMAT(ct.monto, 0), ' = $',
                    FORMAT(
                        CASE
                            WHEN v_consumo < ct.consumo_desde THEN 0
                            WHEN ct.consumo_hasta IS NULL THEN (v_consumo - ct.consumo_desde) * ct.monto
                            WHEN v_consumo <= ct.consumo_hasta THEN (v_consumo - ct.consumo_desde) * ct.monto
                            ELSE (ct.consumo_hasta - ct.consumo_desde) * ct.monto
                        END, 0
                    )
                )
                SEPARATOR ' | '
            )
        INTO v_cargo_consumo, v_observaciones
        FROM configuraciones_tarifas ct
        WHERE CAST(ct.tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci =
              CAST(v_tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci
          AND ct.activo = 1
          AND ct.vigente_desde <= v_fecha_emision
          AND (ct.vigente_hasta IS NULL OR ct.vigente_hasta >= v_fecha_emision)
          AND v_consumo >= ct.consumo_desde
        ORDER BY ct.orden ASC;

        -- 3. Calcular subtotal (cargo_consumo + cargo_fijo)
        SET v_subtotal = v_cargo_consumo + v_cargo_fijo;

        -- 4. Calcular subsidios y descuentos
        SET v_monto_subsidio = 0;
        SET v_monto_descuento = 0;

        IF v_subsidio_porcentaje > 0 THEN
            SET v_monto_subsidio = ROUND(v_subtotal * (v_subsidio_porcentaje / 100), 0);
            IF LENGTH(v_observaciones) > 0 THEN
                SET v_observaciones = CONCAT(v_observaciones, ' | ');
            END IF;
            SET v_observaciones = CONCAT(v_observaciones, 'Subsidio ',
                                          v_subsidio_porcentaje, '%: -$',
                                          FORMAT(v_monto_subsidio, 0));
        END IF;

        IF v_descuento_monto > 0 THEN
            SET v_monto_descuento = v_descuento_monto;
            IF LENGTH(v_observaciones) > 0 THEN
                SET v_observaciones = CONCAT(v_observaciones, ' | ');
            END IF;
            SET v_observaciones = CONCAT(v_observaciones, 'Descuento fijo: -$',
                                          FORMAT(v_monto_descuento, 0));
        END IF;

        SET v_subtotal = v_subtotal - v_monto_subsidio - v_monto_descuento;

        -- 5. Calcular IVA
        IF v_exento_iva = 0 AND v_iva_porcentaje > 0 THEN
            SET v_monto_iva = ROUND(v_subtotal * (v_iva_porcentaje / 100), 0);
        ELSE
            SET v_monto_iva = 0;
        END IF;

        -- 6. Calcular total
        SET v_total = v_subtotal + v_monto_iva;

        -- 7. Verificar si ya existe una boleta para este socio en este mes
        IF EXISTS (SELECT 1 FROM boletas WHERE id_socio = v_id_socio AND mes = p_mes AND activo = 1) THEN
            ITERATE read_loop;
        END IF;

        -- 8. Generar número de boleta único
        SET v_numero_boleta = CONCAT('BOL-', p_mes, '-', LPAD(v_id_socio, 4, '0'));

        -- 9. Insertar la boleta
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
            v_cargo_fijo,
            v_cargo_consumo,
            v_monto_iva,
            (v_monto_subsidio + v_monto_descuento),
            v_total,
            'pendiente',
            CONCAT('Generada automáticamente - Sistema Progresivo | ', IFNULL(v_observaciones, 'Sin consumo')),
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
