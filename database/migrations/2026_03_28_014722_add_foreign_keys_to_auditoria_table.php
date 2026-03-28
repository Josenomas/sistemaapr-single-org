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
        Schema::table('auditoria', function (Blueprint $table) {
            $table->foreign(['id_usuario'])->references(['id'])->on('usuarios')->onDelete('SET NULL');
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
        Schema::table('auditoria', function (Blueprint $table) {
            $table->dropForeign('auditoria_id_usuario_foreign');
            $table->dropForeign('auditoria_id_organizacion_foreign');
        });
    }
};
