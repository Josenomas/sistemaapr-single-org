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
        Schema::create('notificaciones_sistema', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->unsignedBigInteger('id_usuario')->nullable()->index();
            $table->enum('tipo', ['pago_pendiente', 'pago_vencido', 'cuenta_suspendida', 'cuenta_reactivada', 'limite_socios', 'limite_usuarios', 'cambio_plan', 'bienvenida', 'actualizacion_sistema', 'mantenimiento', 'otro'])->default('otro')->index();
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])->default('normal');
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('icono')->nullable();
            $table->string('color')->nullable();
            $table->string('url')->nullable();
            $table->string('texto_accion')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_leida')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['leida', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
