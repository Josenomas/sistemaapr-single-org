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
        Schema::table('lecturas', function (Blueprint $table) {
            // Cambiar de DECIMAL(10,2) a DECIMAL(10,3) para soportar 3 decimales
            $table->decimal('lectura_anterior', 10, 3)->change();
            $table->decimal('lectura_actual', 10, 3)->change();
            $table->decimal('consumo_m3', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lecturas', function (Blueprint $table) {
            // Revertir a 2 decimales
            $table->decimal('lectura_anterior', 10, 2)->change();
            $table->decimal('lectura_actual', 10, 2)->change();
            $table->decimal('consumo_m3', 10, 2)->change();
        });
    }
};
