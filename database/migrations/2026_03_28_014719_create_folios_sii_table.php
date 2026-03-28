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
        Schema::create('folios_sii', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo_documento', 50)->default('boleta')->index();
            $table->integer('folio_desde');
            $table->integer('folio_hasta');
            $table->integer('folio_actual')->index();
            $table->date('fecha_autorizacion');
            $table->date('fecha_vencimiento');
            $table->text('caf_xml')->nullable();
            $table->string('estado', 20)->default('activo')->index();
            $table->integer('folios_disponibles')->default(0);
            $table->unsignedInteger('id_usuario_carga')->index();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('folios_sii');
    }
};
