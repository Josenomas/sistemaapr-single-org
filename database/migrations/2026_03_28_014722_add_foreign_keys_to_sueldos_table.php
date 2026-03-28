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
        Schema::table('sueldos', function (Blueprint $table) {
            $table->foreign(['id_organizacion'])->references(['id'])->on('organizaciones')->onDelete('CASCADE');
            $table->foreign(['id_funcionario'], 'sueldos_ibfk_1')->references(['id'])->on('funcionarios')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sueldos', function (Blueprint $table) {
            $table->dropForeign('sueldos_id_organizacion_foreign');
            $table->dropForeign('sueldos_ibfk_1');
        });
    }
};
