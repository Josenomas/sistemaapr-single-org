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
        Schema::create('ticket_respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_ticket');
            $table->unsignedInteger('id_usuario')->nullable(); // Funcionario que responde
            $table->unsignedInteger('id_socio')->nullable(); // Si responde el socio
            $table->text('mensaje');
            $table->enum('tipo_autor', ['funcionario', 'socio', 'sistema'])->default('funcionario');
            $table->boolean('visible_socio')->default(true); // Si el socio puede ver esta respuesta
            $table->boolean('notificado')->default(false); // Si se envió email
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('activo')->default(1);

            // Índices
            $table->index('id_ticket');
            $table->index('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_respuestas');
    }
};
