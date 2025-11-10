import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { FilterBar } from '@/components/atomic';
import type { Componente, Validacion, DataSourceItem } from '@/types/cajas';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';

type Props = {
    componentes_validaciones: {
        data: Array<Validacion & { componente?: Componente }>;
        meta: {
            total_validaciones: number;
            pagination?: {
                current_page: number;
                last_page: number;
                per_page: number;
                from: number | null;
                to: number | null;
                total: number;
            };
        };
    };
};

export default function Index({ componentes_validaciones }: Props) {
    const { data, meta } = componentes_validaciones;

    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [componente, setComponente] = useState<Componente | null>(null);
    const [loadingComponente, setLoadingComponente] = useState(false);
    const [componenteError, setComponenteError] = useState<string | null>(null);
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [pendingDelete, setPendingDelete] = useState<{ id: number; name: string } | null>(null);

    // Filtros
    const searchParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [q, setQ] = useState<string>(searchParams.get('q') || '');
    const [isRequired, setIsRequired] = useState<string>(searchParams.get('is_required') || '');
    const perPage = meta.pagination?.per_page || 15;

    useEffect(() => {
        const handlePopstate = () => {
            const sp = new URLSearchParams(window.location.search);
            setQ(sp.get('q') || '');
            setIsRequired(sp.get('is_required') || '');
        };
        window.addEventListener('popstate', handlePopstate);
        return () => window.removeEventListener('popstate', handlePopstate);
    }, []);

    const currentFilterParams = useMemo(() => ({
        q: q || undefined,
        is_required: isRequired || undefined,
        per_page: perPage
    }), [q, isRequired, perPage]);

    const applyFilters = () => {
        router.get('/cajas/componente-validacion', { ...currentFilterParams, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const clearFilters = () => {
        setQ('');
        setIsRequired('');
        router.get('/cajas/componente-validacion', { per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const filterOptions = [
        {
            key: 'is_required',
            label: 'Requerido',
            value: isRequired,
            options: [
                { value: '', label: 'Todos' },
                { value: '1', label: 'Sí' },
                { value: '0', label: 'No' },
            ],
            onChange: (value: string) => setIsRequired(value)
        }
    ];

    const handleDelete = (_id: number, componenteName: string) => {
        setPendingDelete({ id: _id, name: componenteName });
        setConfirmOpen(true);
    };

    const confirmDelete = async () => {
        if (!pendingDelete) return;
        try {
            await router.delete(`/cajas/componente-validacion/${pendingDelete.id}`, {
                onFinish: () => {
                    setConfirmOpen(false);
                    setPendingDelete(null);
                },
            });
        } catch (error) {
            console.error('Error al eliminar validación:', error);
        }
    };

    const handleDetail = async (_id: number) => {
        try {
            setSelectedId(_id);
            setLoadingComponente(true);
            setComponenteError(null);

            // Buscar componente en los datos ya cargados
            const validacion = data.find(v => v.id === _id);
            if (validacion && validacion.componente) {
                setComponente(validacion.componente);
            } else {
                setComponente(null);
            }
        } catch (e) {
            setComponente(null);
            const msg = e instanceof Error ? e.message : 'Error desconocido';
            setComponenteError(msg);
        } finally {
            setLoadingComponente(false);
        }
    };

    const getTypeColor = (type: string) => {
        switch (type) {
            case 'input': return 'bg-blue-50 text-blue-700 border-blue-200';
            case 'select': return 'bg-green-50 text-green-700 border-green-200';
            case 'textarea': return 'bg-purple-50 text-purple-700 border-purple-200';
            case 'date': return 'bg-orange-50 text-orange-700 border-orange-200';
            case 'number': return 'bg-red-50 text-red-700 border-red-200';
            case 'dialog': return 'bg-gray-50 text-gray-700 border-gray-200';
            default: return 'bg-gray-50 text-gray-700 border-gray-200';
        }
    };

    return (
        <AppLayout title="Validaciones de Componentes">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Validaciones de Componentes Dinámicos
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Lista de todas las reglas de validación definidas para componentes dinámicos
                        </p>
                    </div>
                    <Link
                        href="/cajas/componente-validacion/create"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Nueva Validación
                    </Link>
                </div>

                {/* Filtros */}
                <div className="px-4 py-5 sm:px-6">
                    <FilterBar
                        searchValue={q}
                        onSearchChange={setQ}
                        onSearchSubmit={applyFilters}
                        filters={filterOptions}
                        onClearFilters={clearFilters}
                        loading={false}
                    />
                </div>

                {/* Estadísticas */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-indigo-600">{meta.total_validaciones}</div>
                            <div className="text-sm text-gray-500">Total Validaciones</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">{data.filter(v => v.is_required).length}</div>
                            <div className="text-sm text-gray-500">Campos Requeridos</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{data.filter(v => v.pattern).length}</div>
                            <div className="text-sm text-gray-500">Con Patrón Regex</div>
                        </div>
                    </div>
                </div>

                {/* Lista de items + detalle lateral */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div className="lg:col-span-2">
                        <ul className="divide-y divide-gray-200">
                            {data.map((validacion) => (
                                <li key={validacion.id}>
                                    <div className="px-4 py-4 sm:px-6">
                                        <div className="flex items-center justify-between">
                                            <div className="flex items-center">
                                                <div className="flex-shrink-0">
                                                    <div className="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center" onClick={() => handleDetail(validacion.id)}>
                                                        <span className="text-sm font-medium text-white">
                                                            {validacion.componente?.label?.charAt(0)?.toUpperCase() || 'V'}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="ml-4">
                                                    <div className="flex items-center">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {validacion.componente?.label || 'Componente sin nombre'}
                                                        </div>
                                                        <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                            validacion.is_required ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                                                        }`}>
                                                            {validacion.is_required ? 'Requerido' : 'Opcional'}
                                                        </span>
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {validacion.componente?.name} • {validacion.componente?.type}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {validacion.pattern && 'Patrón: ' + validacion.pattern.substring(0, 30) + (validacion.pattern.length > 30 ? '...' : '')}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                <div className="text-right">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {validacion.max_length && `Máx: ${validacion.max_length}`}
                                                        {validacion.min_length && ` Mín: ${validacion.min_length}`}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        Campo {validacion.field_size}
                                                    </div>
                                                </div>
                                                <div className="flex space-x-2">
                                                    <Link
                                                        href={`/cajas/componente-validacion/${validacion.id}/show`}
                                                        className="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                                    >
                                                        Ver
                                                    </Link>
                                                    <Link
                                                        href={`/cajas/componente-validacion/${validacion.id}/edit`}
                                                        className="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                                    >
                                                        Editar
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(validacion.id, validacion.componente?.name || 'componente')}
                                                        className="text-red-600 hover:text-red-900 text-sm font-medium"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        {validacion.detail_info && (
                                            <div className="mt-2">
                                                <p className="text-sm text-gray-600">{validacion.detail_info}</p>
                                            </div>
                                        )}
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Panel lateral de detalle del componente */}
                    <aside className="lg:col-span-1 m-2">
                        <div className="sticky top-4 bg-white shadow overflow-hidden sm:rounded-md">
                            <div className="px-4 py-5 sm:px-6 flex items-center justify-between border-b">
                                <div>
                                    <h4 className="text-sm font-semibold text-gray-900">Componente Asociado</h4>
                                    {selectedId ? (
                                        <p className="text-xs text-gray-500">Validación seleccionada: <span className="font-medium">#{selectedId}</span></p>
                                    ) : (
                                        <p className="text-xs text-gray-500">Selecciona una validación para ver su componente</p>
                                    )}
                                </div>
                            </div>
                            <div className="px-4 py-5 sm:px-6 max-h-[70vh] overflow-auto">
                                {loadingComponente && (
                                    <div className="space-y-2">
                                        <div className="h-3 w-1/2 bg-gray-200 rounded animate-pulse" />
                                        <div className="h-3 w-2/3 bg-gray-200 rounded animate-pulse" />
                                        <div className="h-3 w-1/3 bg-gray-200 rounded animate-pulse" />
                                    </div>
                                )}
                                {componenteError && (
                                    <div className="rounded-md border border-red-200 bg-red-50 text-red-700 text-sm px-3 py-2">
                                        {componenteError}
                                    </div>
                                )}
                                {!loadingComponente && !componenteError && selectedId && (
                                    <>
                                        {componente ? (
                                            <div className="space-y-4">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-sm font-medium text-gray-900">Tipo</span>
                                                    <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium border ${getTypeColor(componente.type)}`}>
                                                        {componente.type}
                                                    </span>
                                                </div>

                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">Nombre</span>
                                                    <div className="mt-1 text-sm text-gray-600">
                                                        {componente.name}
                                                    </div>
                                                </div>

                                                <div>
                                                    <span className="text-sm font-medium text-gray-900">Etiqueta</span>
                                                    <div className="mt-1 text-sm text-gray-600">
                                                        {componente.label}
                                                    </div>
                                                </div>

                                                {componente.placeholder && (
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-900">Placeholder</span>
                                                        <div className="mt-1 text-sm text-gray-600">
                                                            {componente.placeholder}
                                                        </div>
                                                    </div>
                                                )}

                                                <div className="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-900">Grupo</span>
                                                        <div className="mt-1 text-sm text-gray-600">
                                                            {componente.group_id}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-900">Orden</span>
                                                        <div className="mt-1 text-sm text-gray-600">
                                                            {componente.order}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="flex gap-4">
                                                    <div className="flex items-center">
                                                        <input
                                                            type="checkbox"
                                                            checked={componente.is_disabled}
                                                            readOnly
                                                            className="rounded border-gray-300 text-indigo-600"
                                                        />
                                                        <span className="ml-2 text-xs text-gray-600">Deshabilitado</span>
                                                    </div>
                                                    <div className="flex items-center">
                                                        <input
                                                            type="checkbox"
                                                            checked={componente.is_readonly}
                                                            readOnly
                                                            className="rounded border-gray-300 text-indigo-600"
                                                        />
                                                        <span className="ml-2 text-xs text-gray-600">Solo lectura</span>
                                                    </div>
                                                </div>

                                                {componente.type === 'select' && componente.data_source && componente.data_source.length > 0 && (
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-900">Opciones del Select</span>
                                                        <div className="mt-1 space-y-1">
                                                            {componente.data_source.slice(0, 3).map((option: DataSourceItem, index: number) => (
                                                                <div key={index} className="text-xs text-gray-600 bg-gray-50 p-1 rounded">
                                                                    {option.label} ({option.value})
                                                                </div>
                                                            ))}
                                                            {componente.data_source.length > 3 && (
                                                                <div className="text-xs text-gray-500">
                                                                    ... y {componente.data_source.length - 3} más
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                )}

                                                {componente.help_text && (
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-900">Texto de ayuda</span>
                                                        <div className="mt-1 text-sm text-gray-600">
                                                            {componente.help_text}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        ) : (
                                            <div className="text-sm text-gray-500">No se pudo cargar la información del componente.</div>
                                        )}
                                    </>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>

                {meta.pagination && (
                    <div className="bg-white px-4 py-3 border-t border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-center gap-4">
                            <div className="text-sm text-gray-700">
                                Mostrando {meta.pagination.from || 0}–{meta.pagination.to || 0} de {meta.pagination.total}
                            </div>
                            <div className="text-sm text-gray-700 flex items-center gap-2">
                                <label htmlFor="per_page" className="text-gray-600">Por página</label>
                                <select
                                    id="per_page"
                                    className="rounded-md border border-gray-300 px-2 py-1 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    value={meta.pagination.per_page}
                                    onChange={(e) => router.get('/cajas/componente-validacion', { page: 1, per_page: Number(e.target.value), q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                >
                                    {[10,15,25,50,100].map(n => (
                                        <option key={n} value={n}>{n}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="inline-flex items-center gap-2">
                            <button
                                onClick={() => router.get('/cajas/componente-validacion', { page: 1, per_page: meta.pagination!.per_page, q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                disabled={meta.pagination.current_page === 1}
                                className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Primera
                            </button>
                            <button
                                onClick={() => router.get('/cajas/componente-validacion', { page: Math.max(1, meta.pagination!.current_page - 1), per_page: meta.pagination!.per_page, q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                disabled={meta.pagination.current_page === 1}
                                className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                            >
                                Anterior
                            </button>
                            {/* Numeración de páginas */}
                            {(() => {
                                const p = meta.pagination!;
                                const start = Math.max(1, p.current_page - 2);
                                const end = Math.min(p.last_page, p.current_page + 2);
                                const pages = Array.from({ length: end - start + 1 }, (_, i) => start + i);
                                return (
                                    <div className="inline-flex gap-1">
                                        {pages.map((num) => (
                                            <button
                                                key={num}
                                                onClick={() => router.get('/cajas/componente-validacion', { page: num, per_page: p.per_page, q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                                className={`inline-flex items-center h-9 px-3 rounded-md border text-sm font-medium ${num === p.current_page ? 'bg-indigo-600 text-gray border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-300'} focus:outline-none focus:ring-2 focus:ring-indigo-500`}
                                            >
                                                {num}
                                            </button>
                                        ))}
                                    </div>
                                );
                            })()}
                            <button
                                onClick={() => router.get('/cajas/componente-validacion', { page: Math.min(meta.pagination!.last_page, meta.pagination!.current_page + 1), per_page: meta.pagination!.per_page, q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                disabled={meta.pagination.current_page === meta.pagination.last_page}
                                className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                            >
                                Siguiente
                            </button>
                            <button
                                onClick={() => router.get('/cajas/componente-validacion', { page: meta.pagination!.last_page, per_page: meta.pagination!.per_page, q: q || undefined, is_required: isRequired || undefined }, { preserveState: true, preserveScroll: true })}
                                disabled={meta.pagination.current_page === meta.pagination.last_page}
                                className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                            >
                                Última
                            </button>
                        </div>
                    </div>
                )}

                {data.length === 0 && (
                    <div className="text-center py-12">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay validaciones definidas</h3>
                        <p className="mt-1 text-sm text-gray-500">Comienza creando reglas de validación para los componentes dinámicos.</p>
                        <div className="mt-6">
                            <Link
                                href="/cajas/componente-validacion/create"
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Nueva Validación
                            </Link>
                        </div>
                    </div>
                )}
            </div>
            {/* Modal de confirmación de eliminación */}
            <Dialog open={confirmOpen} onOpenChange={setConfirmOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Confirmar eliminación</DialogTitle>
                        <DialogDescription>
                            Esta acción eliminará definitivamente las reglas de validación para "{pendingDelete?.name}". No podrás deshacerla.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <DialogClose asChild>
                            <button
                                type="button"
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </DialogClose>
                        <button
                            type="button"
                            onClick={confirmDelete}
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
