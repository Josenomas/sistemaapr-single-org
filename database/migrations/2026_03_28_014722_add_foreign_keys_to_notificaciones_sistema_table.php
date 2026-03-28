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
        Schema::table('notificaciones_sistema', function (Blueprint $table) {
            $table->foreign(['id_usuario'])->references(['id'])->on('usuarios')->onDelete('CASCADE');
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
        Schema::table('notificaciones_sistema', function (Blueprint $table) {
            $table->dropForeign('notificaciones_sistema_id_usuario_foreign');
            $table->dropForeign('notificaciones_sistema_id_organizacion_foreign');
        });
    }
};
