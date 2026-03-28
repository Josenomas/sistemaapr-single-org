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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('codigo_activo', 50)->unique()->comment('Código único del activo');
            $table->string('nombre', 150)->comment('Nombre del activo');
            $table->enum('categoria', ['mobiliario', 'equipos_computo', 'equipos_oficina', 'herramientas', 'vehiculos', 'equipamiento_tecnico', 'otros'])->index()->comment('Categoría del activo');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->date('fecha_adquisicion')->comment('Fecha de compra/adquisición');
            $table->decimal('valor_adquisicion', 10)->comment('Valor de compra');
            $table->decimal('valor_actual', 10)->nullable()->comment('Valor actual estimado');
            $table->string('proveedor', 150)->nullable();
            $table->string('ubicacion', 150)->nullable()->comment('Ubicación física del activo');
            $table->enum('estado', ['excelente', 'bueno', 'regular', 'malo', 'en_reparacion', 'dado_de_baja'])->default('bueno')->index()->comment('Estado físico del activo');
            $table->unsignedBigInteger('id_responsable')->nullable()->index()->comment('Usuario responsable del activo');
            $table->text('observaciones')->nullable();
            $table->string('foto')->nullable()->comment('Ruta de la foto del activo');
            $table->integer('vida_util_anos')->nullable()->comment('Vida útil estimada en años');
            $table->date('fecha_ultimo_mantenimiento')->nullable();
            $table->date('proxima_revision')->nullable();
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
        Schema::dropIfExists('activos_fijos');
    }
};
