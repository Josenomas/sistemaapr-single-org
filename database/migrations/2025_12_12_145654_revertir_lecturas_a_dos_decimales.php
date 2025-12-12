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
            // Revertir de DECIMAL(10,3) a DECIMAL(10,2)
            $table->decimal('lectura_anterior', 10, 2)->change();
            $table->decimal('lectura_actual', 10, 2)->change();
            $table->decimal('consumo_m3', 10, 2)->change();
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
            // Volver a 3 decimales si es necesario
            $table->decimal('lectura_anterior', 10, 3)->change();
            $table->decimal('lectura_actual', 10, 3)->change();
            $table->decimal('consumo_m3', 10, 3)->change();
        });
    }
};
