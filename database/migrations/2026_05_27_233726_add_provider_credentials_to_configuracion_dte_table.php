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
            // Credenciales SimpleAPI
            $table->string('simpleapi_token', 255)->nullable()->after('proveedor_dte')->comment('Token API de SimpleAPI');

            // Credenciales SimpleFactura
            $table->string('simplefactura_usuario', 150)->nullable()->after('simpleapi_token')->comment('Usuario de SimpleFactura');
            $table->string('simplefactura_password', 150)->nullable()->after('simplefactura_usuario')->comment('Password de SimpleFactura (encriptado)');
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
                'simpleapi_token',
                'simplefactura_usuario',
                'simplefactura_password',
            ]);
        });
    }
};
