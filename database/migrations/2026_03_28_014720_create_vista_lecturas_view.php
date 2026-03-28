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
        DB::statement("CREATE VIEW `vista_lecturas` AS select `l`.`id` AS `id`,`l`.`id_socio` AS `id_socio`,`l`.`mes` AS `mes`,`l`.`lectura_anterior` AS `lectura_anterior`,`l`.`lectura_actual` AS `lectura_actual`,`l`.`consumo_m3` AS `consumo_m3`,`l`.`fecha_lectura` AS `fecha_lectura`,`l`.`observaciones` AS `observaciones`,`l`.`id_usuario_registro` AS `id_usuario_registro`,`l`.`fecha_creacion` AS `fecha_creacion`,`s`.`numero_socio` AS `numero_socio`,concat(`s`.`nombre`,' ',`s`.`apellido_paterno`) AS `nombre_socio`,`s`.`direccion` AS `direccion`,`s`.`sector` AS `sector`,`s`.`numero_medidor` AS `numero_medidor` from (`ssr_v2`.`lecturas` `l` join `ssr_v2`.`socios` `s` on(`l`.`id_socio` = `s`.`id`))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_lecturas`");
    }
};
