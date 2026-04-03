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
        // 1. INVENTARIO - codigo_producto
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropUnique('codigo_producto');
            $table->unique(['id_organizacion', 'codigo_producto'], 'unique_codigo_producto_org');
        });

        // 2. MOVIMIENTOS_INVENTARIO - numero_movimiento
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropUnique(['numero_movimiento']);
            $table->unique(['id_organizacion', 'numero_movimiento'], 'unique_numero_movimiento_org');
        });

        // 3. ACTIVOS_FIJOS - codigo_activo
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->dropUnique(['codigo_activo']);
            $table->unique(['id_organizacion', 'codigo_activo'], 'unique_codigo_activo_org');
        });

        // 4. FUNCIONARIOS - rut
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropUnique('rut');
            $table->unique(['id_organizacion', 'rut'], 'unique_rut_org');
        });

        // 5. BOLETAS - numero_boleta
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropUnique('numero_boleta');
            $table->unique(['id_organizacion', 'numero_boleta'], 'unique_numero_boleta_org');
        });

        // 6. COMPRAS - numero_compra
        Schema::table('compras', function (Blueprint $table) {
            $table->dropUnique('numero_compra');
            $table->unique(['id_organizacion', 'numero_compra'], 'unique_numero_compra_org');
        });

        // 7. PAGOS - numero_recibo
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique('numero_recibo');
            $table->unique(['id_organizacion', 'numero_recibo'], 'unique_numero_recibo_org');
        });

        // 8. RENDICIONES_MENSUALES - codigo_rendicion
        Schema::table('rendiciones_mensuales', function (Blueprint $table) {
            $table->dropUnique(['codigo_rendicion']);
            $table->unique(['id_organizacion', 'codigo_rendicion'], 'unique_codigo_rendicion_org');
        });

        // 9. TICKETS - numero_ticket
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('numero_ticket');
            $table->unique(['id_organizacion', 'numero_ticket'], 'unique_numero_ticket_org');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir todos los cambios
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropUnique('unique_codigo_producto_org');
            $table->unique(['codigo_producto'], 'codigo_producto');
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropUnique('unique_numero_movimiento_org');
            $table->unique(['numero_movimiento']);
        });

        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->dropUnique('unique_codigo_activo_org');
            $table->unique(['codigo_activo']);
        });

        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropUnique('unique_rut_org');
            $table->unique(['rut'], 'rut');
        });

        Schema::table('boletas', function (Blueprint $table) {
            $table->dropUnique('unique_numero_boleta_org');
            $table->unique(['numero_boleta'], 'numero_boleta');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropUnique('unique_numero_compra_org');
            $table->unique(['numero_compra'], 'numero_compra');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique('unique_numero_recibo_org');
            $table->unique(['numero_recibo'], 'numero_recibo');
        });

        Schema::table('rendiciones_mensuales', function (Blueprint $table) {
            $table->dropUnique('unique_codigo_rendicion_org');
            $table->unique(['codigo_rendicion']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('unique_numero_ticket_org');
            $table->unique(['numero_ticket'], 'numero_ticket');
        });
    }
};
