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
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_apr', 200);
            $table->string('rut', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto', 100)->nullable();
            $table->string('slug', 100)->unique(); // para subdominio: slug.sistemaapr.cl
            $table->string('dominio_personalizado', 100)->nullable()->unique(); // www.aprnombre.cl

            // Relación con suscripción
            $table->foreignId('id_suscripcion')->constrained('suscripciones')->onDelete('restrict');

            // Datos de suscripción
            $table->date('fecha_inicio_suscripcion');
            $table->date('fecha_fin_suscripcion')->nullable();
            $table->enum('estado_suscripcion', ['prueba', 'activa', 'vencida', 'cancelada', 'suspendida'])->default('prueba');
            $table->integer('dias_prueba_restantes')->default(15);

            // Configuración de pago
            $table->enum('metodo_pago', ['transbank', 'mercadopago', 'transferencia', 'otro'])->nullable();
            $table->date('proximo_pago')->nullable();

            // Personalización (solo Enterprise)
            $table->string('logo', 255)->nullable();
            $table->string('color_primario', 7)->nullable(); // #hexadecimal
            $table->string('color_secundario', 7)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizaciones');
    }
};
