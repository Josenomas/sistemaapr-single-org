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
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->string('banco', 100)->nullable()->after('pago_presencial_lugar');
            $table->enum('tipo_cuenta', ['Cuenta Corriente', 'Cuenta Vista', 'Cuenta de Ahorro'])->nullable()->default('Cuenta Corriente')->after('banco');
            $table->string('numero_cuenta', 50)->nullable()->after('tipo_cuenta');
            $table->string('titular_cuenta', 200)->nullable()->after('numero_cuenta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            $table->dropColumn(['banco', 'tipo_cuenta', 'numero_cuenta', 'titular_cuenta']);
        });
    }
};
