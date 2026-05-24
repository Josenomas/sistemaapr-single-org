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
            // Campos opcionales para emitir Facturas Electrónicas (tipo 33)
            $table->string('rut_receptor', 12)->nullable()->after('observaciones')->comment('RUT del receptor para factura electrónica (opcional)');
            $table->string('razon_social_receptor', 255)->nullable()->after('rut_receptor')->comment('Razón social del receptor para factura (opcional)');
            $table->string('giro_receptor', 255)->nullable()->after('razon_social_receptor')->comment('Giro del receptor (opcional)');
            $table->string('direccion_receptor', 255)->nullable()->after('giro_receptor')->comment('Dirección del receptor (opcional)');
            $table->string('comuna_receptor', 100)->nullable()->after('direccion_receptor')->comment('Comuna del receptor (opcional)');
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
            $table->dropColumn([
                'rut_receptor',
                'razon_social_receptor',
                'giro_receptor',
                'direccion_receptor',
                'comuna_receptor'
            ]);
        });
    }
};
