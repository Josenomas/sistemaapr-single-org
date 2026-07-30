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
        DB::statement("CREATE VIEW `vista_incidentes_activos` AS select `i`.`id` AS `id`,`i`.`tipo` AS `tipo`,`i`.`descripcion` AS `descripcion`,`i`.`ubicacion` AS `ubicacion`,`i`.`sector` AS `sector`,`i`.`id_socio_reporta` AS `id_socio_reporta`,`i`.`prioridad` AS `prioridad`,`i`.`estado` AS `estado`,`i`.`fecha_reporte` AS `fecha_reporte`,`i`.`fecha_atencion` AS `fecha_atencion`,`i`.`fecha_resolucion` AS `fecha_resolucion`,`i`.`solucion` AS `solucion`,`i`.`observaciones` AS `observaciones`,`i`.`id_usuario_asignado` AS `id_usuario_asignado`,`i`.`fecha_creacion` AS `fecha_creacion`,concat(`s`.`nombre`,' ',`s`.`apellido_paterno`) AS `nombre_reportante`,`s`.`telefono` AS `telefono_reportante`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `nombre_asignado` from ((`ssr_v2`.`incidentes` `i` left join `ssr_v2`.`socios` `s` on(`i`.`id_socio_reporta` = `s`.`id`)) left join `ssr_v2`.`usuarios` `u` on(`i`.`id_usuario_asignado` = `u`.`id`)) where `i`.`estado` in ('reportado','en_atencion') order by `i`.`prioridad` desc,`i`.`fecha_reporte`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `vista_incidentes_activos`");
    }
};
