<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\Cajas\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function __construct(
        protected BannerService $service
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->paginate($request);

        return Inertia::render('Cajas/Banners/Index', [
            'items' => [
                'data' => $this->service->mapCollection($paginator->items()),
                'meta' => [
                    'pagination' => [
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                        'per_page' => $paginator->perPage(),
                        'from' => $paginator->firstItem(),
                        'to' => $paginator->lastItem(),
                        'total' => $paginator->total(),
                    ],
                ],
            ],
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'estado' => (string) $request->query('estado', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $result = $this->service->guardar($data, null, $request->file('imagen'));

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.banners.index')
            ->with('success', $result['msj']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        Banner::findOrFail($id);
        $data = $this->validated($request);
        $result = $this->service->guardar($data, $id, $request->file('imagen'));

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']])
                ->withInput();
        }

        return redirect()
            ->route('cajas.banners.index')
            ->with('success', $result['msj']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->service->borrar($id);

        if (! $result['flag']) {
            return redirect()
                ->back()
                ->withErrors(['form' => $result['msj']]);
        }

        return redirect()
            ->route('cajas.banners.index')
            ->with('success', $result['msj']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'estado' => ['required', Rule::in(['A', 'I'])],
            'fecha_inicia' => 'required|date',
            'fecha_finaliza' => 'required|date|after_or_equal:fecha_inicia',
            'url_imagen' => 'nullable|string|max:500',
            'content_html' => 'nullable|string',
            'imagen' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
    }
}
