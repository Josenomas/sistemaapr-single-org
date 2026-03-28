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
        Schema::create('lecturas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_organizacion')->nullable()->index();
            $table->integer('id_socio')->index('idx_socio');
            $table->string('mes', 7)->index('idx_mes')->comment('Formato: YYYY-MM');
            $table->decimal('lectura_anterior', 10)->default(0);
            $table->decimal('lectura_actual', 10);
            $table->decimal('consumo_m3', 10)->comment('Metros c├║bicos consumidos');
            $table->date('fecha_lectura')->index('idx_fecha_lectura');
            $table->text('observaciones')->nullable();
            $table->integer('id_usuario_registro')->nullable()->index('fk_lecturas_usuario')->comment('Usuario que registr├│ la lectura');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['id_socio', 'mes'], 'idx_lecturas_socio_mes');
            $table->unique(['id_socio', 'mes'], 'uk_socio_mes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lecturas');
    }
};
