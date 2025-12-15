<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        color: #333;
        max-width: 800px;
        margin: 0 auto;
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .logo-container {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 10px;
    }
    .logo {
        height: 40px; /* Ajusta el tamaño del logo */
        margin-right: 10px;
        /* Aquí iría la imagen real del logo */
    }
    .title-company {
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        color: #4CAF50; /* Color verde similar al logo */
    }
    h1 {
        font-size: 18px;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #ccc;
        padding-bottom: 5px;
        text-transform: uppercase;
    }
    .date {
        text-align: right;
        font-size: 12px;
        margin-bottom: 10px;
    }
    .body-text {
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 30px;
        text-align: justify;
    }
    .table-container {
        margin-bottom: 40px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-transform: uppercase;
    }
    .data-section {
        margin-top: 10px;
        font-size: 13px;
    }
    .data-section p {
        margin: 3px 0;
    }
    .signature-section {
        text-align: center;
        margin-top: 60px;
    }
    .signature-line {
        width: 250px;
        height: 1px;
        background-color: #000;
        margin: 0 auto 5px auto;
    }
    .signature-name {
        font-weight: bold;
        font-size: 14px;
    }
    .signature-title {
        font-size: 12px;
    }
</style>
<p class="date">Fecha: {{ $fecha }}</p>
<div class="header">
    <div class="logo-container">
        <span class="logo" style="background-color: #4CAF50; width: 40px; height: 40px; border-radius: 50%;"></span>
        <div>
            <p style="margin: 0; font-size: 18px; font-weight: bold;">Comfaca</p>
        </div>
    </div>
    <div style="text-align: left;">
        <p class="title-company">CAJA DE COMPENSACIÓN FAMILIAR DEL CAQUETA</p>
    </div>
</div>

<h1>CERTIFICADO DE AFILIACION</h1>

<div class="body-text">
    <p>
        Se certifica que el señor(a) **{{ $trabajador->nombre }}** identificado(a) con cedula de ciudadanía No 
        **{{ $trabajador->cedula }}** se encuentra afiliado(a) a esta Entidad como **“DEPENDIENTE”**, en estado **ACTIVO**,
        con categoría: **B**, presentando la siguiente trayectoria:
    </p>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nit</th>
                <th>Razón Social</th>
                <th>Fecha Afiliacion</th>
                <th>Fecha Retiro</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trayectorias as $trayectoria)
            <tr>
                <td>{{ $trayectoria->nit }}</td>
                <td>{{ $trayectoria->razon_social }}</td>
                <td>{{ $trayectoria->fecha_afiliacion }}</td>
                <td>{{ $trayectoria->fecha_retiro }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="data-section">
    <p>Calidad Empleador: **EMPRESA**</p>
    <p>Último Periodo Aportes: **202511**</p>
    <p>Estado Aportes Trabajador: **ACTIVO**</p>
</div>

<div style="margin-top: 30px; font-size: 14px;">
    <p>
        Esta certificación se expide en **FLORENCIA** a solicitud del interesado el día **2025-12-15**
    </p>
</div>
<div class="signature-section">
    <div class="signature-line"></div> 
    <p class="signature-name">YENNY PATRICIA ESTRADA OTALORA</p>
    <p class="signature-title">Jefe de Aportes y Subsidio</p>
</div>