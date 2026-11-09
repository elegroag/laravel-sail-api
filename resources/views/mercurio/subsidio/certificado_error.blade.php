<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Error al generar el certificado</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" />
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #f5f7fb;
            font-family: -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #334155;
        }
        .certificado-error {
            max-width: 32rem;
            padding: 2rem;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        .certificado-error__icon {
            font-size: 2.5rem;
            color: #dc3545;
        }
        .certificado-error__title {
            margin: 0.75rem 0 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .certificado-error__message {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.55;
        }
    </style>
</head>
<body>
    <div class="certificado-error">
        <div class="certificado-error__icon">&#9888;</div>
        <h1 class="certificado-error__title">No se pudo generar el certificado</h1>
        <p class="certificado-error__message">{{ $mensaje ?? 'Ocurrió un error inesperado al procesar la solicitud.' }}</p>
    </div>
</body>
</html>
