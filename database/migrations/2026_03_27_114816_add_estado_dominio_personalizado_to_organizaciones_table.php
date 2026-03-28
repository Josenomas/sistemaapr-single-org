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
            $table->enum('estado_dominio_personalizado', [
                'sin_configurar',           // No ha ingresado dominio
                'pendiente_configuracion',  // Ingresó pero DNS mal configurado
                'verificado_dns',           // DNS OK, funcionando, esperando aprobación
                'activo_aprobado',          // Super Admin aprobó
                'rechazado',                // Super Admin rechazó solicitud inicial
                'suspendido'                // Super Admin suspendió después de estar activo
            ])->default('sin_configurar')->after('dominio_personalizado');

            $table->timestamp('fecha_solicitud_dominio')->nullable()->after('estado_dominio_personalizado');
            $table->timestamp('fecha_verificacion_dns')->nullable()->after('fecha_solicitud_dominio');
            $table->timestamp('fecha_aprobacion_dominio')->nullable()->after('fecha_verificacion_dns');
            $table->unsignedBigInteger('aprobado_por')->nullable()->after('fecha_aprobacion_dominio');
            $table->text('observaciones_dominio')->nullable()->after('aprobado_por');
            $table->text('detalles_verificacion_dns')->nullable()->after('observaciones_dominio');

            // Foreign key
            $table->foreign('aprobado_por')->references('id')->on('usuarios')->onDelete('set null');
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
            $table->dropForeign(['aprobado_por']);
            $table->dropColumn([
                'estado_dominio_personalizado',
                'fecha_solicitud_dominio',
                'fecha_verificacion_dns',
                'fecha_aprobacion_dominio',
                'aprobado_por',
                'observaciones_dominio',
                'detalles_verificacion_dns'
            ]);
        });
    }
};
