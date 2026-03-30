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
            $table->string('pago_presencial_dias', 100)->nullable()->default('Lunes a Viernes')->after('activo');
            $table->string('pago_presencial_horario', 100)->nullable()->default('09:00 a 17:00 hrs')->after('pago_presencial_dias');
            $table->string('pago_presencial_lugar', 100)->nullable()->default('Oficina APR')->after('pago_presencial_horario');
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
            $table->dropColumn(['pago_presencial_dias', 'pago_presencial_horario', 'pago_presencial_lugar']);
        });
    }
};
