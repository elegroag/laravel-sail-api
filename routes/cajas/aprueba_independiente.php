<?php

use App\Http\Controllers\Cajas\ApruebaIndependienteController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobaindepen', 'cajas.aprobaindepen', ApruebaIndependienteController::class, [
    'editar' => true, 'aportes' => true, 'deshacer' => true, 'reaprobar' => true,
]);
