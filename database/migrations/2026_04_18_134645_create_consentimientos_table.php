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
        Schema::create('consentimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion');
            $table->string('documento_aceptado'); // 'politica_privacidad', 'terminos_condiciones'
            $table->string('version_documento')->default('1.0');
            $table->boolean('acepto')->default(true);
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_aceptacion');
            $table->timestamps();

            $table->foreign('id_organizacion')->references('id')->on('organizaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consentimientos');
    }
};
