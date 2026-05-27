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
            // Agregar campos para timbre electrónico del SII
            $table->text('timbre_base64')->nullable()->after('xml_dte')->comment('Imagen del timbre en base64');
            $table->text('ted')->nullable()->after('timbre_base64')->comment('Timbre Electrónico Digital XML');
            $table->string('pdf_personalizado_path')->nullable()->after('ted')->comment('Ruta del PDF personalizado con timbre');
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
            $table->dropColumn(['timbre_base64', 'ted', 'pdf_personalizado_path']);
        });
    }
};
