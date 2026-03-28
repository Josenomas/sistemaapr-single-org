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
        // Tablas relacionadas con socios
        $this->addOrganizacionColumn('boletas');
        $this->addOrganizacionColumn('pagos');
        $this->addOrganizacionColumn('lecturas');
        $this->addOrganizacionColumn('historial_consumo');

        // Servicios
        $this->addOrganizacionColumn('incidentes');
        $this->addOrganizacionColumn('cortes_suministro');
        $this->addOrganizacionColumn('renovaciones_medidores');

        // RRHH
        $this->addOrganizacionColumn('funcionarios');
        $this->addOrganizacionColumn('sueldos');
        $this->addOrganizacionColumn('vacaciones');
        $this->addOrganizacionColumn('directiva');

        // Finanzas
        $this->addOrganizacionColumn('inventario');
        $this->addOrganizacionColumn('movimientos_inventario');
        $this->addOrganizacionColumn('movimientos_inventario_detalle');
        $this->addOrganizacionColumn('compras');
        $this->addOrganizacionColumn('giros_bancarios');

        // Configuración
        $this->addOrganizacionColumn('configuraciones_tarifas');
        $this->addOrganizacionColumn('tarifas');

        // Comunicación
        $this->addOrganizacionColumn('notificaciones');
        $this->addOrganizacionColumn('recordatorios');
        $this->addOrganizacionColumn('eventos');
        $this->addOrganizacionColumn('tickets');
        $this->addOrganizacionColumn('ticket_respuestas');

        // Contabilidad
        $this->addOrganizacionColumn('activos_fijos');
        $this->addOrganizacionColumn('rendiciones_mensuales');

        // Operaciones
        $this->addOrganizacionColumn('trabajos_realizados');
        $this->addOrganizacionColumn('mantenciones');

        // Transacciones (Flow)
        $this->addOrganizacionColumn('transacciones_flow');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'boletas', 'pagos', 'lecturas', 'historial_consumo',
            'incidentes', 'cortes_suministro', 'renovaciones_medidores',
            'funcionarios', 'sueldos', 'vacaciones', 'directiva',
            'inventario', 'movimientos_inventario', 'movimientos_inventario_detalle',
            'compras', 'giros_bancarios',
            'configuraciones_tarifas', 'tarifas',
            'notificaciones', 'recordatorios', 'eventos', 'tickets', 'ticket_respuestas',
            'activos_fijos', 'rendiciones_mensuales',
            'trabajos_realizados', 'mantenciones',
            'transacciones_flow'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'id_organizacion')) {
                        $table->dropForeign(['id_organizacion']);
                        $table->dropColumn('id_organizacion');
                    }
                });
            }
        }
    }

    /**
     * Helper para agregar columna id_organizacion
     */
    private function addOrganizacionColumn($tableName)
    {
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id_organizacion')) {
                    $table->foreignId('id_organizacion')
                          ->nullable()
                          ->after('id')
                          ->constrained('organizaciones')
                          ->onDelete('cascade');
                    $table->index('id_organizacion');
                }
            });
        }
    }
};
