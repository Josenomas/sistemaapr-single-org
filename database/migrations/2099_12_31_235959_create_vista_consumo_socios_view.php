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
        DB::statement("CREATE VIEW `vista_consumo_socios` AS select `s`.`id` AS `id`,`s`.`numero_socio` AS `numero_socio`,concat(`s`.`nombre`,' ',`s`.`apellido_paterno`) AS `nombre_socio`,`s`.`direccion` AS `direccion`,`s`.`sector` AS `sector`,count(`l`.`id`) AS `total_lecturas`,sum(`l`.`consumo_m3`) AS `consumo_total`,avg(`l`.`consumo_m3`) AS `consumo_promedio`,max(`l`.`fecha_lectura`) AS `ultima_lectura` from (`ssr_v2`.`socios` `s` left join `ssr_v2`.`lecturas` `l` on(`s`.`id` = `l`.`id_socio`)) where `s`.`activo` = 1 group by `s`.`id`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_consumo_socios`");
    }
};
