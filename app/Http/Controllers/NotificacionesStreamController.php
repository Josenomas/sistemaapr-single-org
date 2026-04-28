<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificacionSistema;

class NotificacionesStreamController extends Controller
{
    public function stream(Request $request)
    {
        $userId = auth()->id();
        $orgId = auth()->user()->id_organizacion;
        $lastId = $request->get('lastId', 0);

        return response()->stream(function () use ($userId, $orgId, $lastId) {
            $timeout = time() + 30;
            $currentLastId = $lastId;

            while (time() < $timeout) {
                // Buscar SOLO nuevas notificaciones del usuario actual y su organización
                $nuevas = NotificacionSistema::where('id_usuario', $userId)
                    ->where('id_organizacion', $orgId)
                    ->where('id', '>', $currentLastId)
                    ->where('leida', 0)
                    ->orderBy('id', 'asc')
                    ->get();

                if ($nuevas->count() > 0) {
                    foreach ($nuevas as $notif) {
                        echo "data: " . json_encode([
                            'id' => $notif->id,
                            'titulo' => $notif->titulo,
                            'mensaje' => $notif->mensaje,
                            'tipo' => $notif->tipo,
                            'color' => $notif->color,
                            'icono' => $notif->icono,
                            'url' => $notif->url
                        ]) . "\n\n";

                        $currentLastId = $notif->id;
                        ob_flush();
                        flush();
                    }
                    break;
                }

                sleep(1);
                if (connection_aborted()) break;
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}
