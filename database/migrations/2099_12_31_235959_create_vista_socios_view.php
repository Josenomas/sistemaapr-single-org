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
        DB::statement("CREATE VIEW `vista_socios` AS select `s`.`id` AS `id`,`s`.`numero_socio` AS `numero_socio`,`s`.`rut` AS `rut`,`s`.`nombre` AS `nombre`,`s`.`apellido_paterno` AS `apellido_paterno`,`s`.`apellido_materno` AS `apellido_materno`,`s`.`direccion` AS `direccion`,`s`.`sector` AS `sector`,`s`.`telefono` AS `telefono`,`s`.`email` AS `email`,`s`.`tipo_cliente` AS `tipo_cliente`,`s`.`numero_medidor` AS `numero_medidor`,`s`.`estado` AS `estado`,`s`.`fecha_ingreso` AS `fecha_ingreso`,`s`.`observaciones` AS `observaciones`,`s`.`activo` AS `activo`,`s`.`fecha_creacion` AS `fecha_creacion`,`s`.`fecha_actualizacion` AS `fecha_actualizacion`,concat(`s`.`nombre`,' ',`s`.`apellido_paterno`,' ',ifnull(`s`.`apellido_materno`,'')) AS `nombre_completo` from `ssr_v2`.`socios` `s` where `s`.`activo` = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_socios`");
    }
};
