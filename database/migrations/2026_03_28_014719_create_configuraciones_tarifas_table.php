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
        Schema::create('configuraciones_tarifas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('nombre', 100)->comment('Nombre del tramo: Ej. Tramo 1, Tramo 2');
            $table->enum('tipo_cliente', ['residencial', 'comercial', 'industrial'])->default('residencial');
            $table->string('nombre_tarifa', 100)->nullable()->comment('Ej: Tarifa Residencial 2025');
            $table->decimal('consumo_desde', 10)->comment('M┬│ de inicio del tramo');
            $table->decimal('consumo_hasta', 10)->nullable()->comment('M┬│ fin del tramo (NULL = sin l├¡mite superior)');
            $table->decimal('monto', 10)->comment('Monto total a cobrar cuando el consumo cae en este tramo');
            $table->decimal('cargo_fijo', 10)->nullable()->comment('Cargo fijo mensual (ya incluido en el monto)');
            $table->decimal('iva', 5)->nullable()->default(19)->comment('Porcentaje de IVA a aplicar');
            $table->date('vigente_desde')->default('2025-01-01');
            $table->date('vigente_hasta')->nullable();
            $table->integer('orden')->default(0)->index('idx_orden')->comment('Orden de evaluaci├│n');
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['tipo_cliente', 'orden'], 'idx_tipo_orden');
            $table->index(['tipo_cliente', 'vigente_desde', 'vigente_hasta'], 'idx_tipo_vigencia');
            $table->index(['activo', 'tipo_cliente'], 'idx_activo_tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuraciones_tarifas');
    }
};
