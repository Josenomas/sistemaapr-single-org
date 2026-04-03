<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar el procedimiento existente
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_generar_boletas_mes");

        // Crear el procedimiento SIN DEFINER (para compatibilidad con usuarios sin SUPER privilege)
        DB::unprepared("CREATE PROCEDURE `sp_generar_boletas_mes`(
            IN `p_mes` VARCHAR(7),
            IN `p_id_organizacion` BIGINT UNSIGNED
        )
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE done_tramos INT DEFAULT FALSE;
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
    DECLARE v_tramo_nombre VARCHAR(100);
    DECLARE v_tramo_desde DECIMAL(10,2);
    DECLARE v_tramo_hasta DECIMAL(10,2);
    DECLARE v_tramo_monto DECIMAL(10,2);
    DECLARE v_tramo_cargo_fijo DECIMAL(10,2);
    DECLARE v_tramo_iva DECIMAL(5,2);
    DECLARE v_m3_en_tramo DECIMAL(10,2);
    DECLARE v_subtotal_tramo DECIMAL(10,2);
    DECLARE v_observaciones TEXT;

    DECLARE cur_socios CURSOR FOR
        SELECT s.id, s.numero_socio,
               CAST(s.tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci,
               s.exento_iva,
               IFNULL(s.subsidio_porcentaje, 0),
               IFNULL(s.descuento_monto, 0)
        FROM socios s
        WHERE s.id_organizacion = p_id_organizacion
          AND s.activo = 1
          AND s.estado != 'desconectado';

    DECLARE cur_tramos CURSOR FOR
        SELECT ct.nombre, ct.consumo_desde, ct.consumo_hasta, ct.monto, ct.cargo_fijo, ct.iva
        FROM configuraciones_tarifas ct
        WHERE ct.id_organizacion = p_id_organizacion
          AND CAST(ct.tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci =
              CAST(v_tipo_cliente AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci
          AND ct.activo = 1
          AND ct.vigente_desde <= v_fecha_emision
          AND (ct.vigente_hasta IS NULL OR ct.vigente_hasta >= v_fecha_emision)
        ORDER BY ct.orden ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SET v_fecha_emision = LAST_DAY(CONCAT(p_mes, '-01'));
    SET v_fecha_vencimiento = CONCAT(DATE_FORMAT(DATE_ADD(v_fecha_emision, INTERVAL 1 MONTH), '%Y-%m-'), '25');

    OPEN cur_socios;

    read_loop: LOOP
        FETCH cur_socios INTO v_id_socio, v_numero_socio, v_tipo_cliente, v_exento_iva,
                              v_subsidio_porcentaje, v_descuento_monto;

        IF done THEN
            LEAVE read_loop;
        END IF;

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

        SELECT id, consumo_m3 INTO v_id_lectura, v_consumo
        FROM lecturas
        WHERE id_organizacion = p_id_organizacion
          AND id_socio = v_id_socio
          AND mes = p_mes
        LIMIT 1;

        IF v_id_lectura IS NULL THEN
            SET v_consumo = 0;
        END IF;

        SET done_tramos = FALSE;

        BLOCK_TRAMOS: BEGIN
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_tramos = TRUE;
            OPEN cur_tramos;

            tramos_loop: LOOP
                FETCH cur_tramos INTO v_tramo_nombre, v_tramo_desde, v_tramo_hasta,
                                      v_tramo_monto, v_tramo_cargo_fijo, v_tramo_iva;

                IF done_tramos THEN
                    LEAVE tramos_loop;
                END IF;

                IF v_consumo < v_tramo_desde THEN
                    ITERATE tramos_loop;
                END IF;

                IF v_cargo_fijo = 0 THEN
                    SET v_cargo_fijo = IFNULL(v_tramo_cargo_fijo, 0);
                    SET v_iva_porcentaje = IFNULL(v_tramo_iva, 0);
                END IF;

                SET v_m3_en_tramo = 0;

                IF v_tramo_hasta IS NULL THEN
                    SET v_m3_en_tramo = v_consumo - v_tramo_desde;
                ELSEIF v_consumo <= v_tramo_hasta THEN
                    SET v_m3_en_tramo = v_consumo - v_tramo_desde;
                ELSE
                    SET v_m3_en_tramo = v_tramo_hasta - v_tramo_desde;
                END IF;

                IF v_m3_en_tramo > 0 THEN
                    SET v_subtotal_tramo = v_m3_en_tramo * v_tramo_monto;
                    SET v_cargo_consumo = v_cargo_consumo + v_subtotal_tramo;

                    IF LENGTH(v_observaciones) > 0 THEN
                        SET v_observaciones = CONCAT(v_observaciones, ' | ');
                    END IF;

                    SET v_observaciones = CONCAT(v_observaciones, v_tramo_nombre, ': ',
                                                  ROUND(v_m3_en_tramo, 2), 'm³ × $',
                                                  FORMAT(v_tramo_monto, 0), ' = $',
                                                  FORMAT(v_subtotal_tramo, 0));
                END IF;

                IF v_tramo_hasta IS NULL OR v_consumo <= v_tramo_hasta THEN
                    LEAVE tramos_loop;
                END IF;
            END LOOP tramos_loop;

            CLOSE cur_tramos;
        END BLOCK_TRAMOS;

        SET v_subtotal = v_cargo_consumo + v_cargo_fijo;
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

        IF v_exento_iva = 0 AND v_iva_porcentaje > 0 THEN
            SET v_monto_iva = ROUND(v_subtotal * (v_iva_porcentaje / 100), 0);
        ELSE
            SET v_monto_iva = 0;
        END IF;

        SET v_total = v_subtotal + v_monto_iva;

        IF EXISTS (
            SELECT 1 FROM boletas
            WHERE id_organizacion = p_id_organizacion
              AND id_socio = v_id_socio
              AND mes = p_mes
              AND activo = 1
        ) THEN
            ITERATE read_loop;
        END IF;

        SET v_numero_boleta = CONCAT('BOL-', p_mes, '-', LPAD(v_id_socio, 4, '0'));

        INSERT INTO boletas (
            id_organizacion,
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
            p_id_organizacion,
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
            CONCAT('Generada automáticamente - Sistema Progresivo | ', v_observaciones),
            1
        );
    END LOOP;

    CLOSE cur_socios;

    SELECT COUNT(*) as boletas_generadas
    FROM boletas
    WHERE id_organizacion = p_id_organizacion
      AND mes = p_mes
      AND activo = 1;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_generar_boletas_mes");
    }
};
