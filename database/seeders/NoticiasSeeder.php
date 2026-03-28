<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Noticia;
use App\Models\Organizacion;
use App\Models\Usuario;
use Carbon\Carbon;

class NoticiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener APR El Valle (tiene plan Enterprise)
        $orgElValle = Organizacion::where('slug', 'elvalle')->first();

        if (!$orgElValle) {
            echo "❌ No se encontró APR El Valle\n";
            return;
        }

        // Obtener admin de El Valle
        $adminElValle = Usuario::where('id_organizacion', $orgElValle->id)
                                ->where('rol', 'admin')
                                ->first();

        if (!$adminElValle) {
            echo "❌ No se encontró admin de APR El Valle\n";
            return;
        }

        // Crear noticias
        $noticias = [
            [
                'titulo' => 'Suspensión de Servicio por Mantención',
                'resumen' => 'El próximo sábado 30 de marzo se realizará mantención programada en el sector norte.',
                'contenido' => "Estimados socios:\n\nLes informamos que el día sábado 30 de marzo de 2026, entre las 09:00 y las 15:00 horas, se llevará a cabo una mantención programada en el sector norte de nuestra APR.\n\nDurante este periodo:\n- Se suspenderá el suministro de agua temporalmente\n- Se realizarán trabajos de limpieza de estanques\n- Se reemplazarán válvulas desgastadas\n\nRecomendamos:\n- Almacenar agua con anticipación\n- Programar actividades que requieran agua fuera de este horario\n\nAgradecemos su comprensión y colaboración.\n\nDirectiva APR El Valle",
                'categoria' => 'Mantenimiento',
                'estado' => 'publicada',
                'destacada' => true,
                'fecha_publicacion' => Carbon::now()->subDays(2),
            ],
            [
                'titulo' => 'Nuevo Horario de Atención',
                'resumen' => 'A partir del 1 de abril, nuestras oficinas tendrán nuevo horario de atención al público.',
                'contenido' => "Estimados socios:\n\nInformamos que a partir del 1 de abril de 2026, nuestro horario de atención al público será:\n\nLunes a Viernes: 9:00 a 13:00 hrs y 15:00 a 18:00 hrs\nSábados: 9:00 a 12:00 hrs\n\nPara consultas urgentes, pueden contactarnos al teléfono +56956789012 o vía WhatsApp.\n\nAtentamente,\nAPR El Valle",
                'categoria' => 'Anuncios',
                'estado' => 'publicada',
                'destacada' => false,
                'fecha_publicacion' => Carbon::now()->subDays(5),
            ],
            [
                'titulo' => 'Asamblea General Ordinaria 2026',
                'resumen' => 'Convocatoria a Asamblea General Ordinaria para el 15 de abril a las 18:00 horas.',
                'contenido' => "Estimados socios:\n\nPor medio de la presente, se convoca a todos los socios de APR El Valle a la Asamblea General Ordinaria que se llevará a cabo el día:\n\nFecha: Martes 15 de abril de 2026\nHora: 18:00 hrs\nLugar: Sede Social APR El Valle\n\nTABLA:\n1. Cuenta anual del Presidente\n2. Presentación de estados financieros 2025\n3. Aprobación de presupuesto 2026\n4. Renovación de directiva\n5. Varios\n\nSu asistencia es muy importante.\n\nDirectiva APR El Valle",
                'categoria' => 'Asambleas',
                'estado' => 'publicada',
                'destacada' => true,
                'fecha_publicacion' => Carbon::now()->subDays(1),
            ],
            [
                'titulo' => 'Campaña de Ahorro de Agua',
                'resumen' => 'Iniciamos campaña de concientización sobre el uso responsable del agua potable.',
                'contenido' => "Estimados socios:\n\nDebido a la escasez hídrica que afecta a nuestra región, hemos iniciado una campaña de ahorro de agua.\n\nConsejos para ahorrar agua:\n- Revisar y reparar fugas en grifos y tuberías\n- Cerrar la llave mientras se lava los dientes o enjabona\n- Regar jardines en horarios de menor evaporación (temprano en la mañana o al atardecer)\n- Utilizar la lavadora con carga completa\n- Reutilizar el agua de lavado de verduras para regar plantas\n\nRecuerden: El agua es un recurso vital. ¡Cuidémosla entre todos!\n\nAPR El Valle",
                'categoria' => 'Educación',
                'estado' => 'publicada',
                'destacada' => false,
                'fecha_publicacion' => Carbon::now()->subDays(7),
            ],
            [
                'titulo' => 'Informe de Calidad del Agua - Marzo 2026',
                'resumen' => 'Resultados de análisis mensuales de calidad del agua potable.',
                'contenido' => "Estimados socios:\n\nCompartimos los resultados de los análisis de calidad del agua correspondientes al mes de marzo de 2026:\n\nParámetros Analizados:\n- pH: 7.2 (Óptimo)\n- Cloro residual: 0.8 mg/L (Dentro de norma)\n- Turbidez: 0.5 NTU (Excelente)\n- Coliformes totales: Ausentes\n- E. Coli: Ausente\n\nCONCLUSIÓN:\nEl agua distribuida cumple con todos los estándares de calidad establecidos por la normativa vigente y es apta para consumo humano.\n\nLos análisis son realizados mensualmente por laboratorio certificado.\n\nAPR El Valle",
                'categoria' => 'Calidad',
                'estado' => 'publicada',
                'destacada' => false,
                'fecha_publicacion' => Carbon::now()->subDays(10),
            ],
            [
                'titulo' => 'Borrador: Nueva Tarifa 2027',
                'resumen' => 'Propuesta de ajuste tarifario para el próximo año.',
                'contenido' => "BORRADOR - NO PUBLICAR\n\nPropuesta de nueva tarifa para 2027:\n- Cargo fijo: $6.000\n- Valor m3: $850\n\nA discutir en asamblea.",
                'categoria' => 'Interno',
                'estado' => 'borrador',
                'destacada' => false,
                'fecha_publicacion' => Carbon::now()->addDays(30),
            ],
        ];

        foreach ($noticias as $noticia) {
            Noticia::create(array_merge($noticia, [
                'id_organizacion' => $orgElValle->id,
                'id_usuario_creador' => $adminElValle->id,
                'vistas' => rand(10, 150),
            ]));
        }

        echo "✓ 6 noticias creadas para APR El Valle\n";
        echo "  - 5 publicadas\n";
        echo "  - 1 borrador\n";
        echo "  - 2 destacadas\n\n";
        echo "Para probar:\n";
        echo "1. Inicia sesión como admin de El Valle: admin@aprelvalle.cl / admin123\n";
        echo "2. Ve al menú 'Noticias' (solo visible para Enterprise)\n";
        echo "3. Vista pública: /noticias-publicas\n";
    }
}
