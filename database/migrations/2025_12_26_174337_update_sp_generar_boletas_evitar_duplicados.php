<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Leer el archivo SQL del procedimiento almacenado
        $sqlFile = database_path('migrations/2025_11_29_sp_generar_boletas_mes_v2.sql');
        $sql = file_get_contents($sqlFile);

        // Ejecutar el SQL (el archivo ya tiene DROP PROCEDURE IF EXISTS)
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No hay rollback para stored procedures
        // Se mantiene la última versión
    }
};
