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
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->foreign(['id_suscripcion'])->references(['id'])->on('suscripciones');
            $table->foreign(['aprobado_por'])->references(['id'])->on('usuarios')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->dropForeign('organizaciones_id_suscripcion_foreign');
            $table->dropForeign('organizaciones_aprobado_por_foreign');
        });
    }
};
