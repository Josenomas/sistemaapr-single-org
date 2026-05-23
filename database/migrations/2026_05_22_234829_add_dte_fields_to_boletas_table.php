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
        Schema::table('boletas', function (Blueprint $table) {
            // Tipo de Documento Tributario Electrónico
            $table->tinyInteger('tipo_dte')->nullable()->after('numero_boleta')
                  ->comment('33=Factura, 39=Boleta, 61=NC, 56=ND');

            // Estado del DTE en el SII
            $table->enum('estado_dte', ['pendiente', 'emitida', 'aceptada', 'rechazada', 'anulada'])
                  ->nullable()->after('tipo_dte');

            // XML firmado del DTE
            $table->text('xml_dte')->nullable()->after('estado_dte')
                  ->comment('XML del documento firmado');

            // URL del PDF timbrado (desde LibreDTE)
            $table->string('pdf_url', 500)->nullable()->after('xml_dte')
                  ->comment('URL del PDF con timbre electrónico');

            // Fecha de emisión al SII
            $table->timestamp('fecha_emision_dte')->nullable()->after('pdf_url');

            // Índices
            $table->index('tipo_dte');
            $table->index('estado_dte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropIndex(['tipo_dte']);
            $table->dropIndex(['estado_dte']);

            $table->dropColumn([
                'tipo_dte',
                'estado_dte',
                'xml_dte',
                'pdf_url',
                'fecha_emision_dte'
            ]);
        });
    }
};
