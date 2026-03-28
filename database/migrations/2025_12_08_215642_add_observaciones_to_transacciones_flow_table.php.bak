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
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('payment_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transacciones_flow', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
