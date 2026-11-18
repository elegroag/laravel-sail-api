<?php

namespace App\Services\Cajas;

use App\Exceptions\DebugException;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UsuarioExternoService
{
    /**
     * @return array{q: string, tipo: string, estado: string, per_page: int}
     */
    public function filtersFromRequest(Request $request): array
    {
        $tipo = trim((string) $request->query('tipo', ''));
        $tipos = array_keys(function_exists('get_array_tipos') ? get_array_tipos() : []);
        if ($tipo !== '' && ! in_array($tipo, $tipos, true)) {
            $tipo = '';
        }

        $estado = trim((string) $request->query('estado', ''));
        $estados = array_keys(function_exists('get_user_estados') ? get_user_estados() : []);
        if ($estado !== '' && ! in_array($estado, $estados, true)) {
            $estado = '';
        }

        return [
            'q' => trim((string) $request->query('q', '')),
            'tipo' => $tipo,
            'estado' => $estado,
            'per_page' => max(1, min(100, (int) $request->query('per_page', 10))),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Mercurio07>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $filters = $this->filtersFromRequest($request);

        return $this->baseQuery($filters)
            ->orderByDesc('fecreg')
            ->orderBy('documento')
            ->paginate($filters['per_page'])
            ->appends($request->only(['q', 'tipo', 'estado', 'per_page']));
    }

    /**
     * @param  array{q: string, tipo: string, estado: string, per_page?: int}  $filters
     * @return Builder<Mercurio07>
     */
    public function baseQuery(array $filters): Builder
    {
        $builder = Mercurio07::query();

        if (($filters['tipo'] ?? '') !== '') {
            $builder->where('tipo', $filters['tipo']);
        }

        if (($filters['estado'] ?? '') !== '') {
            $builder->where('estado', $filters['estado']);
        }

        $q = $filters['q'] ?? '';
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $builder->where(function (Builder $sub) use ($like) {
                $sub->where('documento', 'like', $like)
                    ->orWhere('nombre', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        return $builder;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function mapRows(iterable $usuarios): array
    {
        $rows = [];
        foreach ($usuarios as $usuario) {
            $rows[] = $this->mapRow($usuario);
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    public function mapRow(Mercurio07 $usuario): array
    {
        $documento = (string) $usuario->documento;
        $tipo = (string) $usuario->tipo;
        $coddoc = (string) $usuario->coddoc;

        return [
            'documento' => $documento,
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'nombre' => (string) ($usuario->nombre ?? ''),
            'email' => strtolower((string) ($usuario->email ?? '')),
            'estado' => (string) ($usuario->estado ?? ''),
            'estado_detalle' => function_exists('get_user_estado_detalle')
                ? (string) (get_user_estado_detalle((string) $usuario->estado) ?? $usuario->estado)
                : (string) $usuario->estado,
            'tipo_detalle' => function_exists('get_tipo_detalle')
                ? (string) (get_tipo_detalle($tipo) ?? $tipo)
                : $tipo,
            'coddoc_detalle' => function_exists('coddoc_repleg_detalle')
                ? (string) (coddoc_repleg_detalle((int) $coddoc) ?: $coddoc)
                : $coddoc,
            'whatsapp' => (string) ($usuario->whatsapp ?? ''),
            'codciu' => (string) ($usuario->codciu ?? ''),
            'fecreg' => $usuario->fecreg ? (string) $usuario->fecreg : null,
            'show_url' => route('cajas.usuario.show', [
                'documento' => $documento,
                'tipo' => $tipo,
                'coddoc' => $coddoc,
            ]),
            'edit_url' => route('cajas.usuario.edit', [
                'documento' => $documento,
                'tipo' => $tipo,
                'coddoc' => $coddoc,
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapDetail(Mercurio07 $usuario): array
    {
        $row = $this->mapRow($usuario);
        $codciu = (string) ($usuario->codciu ?? '');
        $ciudad = '';
        if ($codciu !== '') {
            $ciudad = (string) (Gener09::where('codzon', $codciu)->value('detzon') ?? $codciu);
        }

        return array_merge($row, [
            'codciu_detalle' => $ciudad !== '' ? $ciudad : $codciu,
            'autoriza' => (string) ($usuario->autoriza ?? ''),
            'feccla' => $usuario->feccla ? (string) $usuario->feccla : null,
            'fecha_syncron' => $usuario->fecha_syncron ? (string) $usuario->fecha_syncron : null,
        ]);
    }

    public function findOrFail(string $documento, string $tipo, string $coddoc): Mercurio07
    {
        $user = Mercurio07::where('documento', $documento)
            ->where('tipo', $tipo)
            ->where('coddoc', $coddoc)
            ->first();

        if (! $user) {
            abort(404, 'El usuario no existe.');
        }

        return $user;
    }

    /**
     * @return array<string, string>
     */
    public function tipoOptions(): array
    {
        return function_exists('get_array_tipos') ? get_array_tipos() : [];
    }

    /**
     * @return array<string, string>
     */
    public function estadoOptions(): array
    {
        return function_exists('get_user_estados') ? get_user_estados() : [];
    }

    /**
     * @return array{
     *     coddoc: array<string, string>,
     *     tipo: array<string, string>,
     *     estado: array<string, string>,
     *     codciu: array<string, string>
     * }
     */
    public function formOptions(?Mercurio07 $usuario = null): array
    {
        $coddoc = Gener18::query()
            ->orderBy('coddoc')
            ->pluck('detdoc', 'coddoc')
            ->mapWithKeys(fn ($label, $key) => [(string) $key => (string) $label])
            ->all();

        $codciu = Gener09::query()
            ->whereBetween('codzon', ['18000', '19000'])
            ->orderBy('detzon')
            ->pluck('detzon', 'codzon')
            ->mapWithKeys(fn ($label, $key) => [(string) $key => (string) $label])
            ->all();

        if ($usuario) {
            $currentCodciu = (string) ($usuario->codciu ?? '');
            if ($currentCodciu !== '' && ! isset($codciu[$currentCodciu])) {
                $label = (string) (Gener09::where('codzon', $currentCodciu)->value('detzon') ?? $currentCodciu);
                $codciu = [$currentCodciu => $label] + $codciu;
            }

            $currentCoddoc = (string) ($usuario->coddoc ?? '');
            if ($currentCoddoc !== '' && ! isset($coddoc[$currentCoddoc])) {
                $label = function_exists('coddoc_repleg_detalle')
                    ? (string) (coddoc_repleg_detalle((int) $currentCoddoc) ?: $currentCoddoc)
                    : $currentCoddoc;
                $coddoc = [$currentCoddoc => $label] + $coddoc;
            }
        }

        return [
            'coddoc' => $coddoc,
            'tipo' => $this->tipoOptions(),
            'estado' => $this->estadoOptions(),
            'codciu' => $codciu,
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{usuario: Mercurio07, clave_plana: string|null}
     *
     * @throws ValidationException
     * @throws DebugException
     */
    public function update(string $documento, string $tipo, string $coddoc, array $input): array
    {
        $usuario = $this->findOrFail($documento, $tipo, $coddoc);
        $options = $this->formOptions($usuario);

        $validator = Validator::make($input, [
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'codciu' => ['required', 'string', Rule::in(array_keys($options['codciu']))],
            'coddoc' => ['required', 'string', Rule::in(array_keys($options['coddoc']))],
            'estado' => ['required', 'string', Rule::in(array_keys($options['estado']))],
            'autoriza' => ['nullable', 'string', Rule::in(['S', 'N'])],
            'cambiar_clave' => ['nullable', 'boolean'],
            'newclave' => ['nullable', 'string', 'min:5', 'max:40', 'confirmed'],
        ], [
            'newclave.confirmed' => 'La confirmación de la nueva clave no coincide.',
            'newclave.min' => 'La nueva clave debe tener al menos 5 caracteres.',
        ]);

        $validator->after(function ($validator) use ($input) {
            $cambiar = (bool) ($input['cambiar_clave'] ?? false);
            $clave = trim((string) ($input['newclave'] ?? ''));
            if ($cambiar && $clave === '') {
                $validator->errors()->add('newclave', 'Ingrese la nueva clave o desactive el cambio de clave.');
            }
        });

        $data = $validator->validate();

        $newCoddoc = (string) $data['coddoc'];
        if ($newCoddoc !== (string) $usuario->coddoc) {
            $exists = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $newCoddoc)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'coddoc' => 'Ya existe un usuario con ese tipo de documento.',
                ]);
            }
        }

        $clavePlana = null;
        $cambiarClave = (bool) ($data['cambiar_clave'] ?? false) && trim((string) ($data['newclave'] ?? '')) !== '';

        $payload = [
            'nombre' => trim((string) $data['nombre']),
            'email' => strtolower(trim((string) $data['email'])),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
            'codciu' => (string) $data['codciu'],
            'coddoc' => $newCoddoc,
            'estado' => (string) $data['estado'],
            'autoriza' => (string) ($data['autoriza'] ?? $usuario->autoriza ?? 'S'),
        ];

        if ($cambiarClave) {
            $clavePlana = (string) $data['newclave'];
            $payload['clave'] = clave_hash($clavePlana);
            $payload['feccla'] = date('Y-m-d');
        }

        Mercurio07::where('documento', $documento)
            ->where('tipo', $tipo)
            ->where('coddoc', $coddoc)
            ->update($payload);

        $actualizado = $this->findOrFail($documento, $tipo, $newCoddoc);

        return [
            'usuario' => $actualizado,
            'clave_plana' => $clavePlana,
        ];
    }
}
