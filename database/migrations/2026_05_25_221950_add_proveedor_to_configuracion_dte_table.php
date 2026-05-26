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
            // Agregar campo para seleccionar proveedor DTE (libredte o simpleapi)
            $table->enum('proveedor_dte', ['libredte', 'simpleapi'])
                  ->default('libredte')
                  ->after('activo')
                  ->comment('Proveedor de facturación electrónica a utilizar');
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
            $table->dropColumn('proveedor_dte');
        });
    }
};
