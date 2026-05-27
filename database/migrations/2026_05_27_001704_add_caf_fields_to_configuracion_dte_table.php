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
        Schema::table('configuracion_dte', function (Blueprint $table) {
            // Archivos CAF (Código de Autorización de Folios)
            $table->text('caf_boleta_39')->nullable()->after('certificado_password')->comment('CAF para Boleta Electrónica (tipo 39)');
            $table->text('caf_factura_33')->nullable()->after('caf_boleta_39')->comment('CAF para Factura Electrónica (tipo 33)');
            $table->text('caf_nota_credito_61')->nullable()->after('caf_factura_33')->comment('CAF para Nota de Crédito (tipo 61)');
            $table->text('caf_nota_debito_56')->nullable()->after('caf_nota_credito_61')->comment('CAF para Nota de Débito (tipo 56)');

            // Metadatos de los CAF
            $table->integer('caf_boleta_desde')->nullable()->after('caf_nota_debito_56')->comment('Folio inicial CAF boleta');
            $table->integer('caf_boleta_hasta')->nullable()->after('caf_boleta_desde')->comment('Folio final CAF boleta');
            $table->date('caf_boleta_vencimiento')->nullable()->after('caf_boleta_hasta')->comment('Fecha vencimiento CAF boleta');

            $table->integer('caf_factura_desde')->nullable()->after('caf_boleta_vencimiento');
            $table->integer('caf_factura_hasta')->nullable()->after('caf_factura_desde');
            $table->date('caf_factura_vencimiento')->nullable()->after('caf_factura_hasta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuracion_dte', function (Blueprint $table) {
            $table->dropColumn([
                'caf_boleta_39',
                'caf_factura_33',
                'caf_nota_credito_61',
                'caf_nota_debito_56',
                'caf_boleta_desde',
                'caf_boleta_hasta',
                'caf_boleta_vencimiento',
                'caf_factura_desde',
                'caf_factura_hasta',
                'caf_factura_vencimiento',
            ]);
        });
    }
};
