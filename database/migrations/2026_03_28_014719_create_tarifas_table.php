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
        Schema::create('tarifas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->string('nombre', 100);
            $table->enum('tipo_cliente', ['residencial', 'comercial', 'industrial'])->index('idx_tipo');
            $table->decimal('consumo_minimo', 10)->default(0)->comment('M3 incluidos en tarifa base');
            $table->decimal('tarifa_base', 10)->comment('Cargo fijo mensual');
            $table->decimal('precio_m3_excedente', 10)->comment('Precio por m3 adicional');
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['vigente_desde', 'vigente_hasta'], 'idx_vigencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarifas');
    }
};
