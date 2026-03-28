<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cambios_plan', function (Blueprint $table) {
            $table->foreign(['id_suscripcion_nueva'])->references(['id'])->on('suscripciones');
            $table->foreign(['id_suscripcion_anterior'])->references(['id'])->on('suscripciones');
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cambios_plan', function (Blueprint $table) {
            $table->dropForeign('cambios_plan_id_suscripcion_nueva_foreign');
            $table->dropForeign('cambios_plan_id_suscripcion_anterior_foreign');
            $table->dropForeign('cambios_plan_id_organizacion_foreign');
        });
    }
};
