<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Organizacion;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartir colores del tema con todas las vistas
        View::composer('*', function ($view) {
            $colores = $this->obtenerColoresOrganizacion();
            $view->with('coloresTema', $colores);
        });
    }

    /**
     * Obtiene los colores personalizados de la organización
     */
    private function obtenerColoresOrganizacion(): array
    {
        $colorPrimario = '#2563eb';
        $colorSecundario = '#10b981';

        if (auth()->check() && auth()->user()->id_organizacion) {
            $organizacion = Organizacion::find(auth()->user()->id_organizacion);

            if ($organizacion) {
                $colorPrimario = $organizacion->color_primario ?? $colorPrimario;
                $colorSecundario = $organizacion->color_secundario ?? $colorSecundario;
            }
        }

        return [
            'primario' => $colorPrimario,
            'primario_dark' => $this->adjustBrightness($colorPrimario, -40),
            'primario_light' => $this->hexToRgba($colorPrimario, 0.1),
            'secundario' => $colorSecundario,
        ];
    }

    /**
     * Ajusta el brillo de un color hexadecimal
     */
    private function adjustBrightness(string $hex, int $steps): string
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Convierte hexadecimal a RGBA
     */
    private function hexToRgba(string $hex, float $alpha = 0.1): string
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    }
}
