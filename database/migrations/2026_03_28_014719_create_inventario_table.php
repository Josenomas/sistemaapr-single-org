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
        Schema::create('inventario', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('codigo_producto', 50)->unique('codigo_producto');
            $table->string('nombre', 200)->index('idx_nombre');
            $table->enum('categoria', ['materiales', 'equipos', 'herramientas', 'insumos', 'quimicos', 'repuestos', 'otro'])->default('materiales')->index('idx_categoria');
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 50);
            $table->decimal('cantidad_actual', 10)->default(0);
            $table->decimal('cantidad_minima', 10)->default(0);
            $table->decimal('cantidad_maxima', 10)->nullable();
            $table->decimal('precio_unitario', 10)->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->string('proveedor', 200)->nullable();
            $table->date('fecha_ultima_compra')->nullable();
            $table->date('fecha_ultimo_movimiento')->nullable();
            $table->enum('estado', ['disponible', 'agotado', 'bajo_stock', 'descontinuado'])->default('disponible')->index('idx_estado');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->index(['codigo_producto'], 'idx_codigo_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario');
    }
};
