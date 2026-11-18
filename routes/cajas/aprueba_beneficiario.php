<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobacionben', 'cajas.aprobacionben', ApruebaBeneficiarioController::class, [
    'editar' => true, 'deshacer' => true, 'reaprobar' => true,
]);
