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

        // Remover comandos USE y DELIMITER que causan problemas en Laravel
        $sql = preg_replace('/^USE `[^`]+`;/m', '', $sql);
        $sql = str_replace('DELIMITER $$', '', $sql);
        $sql = str_replace('DELIMITER ;', '', $sql);
        $sql = str_replace('$$', ';', $sql);

        // Dividir en statements individuales y ejecutar
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                DB::unprepared($statement);
            }
        }
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
