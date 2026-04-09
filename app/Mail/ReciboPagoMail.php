<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pago;

class ReciboPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pago;

    /**
     * Create a new message instance.
     */
    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generar PDF del recibo
        $pago = $this->pago;
        $html = view('pagos.imprimir', compact('pago'))->render();

        // Guardar HTML temporal
        $tempHtmlPath = public_path('temp_recibo_email_' . $this->pago->id . '_' . time() . '.html');
        file_put_contents($tempHtmlPath, $html);

        // Generar PDF
        $pdfPath = storage_path('app/temp_recibo_email_' . $this->pago->id . '_' . time() . '.pdf');

        // Detectar sistema operativo y usar ruta correcta
        $wkhtmltopdfPath = PHP_OS_FAMILY === 'Windows'
            ? '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"'
            : '/usr/bin/wkhtmltopdf';

        // Convertir path a formato file://
        $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlPath);

        $command = $wkhtmltopdfPath . ' --enable-local-file-access --page-size Letter "' . $fileUrl . '" "' . $pdfPath . '" 2>&1';
        exec($command, $output, $returnCode);

        // Leer el PDF generado
        if (!file_exists($pdfPath)) {
            throw new \Exception('Error al generar PDF para email: ' . implode("\n", $output));
        }

        $pdfContent = file_get_contents($pdfPath);

        // Eliminar archivos temporales
        @unlink($tempHtmlPath);
        @unlink($pdfPath);

        $nombreOrg = $this->pago->socio->organizacion->nombre_apr ?? 'Sistema APR';

        return $this->subject("Comprobante de Pago #{$this->pago->numero_recibo} - {$nombreOrg}")
                    ->view('emails.recibo-pago')
                    ->with([
                        'pago' => $this->pago,
                        'socio' => $this->pago->socio
                    ])
                    ->attachData($pdfContent, 'Comprobante-' . $this->pago->numero_recibo . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
