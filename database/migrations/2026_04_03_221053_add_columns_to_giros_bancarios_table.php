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
        Schema::table('giros_bancarios', function (Blueprint $table) {
            $table->string('numero_giro', 50)->unique()->after('id');
            $table->string('banco', 100)->after('id_organizacion');
            $table->string('numero_cuenta', 50)->after('banco');
            $table->enum('tipo_cuenta', ['corriente', 'vista', 'ahorro'])->after('numero_cuenta');
            $table->string('beneficiario', 200)->after('tipo_cuenta');
            $table->string('rut_beneficiario', 12)->nullable()->after('beneficiario');
            $table->decimal('monto', 10, 2)->after('rut_beneficiario');
            $table->date('fecha_emision')->after('monto');
            $table->date('fecha_pago')->nullable()->after('fecha_emision');
            $table->string('concepto', 200)->after('fecha_pago');
            $table->text('descripcion')->nullable()->after('concepto');
            $table->enum('estado', ['emitido', 'pagado', 'anulado', 'vencido'])->default('emitido')->after('descripcion');
            $table->enum('metodo_entrega', ['retiro_sucursal', 'transferencia', 'cheque'])->after('estado');
            $table->string('numero_comprobante', 100)->nullable()->after('metodo_entrega');
            $table->integer('id_responsable')->nullable()->after('numero_comprobante');
            $table->text('observaciones')->nullable()->after('id_responsable');
            $table->boolean('activo')->default(true)->after('observaciones');

            // Renombrar timestamps
            $table->renameColumn('created_at', 'fecha_creacion');
            $table->renameColumn('updated_at', 'fecha_actualizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('giros_bancarios', function (Blueprint $table) {
            $table->dropColumn([
                'numero_giro',
                'banco',
                'numero_cuenta',
                'tipo_cuenta',
                'beneficiario',
                'rut_beneficiario',
                'monto',
                'fecha_emision',
                'fecha_pago',
                'concepto',
                'descripcion',
                'estado',
                'metodo_entrega',
                'numero_comprobante',
                'id_responsable',
                'observaciones',
                'activo'
            ]);

            $table->renameColumn('fecha_creacion', 'created_at');
            $table->renameColumn('fecha_actualizacion', 'updated_at');
        });
    }
};
