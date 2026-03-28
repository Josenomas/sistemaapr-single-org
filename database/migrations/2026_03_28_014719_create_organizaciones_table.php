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
            $table->bigIncrements('id');
            $table->string('nombre_apr', 200);
            $table->string('rut', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto', 100)->nullable();
            $table->string('slug', 100)->unique();
            $table->string('dominio_personalizado', 100)->nullable()->unique();
            $table->enum('estado_dominio_personalizado', ['sin_configurar', 'pendiente_configuracion', 'verificado_dns', 'activo_aprobado', 'rechazado', 'suspendido'])->default('sin_configurar');
            $table->timestamp('fecha_solicitud_dominio')->nullable();
            $table->timestamp('fecha_verificacion_dns')->nullable();
            $table->timestamp('fecha_aprobacion_dominio')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable()->index('organizaciones_aprobado_por_foreign');
            $table->text('observaciones_dominio')->nullable();
            $table->text('detalles_verificacion_dns')->nullable();
            $table->unsignedBigInteger('id_suscripcion')->index('organizaciones_id_suscripcion_foreign');
            $table->date('fecha_inicio_suscripcion');
            $table->date('fecha_fin_suscripcion')->nullable();
            $table->enum('estado_suscripcion', ['prueba', 'activa', 'vencida', 'cancelada', 'suspendida'])->default('prueba');
            $table->integer('dias_prueba_restantes')->default(15);
            $table->enum('metodo_pago', ['transbank', 'mercadopago', 'transferencia', 'otro'])->nullable();
            $table->date('proximo_pago')->nullable();
            $table->string('logo')->nullable();
            $table->string('color_primario', 7)->nullable();
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
