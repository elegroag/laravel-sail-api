<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\Mercurio53Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio53Controller extends Controller
{
    public function __construct(
        protected Mercurio53Service $service
    ) {}

    public function index(): Response
    {
        return Inertia::render('Cajas/Mercurio53/Index', [
            'items' => $this->service->listar(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $result = $this->service->crear($request->file('archivo'));

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio53.index')
            ->with('success', $result['msj']);
    }

    public function update(Request $request, int $numero): RedirectResponse
    {
        $request->validate([
            'archivo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $result = $this->service->actualizar($numero, $request->file('archivo'));

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio53.index')
            ->with('success', $result['msj']);
    }

    public function destroy(int $numero): RedirectResponse
    {
        $result = $this->service->borrar($numero);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio53.index')
            ->with('success', $result['msj']);
    }

    public function moveUp(int $numero): RedirectResponse
    {
        $result = $this->service->mover($numero, 'up');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio53.index')
            ->with('success', $result['msj']);
    }

    public function moveDown(int $numero): RedirectResponse
    {
        $result = $this->service->mover($numero, 'down');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio53.index')
            ->with('success', $result['msj']);
    }
}
