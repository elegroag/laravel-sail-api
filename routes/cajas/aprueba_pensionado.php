<?php

use App\Http\Controllers\Cajas\ApruebaPensionadoController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobacionpen', 'cajas.aprobacionpen', ApruebaPensionadoController::class, [
    'editar' => true, 'aportes' => true, 'deshacer' => true, 'reaprobar' => true,
]);
