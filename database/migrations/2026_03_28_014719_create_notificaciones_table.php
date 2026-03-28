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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->enum('tipo', ['informativa', 'importante', 'urgente', 'recordatorio', 'aviso_pago', 'corte_servicio'])->default('informativa')->index();
            $table->enum('destinatario', ['todos', 'morosos', 'activos', 'sector', 'individual'])->default('todos')->index();
            $table->unsignedBigInteger('id_socio')->nullable()->index();
            $table->string('sector', 100)->nullable()->index();
            $table->enum('estado', ['borrador', 'programada', 'enviada', 'cancelada'])->default('borrador')->index();
            $table->date('fecha_programada')->nullable()->index();
            $table->dateTime('fecha_enviada')->nullable();
            $table->enum('canal', ['sistema', 'email', 'sms', 'whatsapp', 'multiple'])->default('sistema');
            $table->boolean('enviado_email')->default(false);
            $table->boolean('enviado_sms')->default(false);
            $table->boolean('enviado_whatsapp')->default(false);
            $table->integer('total_destinatarios')->default(0);
            $table->integer('total_enviados')->default(0);
            $table->integer('total_leidos')->default(0);
            $table->integer('total_errores')->default(0);
            $table->unsignedBigInteger('id_usuario_creador');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};
