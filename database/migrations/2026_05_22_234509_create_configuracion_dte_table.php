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
        Schema::create('configuracion_dte', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_organizacion')->unique();

            // Datos del Emisor
            $table->string('rut_emisor', 12);
            $table->string('razon_social', 200);
            $table->string('giro', 200);
            $table->string('direccion_casa_matriz', 255);
            $table->string('comuna', 100);
            $table->string('ciudad', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto', 150);

            // Configuración LibreDTE
            $table->string('libredte_hash', 100)->nullable()->comment('Token de API LibreDTE');
            $table->string('libredte_url', 255)->default('https://libredte.cl');
            $table->enum('ambiente', ['certificacion', 'produccion'])->default('certificacion');

            // Certificado Digital (opcional - LibreDTE puede usar el suyo)
            $table->text('certificado_digital')->nullable()->comment('Certificado .pfx en base64');
            $table->string('certificado_password')->nullable();

            // Control de Folios
            $table->integer('folio_boleta_actual')->default(0);
            $table->integer('folio_factura_actual')->default(0);

            // Estado
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->useCurrent();

            // Índices
            $table->index('id_organizacion');
            $table->index('activo');

            // Foreign key
            $table->foreign('id_organizacion')
                  ->references('id')
                  ->on('organizaciones')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion_dte');
    }
};
