<?php

use App\Http\Controllers\Cajas\ApruebaUpEmpresaController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/actualizaemp', 'cajas.actualizaemp', ApruebaUpEmpresaController::class);
