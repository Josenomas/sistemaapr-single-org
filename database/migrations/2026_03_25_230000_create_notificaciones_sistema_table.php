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
        Schema::create('notificaciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();

            // Tipo de notificación
            $table->enum('tipo', [
                'pago_pendiente',
                'pago_vencido',
                'cuenta_suspendida',
                'cuenta_reactivada',
                'limite_socios',
                'limite_usuarios',
                'cambio_plan',
                'bienvenida',
                'actualizacion_sistema',
                'mantenimiento',
                'otro'
            ])->default('otro');

            // Prioridad
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal');

            // Contenido
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('icono')->nullable(); // Font Awesome icon class
            $table->string('color')->nullable(); // primary, success, warning, danger, info

            // Acción (URL a donde redirige al hacer click)
            $table->string('url')->nullable();
            $table->string('texto_accion')->nullable();

            // Estado
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_leida')->nullable();

            // Metadata
            $table->json('metadata')->nullable(); // Datos adicionales

            $table->timestamps();

            // Índices
            $table->index('id_organizacion');
            $table->index('id_usuario');
            $table->index(['leida', 'created_at']);
            $table->index('tipo');

            // Foreign keys
            $table->foreign('id_organizacion')
                ->references('id')->on('organizaciones')
                ->onDelete('cascade');

            $table->foreign('id_usuario')
                ->references('id')->on('usuarios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
