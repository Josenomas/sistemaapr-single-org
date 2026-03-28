<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->enum('tipo', ['informativa', 'importante', 'urgente', 'recordatorio', 'aviso_pago', 'corte_servicio'])->default('informativa');
            $table->enum('destinatario', ['todos', 'morosos', 'activos', 'sector', 'individual'])->default('todos');

            // Para destinatarios específicos
            $table->unsignedBigInteger('id_socio')->nullable();
            $table->string('sector', 100)->nullable();

            // Estado de la notificación
            $table->enum('estado', ['borrador', 'programada', 'enviada', 'cancelada'])->default('borrador');
            $table->date('fecha_programada')->nullable();
            $table->dateTime('fecha_enviada')->nullable();

            // Canal de envío
            $table->enum('canal', ['sistema', 'email', 'sms', 'whatsapp', 'multiple'])->default('sistema');
            $table->boolean('enviado_email')->default(false);
            $table->boolean('enviado_sms')->default(false);
            $table->boolean('enviado_whatsapp')->default(false);

            // Estadísticas
            $table->integer('total_destinatarios')->default(0);
            $table->integer('total_enviados')->default(0);
            $table->integer('total_leidos')->default(0);
            $table->integer('total_errores')->default(0);

            // Información de registro
            $table->unsignedBigInteger('id_usuario_creador');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Índices
            $table->index('tipo');
            $table->index('destinatario');
            $table->index('estado');
            $table->index('id_socio');
            $table->index('sector');
            $table->index('fecha_programada');
            $table->index('activo');

            // Relaciones - Comentadas temporalmente (se agregan después cuando existan las tablas)
            // $table->foreign('id_socio')->references('id')->on('socios')->onDelete('cascade');
            // $table->foreign('id_usuario_creador')->references('id')->on('usuarios')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
