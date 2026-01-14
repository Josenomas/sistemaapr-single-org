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
        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_activo', 50)->unique()->comment('Código único del activo');
            $table->string('nombre', 150)->comment('Nombre del activo');
            $table->enum('categoria', [
                'mobiliario',
                'equipos_computo',
                'equipos_oficina',
                'herramientas',
                'vehiculos',
                'equipamiento_tecnico',
                'otros'
            ])->comment('Categoría del activo');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->date('fecha_adquisicion')->comment('Fecha de compra/adquisición');
            $table->decimal('valor_adquisicion', 10, 2)->comment('Valor de compra');
            $table->decimal('valor_actual', 10, 2)->nullable()->comment('Valor actual estimado');
            $table->string('proveedor', 150)->nullable();
            $table->string('ubicacion', 150)->nullable()->comment('Ubicación física del activo');
            $table->enum('estado', [
                'excelente',
                'bueno',
                'regular',
                'malo',
                'en_reparacion',
                'dado_de_baja'
            ])->default('bueno')->comment('Estado físico del activo');
            $table->unsignedBigInteger('id_responsable')->nullable()->comment('Usuario responsable del activo');
            $table->text('observaciones')->nullable();
            $table->string('foto', 255)->nullable()->comment('Ruta de la foto del activo');
            $table->integer('vida_util_anos')->nullable()->comment('Vida útil estimada en años');
            $table->date('fecha_ultimo_mantenimiento')->nullable();
            $table->date('proxima_revision')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Índices
            $table->index('categoria');
            $table->index('estado');
            $table->index('id_responsable');
            $table->index('activo');

            // Llave foránea
            $table->foreign('id_responsable')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activos_fijos');
    }
};
