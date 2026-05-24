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
            // Credenciales de certificación (opcional, para pruebas)
            $table->string('libredte_hash_certificacion', 255)->nullable()->after('libredte_url')
                  ->comment('Hash de autenticación LibreDTE para ambiente de certificación');

            $table->string('libredte_url_certificacion', 255)->nullable()->after('libredte_hash_certificacion')
                  ->comment('URL base de LibreDTE para ambiente de certificación');
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
            $table->dropColumn(['libredte_hash_certificacion', 'libredte_url_certificacion']);
        });
    }
};
