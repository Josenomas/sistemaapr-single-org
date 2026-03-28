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
        Schema::create('compras', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('numero_compra', 50)->index('idx_numero_compra');
            $table->date('fecha_compra')->index('idx_fecha_compra');
            $table->string('proveedor', 200)->index('idx_proveedor');
            $table->string('rut_proveedor', 12)->nullable();
            $table->enum('tipo_compra', ['materiales', 'equipos', 'herramientas', 'insumos', 'servicios', 'otro'])->default('materiales')->index('idx_tipo_compra');
            $table->text('descripcion');
            $table->decimal('cantidad', 10);
            $table->string('unidad_medida', 50)->nullable();
            $table->decimal('precio_unitario', 10);
            $table->decimal('subtotal', 10);
            $table->decimal('iva', 10)->nullable()->default(0);
            $table->decimal('total', 10);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'credito'])->default('transferencia');
            $table->string('numero_factura', 50)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente')->index('idx_estado');
            $table->integer('id_responsable')->nullable()->index('idx_id_responsable')->comment('ID del funcionario responsable');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true)->index('idx_activo');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['numero_compra'], 'numero_compra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
};
