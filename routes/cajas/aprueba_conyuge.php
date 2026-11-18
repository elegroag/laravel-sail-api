<?php

use App\Http\Controllers\Cajas\ApruebaConyugeController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobacioncon', 'cajas.aprobacioncon', ApruebaConyugeController::class, [
    'editar' => true, 'deshacer' => true, 'reaprobar' => true,
]);
