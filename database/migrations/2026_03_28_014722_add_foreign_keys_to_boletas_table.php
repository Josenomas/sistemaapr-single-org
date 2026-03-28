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
        Schema::table('boletas', function (Blueprint $table) {
            $table->foreign(['id_socio'], 'fk_boletas_socio')->references(['id'])->on('socios')->onDelete('CASCADE');
            $table->foreign(['id_lectura'], 'fk_boletas_lectura')->references(['id'])->on('lecturas')->onDelete('SET NULL');
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
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropForeign('fk_boletas_socio');
            $table->dropForeign('fk_boletas_lectura');
            $table->dropForeign('boletas_id_organizacion_foreign');
        });
    }
};
