<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // Modificar el enum del campo 'rol' para incluir 'superadmin'
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('superadmin', 'admin', 'tesorero', 'operador', 'lecturista') NOT NULL DEFAULT 'operador'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir a los roles originales
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'tesorero', 'operador', 'lecturista') NOT NULL DEFAULT 'operador'");
    }
};
