<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\Mercurio57Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio57Controller extends Controller
{
    public function __construct(
        protected Mercurio57Service $service
    ) {}

    public function index(): Response
    {
        return Inertia::render('Cajas/Mercurio57/Index', [
            'items' => $this->service->listar(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['A', 'I'])],
            'archivo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $result = $this->service->crear(
            ['estado' => $data['estado']],
            $request->file('archivo')
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio57.index')
            ->with('success', $result['msj']);
    }

    public function update(Request $request, int $numpro): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['A', 'I'])],
            'archivo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $result = $this->service->actualizar(
            $numpro,
            ['estado' => $data['estado']],
            $request->file('archivo')
        );

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']])->withInput();
        }

        return redirect()
            ->route('cajas.mercurio57.index')
            ->with('success', $result['msj']);
    }

    public function destroy(int $numpro): RedirectResponse
    {
        $result = $this->service->borrar($numpro);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio57.index')
            ->with('success', $result['msj']);
    }

    public function moveUp(int $numpro): RedirectResponse
    {
        $result = $this->service->mover($numpro, 'up');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio57.index')
            ->with('success', $result['msj']);
    }

    public function moveDown(int $numpro): RedirectResponse
    {
        $result = $this->service->mover($numpro, 'down');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio57.index')
            ->with('success', $result['msj']);
    }
}
