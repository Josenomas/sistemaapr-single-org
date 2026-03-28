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
        Schema::table('trabajos_realizados', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_responsable'], 'trabajos_realizados_ibfk_1')->references(['id'])->on('funcionarios')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trabajos_realizados', function (Blueprint $table) {
            $table->dropForeign('trabajos_realizados_id_organizacion_foreign');
            $table->dropForeign('trabajos_realizados_ibfk_1');
        });
    }
};
