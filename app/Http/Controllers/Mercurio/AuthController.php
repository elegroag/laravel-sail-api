<?php

namespace App\Http\Controllers\Mercurio;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }

    public function register()
    {
        return Inertia::render('Auth/Register');
    }

    public function passwordRequest()
    {
        return Inertia::render('Auth/ResetPassword');
    }
}
