<?php

use App\Http\Controllers\Cajas\ApruebaFacultativoController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobacionfac', 'cajas.aprobacionfac', ApruebaFacultativoController::class, [
    'editar' => true,
]);
