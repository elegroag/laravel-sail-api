<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio30;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio40;
use App\Models\Mercurio41;

class ParticularController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index()
    {
        return view('mercurio/particular/index', [
            'title' => 'Subsidio Empresa',
        ]);
    }

    public function historial()
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];
        $mercurio30 = Mercurio30::where('nit', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');
        $mercurio36 = Mercurio36::where('cedtra', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');
        $mercurio38 = Mercurio38::where('cedtra', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');
        $mercurio39 = Mercurio39::where('cedtra', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');
        $mercurio40 = Mercurio40::where('cedtra', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');
        $mercurio41 = Mercurio41::where('cedtra', $documento)->where('coddoc', $coddoc)->orderBy('id', 'desc');

        return view('mercurio/particular/historial', [
            'mercurio30' => $mercurio30,
            'mercurio36' => $mercurio36,
            'mercurio38' => $mercurio38,
            'mercurio39' => $mercurio39,
            'mercurio40' => $mercurio40,
            'mercurio41' => $mercurio41,
        ]);
    }
}
