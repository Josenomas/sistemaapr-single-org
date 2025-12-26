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
        $content = file_get_contents($sqlFile);

        // Eliminar comentarios y líneas en blanco
        $lines = explode("\n", $content);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            // Saltar comentarios y líneas vacías
            if (empty($trimmed) || substr($trimmed, 0, 2) === '--') {
                continue;
            }
            // Saltar comandos USE y DELIMITER
            if (stripos($trimmed, 'USE ') === 0 || stripos($trimmed, 'DELIMITER') === 0) {
                continue;
            }
            $cleanLines[] = $line;
        }

        // Reconstruir SQL y reemplazar delimitadores
        $sql = implode("\n", $cleanLines);
        $sql = str_replace('$$', '', $sql);

        // Ejecutar DROP y CREATE por separado
        if (preg_match('/DROP PROCEDURE IF EXISTS `sp_generar_boletas_mes`;/i', $sql, $matches)) {
            DB::unprepared('DROP PROCEDURE IF EXISTS `sp_generar_boletas_mes`');
        }

        // Extraer solo el CREATE PROCEDURE hasta el END final
        if (preg_match('/(CREATE PROCEDURE.*?END)\s*;?\s*$/s', $sql, $matches)) {
            DB::unprepared($matches[1]);
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
