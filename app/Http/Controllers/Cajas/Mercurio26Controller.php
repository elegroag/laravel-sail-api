<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\Cajas\Mercurio26Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class Mercurio26Controller extends Controller
{
    public function __construct(
        protected Mercurio26Service $service
    ) {}

    public function index(): Response
    {
        return Inertia::render('Cajas/Mercurio26/Index', [
            'items' => $this->service->listar(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tipo = $request->input('tipo');

        $data = $request->validate([
            'tipo' => ['required', Rule::in(['F', 'V'])],
            'nota' => 'nullable|string|max:255',
            'archivo' => [
                'required',
                'file',
                $tipo === 'V'
                    ? 'mimetypes:video/mp4|max:20480'
                    : 'mimes:jpg,jpeg,png|max:5120',
            ],
        ]);

        $result = $this->service->guardar(
            [
                'tipo' => $data['tipo'],
                'nota' => $data['nota'] ?? null,
            ],
            $request->file('archivo')
        );

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.mercurio26.index')
            ->with('success', $result['msj']);
    }

    public function destroy(int $numero): RedirectResponse
    {
        $result = $this->service->borrar($numero);

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio26.index')
            ->with('success', $result['msj']);
    }

    public function moveUp(int $numero): RedirectResponse
    {
        $result = $this->service->mover($numero, 'up');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio26.index')
            ->with('success', $result['msj']);
    }

    public function moveDown(int $numero): RedirectResponse
    {
        $result = $this->service->mover($numero, 'down');

        if (! $result['flag']) {
            return redirect()->back()->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.mercurio26.index')
            ->with('success', $result['msj']);
    }
}
