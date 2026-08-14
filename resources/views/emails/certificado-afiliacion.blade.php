<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de afiliación</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Helvetica,Arial,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#059669;padding:20px 24px;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;">Comfaca En Línea</h1>
                            <p style="margin:8px 0 0;font-size:14px;opacity:0.95;">Certificado de afiliación</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.5;">
                                Hola, <strong>{{ $nombre }}</strong>.
                            </p>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.5;">
                                Adjuntamos el certificado solicitado desde Comfaca En Línea.
                                Por seguridad, el documento se envía únicamente al correo registrado en su cuenta.
                            </p>
                            @if (!empty($tipo_certificado))
                                <p style="margin:0 0 12px;font-size:14px;line-height:1.5;color:#4b5563;">
                                    Tipo de certificado: <strong>{{ $tipo_certificado }}</strong>
                                </p>
                            @endif
                            <p style="margin:0;font-size:14px;line-height:1.5;color:#4b5563;">
                                Si usted no solicitó este documento, ignore este mensaje o comuníquese con COMFACA.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px 24px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;">
                            Caja de Compensación Familiar del Caquetá — COMFACA
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
