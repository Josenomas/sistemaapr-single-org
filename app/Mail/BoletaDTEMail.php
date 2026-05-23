<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Boleta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoletaDTEMail extends Mailable
{
    use Queueable, SerializesModels;

    public $boleta;

    /**
     * Create a new message instance.
     */
    public function __construct(Boleta $boleta)
    {
        $this->boleta = $boleta;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Verificar que tenga PDF timbrado
        if (!$this->boleta->pdf_url) {
            Log::warning("Boleta {$this->boleta->id} no tiene PDF timbrado para enviar por email");
            throw new \Exception('La boleta no tiene PDF timbrado disponible');
        }

        try {
            // Descargar PDF timbrado desde LibreDTE
            $pdfContent = $this->descargarPdfTimbrado($this->boleta->pdf_url);

            $tipoDte = $this->boleta->tipo_dte == 39 ? 'Boleta Electrónica' : 'Documento Tributario Electrónico';
            $nombreArchivo = 'DTE_' . $this->boleta->numero_boleta . '_F' . $this->boleta->folio_sii . '.pdf';

            return $this->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject("{$tipoDte} N° {$this->boleta->numero_boleta} - {$this->boleta->mes_texto}")
                        ->view('emails.boleta-dte')
                        ->with([
                            'boleta' => $this->boleta,
                            'socio' => $this->boleta->socio,
                            'tipoDte' => $tipoDte
                        ])
                        ->attachData($pdfContent, $nombreArchivo, [
                            'mime' => 'application/pdf',
                        ]);

        } catch (\Exception $e) {
            Log::error("Error al generar email de DTE para boleta {$this->boleta->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Descargar PDF timbrado desde LibreDTE o URL externa
     */
    private function descargarPdfTimbrado($url)
    {
        try {
            // Intentar descargar con HTTP client
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            // Fallback: usar file_get_contents
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Sistema APR',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $content = file_get_contents($url, false, $context);

            if ($content === false) {
                throw new \Exception("No se pudo descargar el PDF desde: {$url}");
            }

            return $content;

        } catch (\Exception $e) {
            Log::error("Error al descargar PDF timbrado: " . $e->getMessage(), [
                'url' => $url,
                'boleta_id' => $this->boleta->id
            ]);
            throw new \Exception("Error al descargar PDF timbrado: " . $e->getMessage());
        }
    }
}
