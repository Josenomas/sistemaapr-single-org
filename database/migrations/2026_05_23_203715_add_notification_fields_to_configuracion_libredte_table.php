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
        Schema::table('configuracion_dte', function (Blueprint $table) {
            $table->boolean('notificar_rechazos')->default(true)->after('libredte_hash')->comment('Notificar por email cuando un DTE sea rechazado');
            $table->string('email_notificaciones', 255)->nullable()->after('notificar_rechazos')->comment('Email para recibir notificaciones de rechazos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuracion_dte', function (Blueprint $table) {
            $table->dropColumn(['notificar_rechazos', 'email_notificaciones']);
        });
    }
};
