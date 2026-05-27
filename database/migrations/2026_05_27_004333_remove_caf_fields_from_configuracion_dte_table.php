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
            // Eliminar campos CAF - Los proveedores manejan esto en sus portales
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuracion_dte', function (Blueprint $table) {
            // Restaurar campos CAF por si acaso
            $table->text('caf_boleta_39')->nullable();
            $table->text('caf_factura_33')->nullable();
            $table->text('caf_nota_credito_61')->nullable();
            $table->text('caf_nota_debito_56')->nullable();
            $table->integer('caf_boleta_desde')->nullable();
            $table->integer('caf_boleta_hasta')->nullable();
            $table->date('caf_boleta_vencimiento')->nullable();
            $table->integer('caf_factura_desde')->nullable();
            $table->integer('caf_factura_hasta')->nullable();
            $table->date('caf_factura_vencimiento')->nullable();
        });
    }
};
