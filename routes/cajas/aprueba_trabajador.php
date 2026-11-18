<?php

use App\Http\Controllers\Cajas\ApruebaTrabajadorController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobaciontra', 'cajas.aprobaciontra', ApruebaTrabajadorController::class, [
    'editar' => true, 'aportes' => true, 'deshacer' => true, 'reaprobar' => true,
    'multiafiliacion' => true, 'trayectoria' => true,
]);
