<?php

use App\Http\Controllers\Cajas\ApruebaUpTrabajadorController;
use App\Http\Routing\SolicitudRoutes;

SolicitudRoutes::register('/cajas/actualizatra', 'cajas.actualizatra', ApruebaUpTrabajadorController::class, ['devolver' => false]);
