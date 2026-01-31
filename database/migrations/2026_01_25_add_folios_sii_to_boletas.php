<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->unsignedInteger('id_folio_sii')->nullable()->after('numero_boleta');
            $table->integer('folio_sii')->nullable()->after('id_folio_sii')->comment('Número de folio SII asignado');
            $table->text('timbre_electronico')->nullable()->after('folio_sii')->comment('XML del timbre electrónico');
            $table->timestamp('fecha_timbraje')->nullable()->after('timbre_electronico');

            // Índices
            $table->index('id_folio_sii');
            $table->index('folio_sii');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropIndex(['id_folio_sii']);
            $table->dropIndex(['folio_sii']);
            $table->dropColumn(['id_folio_sii', 'folio_sii', 'timbre_electronico', 'fecha_timbraje']);
        });
    }
};
