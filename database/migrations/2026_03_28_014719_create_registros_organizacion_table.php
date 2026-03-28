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
        Schema::create('registros_organizacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_apr');
            $table->string('slug')->unique();
            $table->string('rut', 12)->unique();
            $table->string('direccion')->nullable();
            $table->string('comuna')->nullable();
            $table->string('region')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto')->unique();
            $table->string('admin_nombre');
            $table->string('admin_apellido');
            $table->string('admin_email')->unique();
            $table->string('admin_telefono')->nullable();
            $table->string('password');
            $table->string('token_verificacion')->index();
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado', 'expirado'])->default('pendiente')->index();
            $table->timestamp('email_verificado_at')->nullable();
            $table->timestamp('expira_en')->nullable();
            $table->string('ip_registro', 45)->nullable();
            $table->unsignedBigInteger('id_suscripcion_deseada')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['email_contacto']);
            $table->unique(['token_verificacion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registros_organizacion');
    }
};
