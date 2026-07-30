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
        DB::statement("CREATE VIEW `vista_boletas_pendientes` AS select `b`.`id` AS `id`,`b`.`numero_boleta` AS `numero_boleta`,`b`.`id_socio` AS `id_socio`,`b`.`id_lectura` AS `id_lectura`,`b`.`mes` AS `mes`,`b`.`fecha_emision` AS `fecha_emision`,`b`.`fecha_vencimiento` AS `fecha_vencimiento`,`b`.`consumo_m3` AS `consumo_m3`,`b`.`cargo_fijo` AS `cargo_fijo`,`b`.`cargo_consumo` AS `cargo_consumo`,`b`.`otros_cargos` AS `otros_cargos`,`b`.`descuentos` AS `descuentos`,`b`.`total` AS `total`,`b`.`estado` AS `estado`,`b`.`observaciones` AS `observaciones`,`b`.`fecha_creacion` AS `fecha_creacion`,`s`.`numero_socio` AS `numero_socio`,concat(`s`.`nombre`,' ',`s`.`apellido_paterno`) AS `nombre_socio`,`s`.`telefono` AS `telefono`,`s`.`direccion` AS `direccion`,to_days(curdate()) - to_days(`b`.`fecha_vencimiento`) AS `dias_vencidos` from (`ssr_v2`.`boletas` `b` join `ssr_v2`.`socios` `s` on(`b`.`id_socio` = `s`.`id`)) where `b`.`estado` in ('pendiente','vencida') order by `b`.`fecha_vencimiento`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_boletas_pendientes`");
    }
};
