<?php

namespace App\Http\Controllers\Mercurio;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio30;
use App\Models\Subsi54;

class AuthController extends Controller
{
    public function index()
    {
        $tipsoc = array();
        $coddoc = array();
        $detadoc = array();
        $codciu = array();

        foreach (Subsi54::all() as $entity) {
            $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
            $detadoc["{$entity->getCodrua()}"] = $entity->getDetdoc();
        }

        foreach (Gener09::where("codzon", '>=',  18000)->where("codzon", '<=', 19000)->get() as $entity) {
            $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
        }

        return Inertia::render('Auth/Login', [
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc
        ]);
    }

    public function register()
    {
        $tipsoc = array();
        $coddoc = array();
        $detadoc = array();
        $codciu = array();

        foreach (Subsi54::all() as $entity) {
            $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
            $detadoc["{$entity->getCodrua()}"] = $entity->getDetdoc();
        }

        foreach (Gener09::where("codzon", '>=',  18000)->where("codzon", '<=', 19000)->get() as $entity) {
            $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
        }
        return Inertia::render('Auth/Register', [
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc
        ]);
    }

    public function resetPassword()
    {
        return Inertia::render('Auth/ResetPassword');
    }
}
