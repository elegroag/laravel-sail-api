<?php

use App\Http\Controllers\Cajas\ApruebaCertificadoController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/aprobacioncer', 'cajas.aprobacioncer', ApruebaCertificadoController::class);
