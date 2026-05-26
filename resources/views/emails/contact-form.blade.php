<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #185f35; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; }
        .field-label { font-weight: bold; color: #185f35; font-size: 14px; }
        .field-value { margin-top: 5px; color: #333333; font-size: 15px; line-height: 1.5; }
        .message-box { background-color: #f9f9f9; border-left: 4px solid #43b98e; padding: 15px; border-radius: 4px; margin-top: 5px; }
        .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nuevo mensaje de contacto</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Nombre:</div>
                <div class="field-value">{{ $nombre }}</div>
            </div>
            <div class="field">
                <div class="field-label">Correo electrónico:</div>
                <div class="field-value">{{ $emailRemitente }}</div>
            </div>
            <div class="field">
                <div class="field-label">Fecha:</div>
                <div class="field-value">{{ $fecha }}</div>
            </div>
            <div class="field">
                <div class="field-label">Asunto:</div>
                <div class="field-value">{{ $asunto }}</div>
            </div>
            <div class="field">
                <div class="field-label">Mensaje:</div>
                <div class="message-box">{{ $mensaje }}</div>
            </div>
        </div>
        <div class="footer">
            <p>Este mensaje fue enviado desde el formulario de contacto de <strong>Comfaca En Línea</strong>.</p>
        </div>
    </div>
</body>
</html>