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
        Schema::table('directiva', function (Blueprint $table) {
            $table->integer('id_socio')->after('id_organizacion')->index();
            $table->enum('cargo', ['presidente', 'vicepresidente', 'secretario', 'tesorero', 'director', 'vocal', 'suplente'])->after('id_socio');
            $table->date('fecha_inicio')->after('cargo');
            $table->date('fecha_termino')->nullable()->after('fecha_inicio');
            $table->enum('estado', ['activo', 'finalizado', 'renunciado'])->default('activo')->after('fecha_termino');
            $table->string('periodo', 20)->nullable()->after('estado')->comment('Ej: 2024-2026');
            $table->string('acta_nombramiento', 255)->nullable()->after('periodo');
            $table->text('observaciones')->nullable()->after('acta_nombramiento');
            $table->boolean('activo')->default(true)->after('observaciones');

            // Renombrar timestamps
            $table->renameColumn('created_at', 'fecha_creacion');
            $table->renameColumn('updated_at', 'fecha_actualizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directiva', function (Blueprint $table) {
            $table->dropColumn([
                'id_socio',
                'cargo',
                'fecha_inicio',
                'fecha_termino',
                'estado',
                'periodo',
                'acta_nombramiento',
                'observaciones',
                'activo'
            ]);

            $table->renameColumn('fecha_creacion', 'created_at');
            $table->renameColumn('fecha_actualizacion', 'updated_at');
        });
    }
};
