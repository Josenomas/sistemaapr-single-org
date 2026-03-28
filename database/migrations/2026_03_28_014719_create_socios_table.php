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
        Schema::create('socios', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable();
            $table->string('numero_socio', 20)->index('idx_numero_socio');
            $table->string('rut', 12)->index('idx_rut');
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('direccion');
            $table->string('sector', 100)->nullable()->index('idx_sector')->comment('Sector o zona geogr├ífica');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('tipo_cliente', ['residencial', 'comercial', 'industrial'])->default('residencial');
            $table->boolean('exento_iva')->default(false);
            $table->decimal('subsidio_porcentaje', 5)->default(0)->comment('Porcentaje de subsidio a aplicar (ej: 50 = 50%)');
            $table->decimal('descuento_monto', 10)->default(0)->comment('Monto fijo de descuento (ej: convenio especial)');
            $table->string('observaciones_subsidio')->nullable()->comment('Descripción del subsidio o convenio (ej: Subsidio Municipal, Convenio APR)');
            $table->string('numero_medidor', 50)->nullable();
            $table->enum('estado', ['activo', 'suspendido', 'moroso', 'desconectado'])->default('activo')->index('idx_estado');
            $table->date('fecha_ingreso');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['numero_socio'], 'numero_socio');
            $table->index(['nombre', 'apellido_paterno'], 'idx_nombre');
            $table->unique(['rut'], 'rut');
            $table->index(['id_organizacion', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socios');
    }
};
