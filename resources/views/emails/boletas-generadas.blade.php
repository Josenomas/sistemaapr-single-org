@extends('emails.layouts.base')

@section('content')
<tr>
    <td style="padding: 40px 30px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-block; background: #10B981; color: white; border-radius: 50%; width: 60px; height: 60px; line-height: 60px; font-size: 30px;">
                ✓
            </div>
        </div>

        <h2 style="color: #1F2937; margin: 0 0 20px 0; text-align: center;">
            Generación de Boletas Completada
        </h2>

        <p style="color: #4B5563; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
            La generación masiva de boletas ha finalizado exitosamente.
        </p>

        <div style="background: #F3F4F6; border-radius: 8px; padding: 20px; margin: 0 0 25px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #6B7280; font-size: 14px;">
                        <strong>Período:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #1F2937; font-size: 14px; text-align: right;">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $data['mes'])->locale('es')->isoFormat('MMMM YYYY') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6B7280; font-size: 14px;">
                        <strong>Boletas generadas:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #10B981; font-size: 18px; font-weight: bold; text-align: right;">
                        {{ number_format($data['total_generadas'], 0, ',', '.') }}
                    </td>
                </tr>
                @if($data['folios_asignados'] > 0)
                <tr>
                    <td style="padding: 8px 0; color: #6B7280; font-size: 14px;">
                        <strong>Folios SII asignados:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #3B82F6; font-size: 16px; font-weight: bold; text-align: right;">
                        {{ number_format($data['folios_asignados'], 0, ',', '.') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; color: #6B7280; font-size: 14px;">
                        <strong>Tiempo de proceso:</strong>
                    </td>
                    <td style="padding: 8px 0; color: #1F2937; font-size: 14px; text-align: right;">
                        {{ $data['tiempo_proceso'] }} segundos
                    </td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('boletas.index') }}" 
               style="display: inline-block; background: #3B82F6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 500;">
                Ver Boletas Generadas
            </a>
        </div>

        <p style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0; text-align: center;">
            Las boletas ya están disponibles en el sistema y puedes comenzar a enviarlas a los socios.
        </p>
    </td>
</tr>
@endsection
