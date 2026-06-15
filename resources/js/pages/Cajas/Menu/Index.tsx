import AppLayout from '@/layouts/AppLayout';
import { useFetch } from '@/hooks/useFetch';
import { Link, router } from '@inertiajs/react';
import { useCallback, useEffect, useMemo, useState } from 'react';

type Props = {
    menu_items: {
        data: any[];
        meta: {
            total_menu_items: number;
            menu_permisos: any[];
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

type ChildrenResponse = {
    success: boolean;
    data: any[];
    message: string;
};

type OptionsResponse = {
    success: boolean;
    data: Array<{ id: number; title: string; controller: string | null; action: string | null }>;
    message: string;
};

export default function Index({ menu_items }: Props) {
    const { data, meta } = menu_items;

    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [children, setChildren] = useState<any[]>([]);
    const [loadingChildren, setLoadingChildren] = useState(false);
    const [childrenError, setChildrenError] = useState<string | null>(null);

    // Modal agregar hijo
    const [addOpen, setAddOpen] = useState(false);
    const [options, setOptions] = useState<Array<{ id: number; title: string; controller: string | null; action: string | null }>>([]);
    const [optionsLoading, setOptionsLoading] = useState(false);
    const [optionsError, setOptionsError] = useState<string | null>(null);
    const [selectedChildId, setSelectedChildId] = useState<string>('');
    const [searchOption, setSearchOption] = useState<string>('');
    const [attaching, setAttaching] = useState(false);
    const [toast, setToast] = useState<{ type: 'success' | 'error'; message: string } | null>(null);

    // Filtros
    const searchParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [q, setQ] = useState<string>(searchParams.get('q') || '');
    const [tipo, setTipo] = useState<string>(searchParams.get('tipo') || '');
    const [codapl, setCodapl] = useState<string>(searchParams.get('codapl') || '');
    const perPage = meta.pagination?.per_page || 10;

    // Hooks para fetching
    const fetchChildren = useFetch<ChildrenResponse>({ method: 'post' });
    const fetchOptions = useFetch<OptionsResponse>({ method: 'post' });
    const fetchAttach = useFetch<{ message: string }>();

    useEffect(() => {
        // Si cambia la URL por navegación, mantener filtros en el estado (básico)
        const sp = new URLSearchParams(window.location.search);
        setQ(sp.get('q') || '');
        setTipo(sp.get('tipo') || '');
        setCodapl(sp.get('codapl') || '');
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [window.location.search]);

    useEffect(() => {
        if (fetchChildren.data) {
            setChildren(Array.isArray(fetchChildren.data.data) ? fetchChildren.data.data : []);
        }
        if (fetchChildren.error) {
            setChildren([]);
            setChildrenError(fetchChildren.error);
        }
        if (!fetchChildren.loading) {
            setLoadingChildren(false);
        }
    }, [fetchChildren.data, fetchChildren.loading, fetchChildren.error]);

    useEffect(() => {
        if (fetchOptions.data) {
            setOptions(Array.isArray(fetchOptions.data.data) ? fetchOptions.data.data : []);
        }
        if (fetchOptions.error) {
            setOptionsError(fetchOptions.error);
        }
        if (!fetchOptions.loading) {
            setOptionsLoading(false);
        }
    }, [fetchOptions.data, fetchOptions.loading, fetchOptions.error]);

    const handleDetail = useCallback(async (menu_item: any) => {
        setSelectedId(menu_item.id);
        setLoadingChildren(true);
        setChildrenError(null);
        await fetchChildren.execute(`/cajas/menu/children`, {
            id: menu_item.id,
            tipo: menu_item.tipo,
            codapl: menu_item.codapl,
        });
    }, [fetchChildren]);

    useEffect(() => {
        if (fetchAttach.data) {
            setAttaching(false);
            handleDetail({ id: selectedId, tipo, codapl });
            setAddOpen(false);
            setSelectedChildId('');
            setSearchOption('');
            setToast({ type: 'success', message: fetchAttach.data.message || 'Hijo agregado correctamente' });
        }
        if (fetchAttach.error) {
            setAttaching(false);
            setToast({ type: 'error', message: fetchAttach.error });
        }
    }, [fetchAttach.data, fetchAttach.error, handleDetail, selectedId, tipo, codapl]);

    const currentFilterParams = useMemo(() => ({ q: q || undefined, tipo: tipo || undefined, codapl: codapl || undefined }), [q, tipo, codapl]);

    const applyFilters = () => {
        router.get('/cajas/menu', { ...currentFilterParams, page: 1, per_page: perPage }, { preserveScroll: true });
    };

    const clearFilters = () => {
        setQ('');
        setTipo('');
        setCodapl('');
        router.get('/cajas/menu', { per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const handleDelete = async (_id: number, title: string) => {
        if (!confirm(`¿Estás seguro de que deseas eliminar el menu "${title}"? Esta acción no se puede deshacer.`)) {
            return;
        }

        try {
            await router.delete(`/cajas/menu/${_id}`, {
                onSuccess: () => {
                    // La página se recargará automáticamente con los datos actualizados
                },
                onError: () => {
                    alert('Error al eliminar el menu. Por favor, inténtalo de nuevo.');
                }
            });
        } catch (error) {
            console.error('Error al eliminar menu:', error);
            alert('Error al eliminar el menu. Por favor, inténtalo de nuevo.');
        }
    };

    const openAddChild = async () => {
        if (!selectedId) return;
        setAddOpen(true);
        await loadOptions('');
    };

    const loadOptions = async (q: string) => {
        if (!selectedId) return;
        setOptionsLoading(true);
        setOptionsError(null);
        const url = new URL(window.location.origin + `/cajas/menu/options`);
        if (q) url.searchParams.set('q', q);
        await fetchOptions.execute(url.toString(), { q, id: selectedId, tipo, codapl });
    };

    const attachChild = async () => {
        if (!selectedId || !selectedChildId) return;
        setAttaching(true);
        await fetchAttach.execute(`/cajas/menu/attach-child`, {
            id: selectedId,
            child_id: Number(selectedChildId),
            tipo,
            codapl,
        });
    };


    return (
        <AppLayout title="Menu Items" description="Lista de todos los menu en el sistema">
            <div className="shadow overflow-hidden sm:rounded-md m-2" key={meta.pagination?.current_page}>
                {/* Filtros */}
                <div className="m-2 px-4 sm:px-6 pb-4 bg-white border border-gray-200 rounded-lg flex justify-between items-center">
                    <div className="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <div className="sm:col-span-2">
                            <label htmlFor="q" className="block text-sm font-medium text-gray-700">Buscar</label>
                            <input
                                id="q"
                                type="text"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                                placeholder="Título, controller, action, URL..."
                                value={q}
                                onChange={(e) => setQ(e.target.value)}
                                onKeyDown={(e) => { if (e.key === 'Enter') applyFilters(); }}
                            />
                        </div>
                        <div>
                            <label htmlFor="tipo" className="block text-sm font-medium text-gray-700">Tipo</label>
                            <select
                                id="tipo"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={tipo}
                                onChange={(e) => setTipo(e.target.value)}
                            >
                                <option value="">Todos</option>
                                <option value="A">Administrador</option>
                                <option value="E">Empresa</option>
                                <option value="P">Particular</option>
                                <option value="T">Trabajador</option>
                                <option value="F">Foniñez</option>
                            </select>
                        </div>
                        <div>
                            <label htmlFor="codapl" className="block text-sm font-medium text-gray-700">Aplicación</label>
                            <select
                                id="codapl"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={codapl}
                                onChange={(e) => setCodapl(e.target.value)}
                            >
                                <option value="">Todas</option>
                                <option value="CA">CA</option>
                                <option value="ME">ME</option>
                            </select>
                        </div>
                        <div className="flex items-end gap-2">
                            <button onClick={applyFilters} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
                            <button onClick={clearFilters} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Limpiar</button>
                        </div>
                    </div>
                    <div className="px-4 py-5 sm:px-6 flex justify-end items-center">
                        <Link
                            href="/cajas/menu/create"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Nuevo item Menu
                        </Link>
                    </div>
                </div>

                {/* Lista de items + detalle lateral */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* Lista de items */}
                    <div className="lg:col-span-2 p-2">
                        <ul className="divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            {data.map((menu_item) => (
                                <li key={menu_item.id}>
                                    <div className="px-4 py-4 sm:px-6 space-y-3">
                                        {/* Fila principal */}
                                        <div className="flex items-center justify-between gap-4">
                                            {/* Info izquierda */}
                                            <div className="flex items-center gap-3 min-w-0">
                                                <button
                                                    className="shrink-0 h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center hover:bg-indigo-600 transition-colors"
                                                    onClick={() => handleDetail(menu_item)}
                                                >
                                                    <span className="text-sm font-medium text-white">
                                                        {menu_item.title.charAt(0).toUpperCase()}
                                                    </span>
                                                </button>
                                                <div className="min-w-0">
                                                    <div className="flex items-center gap-2 flex-wrap">
                                                        <span className="text-sm font-medium text-gray-900 truncate">
                                                            {menu_item.title}
                                                        </span>
                                                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium shrink-0 ${menu_item.codapl === 'CA'
                                                                ? 'bg-green-100 text-green-800'
                                                                : 'bg-red-100 text-red-800'
                                                            }`}>
                                                            {menu_item.codapl}
                                                        </span>
                                                    </div>
                                                    <div className="text-xs text-gray-500 mt-0.5">
                                                        {menu_item.controller || 'Menu Padre'} | {menu_item.action || '#'}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Info derecha */}
                                            <div className="flex items-center gap-3 shrink-0">
                                                {/* Metadatos */}
                                                <div className="hidden sm:flex items-center gap-2 text-xs text-gray-500 divide-x divide-gray-200">
                                                    <span className={menu_item.is_visible ? 'text-green-600 font-medium' : 'text-red-500'}>
                                                        {menu_item.is_visible ? 'Visible' : 'Oculto'}
                                                    </span>
                                                    <span className="pl-2">Pos: {menu_item.position}</span>
                                                    <span className="pl-2">Tipo: {menu_item.tipo || 'N/A'}</span>
                                                </div>

                                                {/* Acciones */}
                                                <div className="flex items-center gap-1 border-l border-gray-200 pl-3">
                                                    <Link
                                                        href={`/cajas/menu/${menu_item.id}/show`}
                                                        className="px-2 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                                    >
                                                        Ver
                                                    </Link>
                                                    <Link
                                                        href={`/cajas/menu/${menu_item.id}/edit`}
                                                        className="px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition-colors"
                                                    >
                                                        Editar
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(menu_item.id, menu_item.title)}
                                                        className="px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            ))}
                        </ul>

                        {meta.pagination && (
                            <div className="bg-white px-4 py-3 border-t border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mt-2">
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
                                            onChange={(e) => router.get('/cajas/menu', { page: 1, per_page: Number(e.target.value), ...currentFilterParams }, { preserveScroll: true })}
                                        >
                                            {[5, 10, 25, 50, 100].map(n => (
                                                <option key={n} value={n}>{n}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>
                                <div className="inline-flex items-center gap-2">
                                    <button
                                        onClick={() => router.get('/cajas/menu', { page: 1, per_page: meta.pagination!.per_page, ...currentFilterParams }, { preserveState: true, preserveScroll: true })}
                                        disabled={meta.pagination.current_page === 1}
                                        className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Primera
                                    </button>
                                    <button
                                        onClick={() => router.get('/cajas/menu', { page: Math.max(1, meta.pagination!.current_page - 1), per_page: meta.pagination!.per_page, ...currentFilterParams }, { preserveState: true, preserveScroll: true })}
                                        disabled={meta.pagination.current_page === 1}
                                        className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                                    >
                                        Anterior
                                    </button>
                                    {/* Numeración de páginas (ventana de 5) */}
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
                                                        onClick={() => router.get('/cajas/menu', { page: num, per_page: p.per_page, ...currentFilterParams }, { preserveState: true, preserveScroll: true })}
                                                        className={`inline-flex items-center h-9 px-3 rounded-md border text-sm font-medium ${num === p.current_page ? 'bg-indigo-600 text-gray border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-300'} focus:outline-none focus:ring-2 focus:ring-indigo-500`}
                                                    >
                                                        {num}
                                                    </button>
                                                ))}
                                            </div>
                                        );
                                    })()}
                                    <button
                                        onClick={() => router.get('/cajas/menu', { page: Math.min(meta.pagination!.last_page, meta.pagination!.current_page + 1), per_page: meta.pagination!.per_page, ...currentFilterParams }, { preserveState: true, preserveScroll: true })}
                                        disabled={meta.pagination.current_page === meta.pagination.last_page}
                                        className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                                    >
                                        Siguiente
                                    </button>
                                    <button
                                        onClick={() => router.get('/cajas/menu', { page: meta.pagination!.last_page, per_page: meta.pagination!.per_page, ...currentFilterParams }, { preserveState: true, preserveScroll: true })}
                                        disabled={meta.pagination.current_page === meta.pagination.last_page}
                                        className="px-3 py-1 border rounded disabled:opacity-50 text-gray-600 hover:text-gray-900"
                                    >
                                        Última
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Panel lateral de detalle de hijos */}
                    <aside className="lg:col-span-1 m-2">
                        <div className="sticky top-4 rounded-xl bg-white shadow-md ring-1 ring-gray-200 overflow-hidden">
                            <div className="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                <div>
                                    <h4 className="text-sm font-semibold text-gray-900">Detalle del Item</h4>
                                    {selectedId ? (
                                        <p className="text-xs text-gray-500">Item seleccionado: <span className="font-medium">#{selectedId}</span></p>
                                    ) : (
                                        <p className="text-xs text-gray-500">Selecciona un item para ver sus hijos</p>
                                    )}
                                </div>
                                {selectedId && (
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs text-gray-500">
                                            {children.length} hijo{children.length === 1 ? '' : 's'}
                                        </span>
                                        <button
                                            onClick={openAddChild}
                                            className="inline-flex items-center h-8 px-2.5 rounded-md border border-gray-300 text-xs font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        >
                                            Agregar
                                        </button>
                                    </div>
                                )}
                            </div>
                            <div className="p-4 max-h-[70vh] overflow-auto">
                                {loadingChildren && (
                                    <div className="space-y-2">
                                        <div className="h-3 w-1/2 bg-gray-200 rounded animate-pulse" />
                                        <div className="h-3 w-2/3 bg-gray-200 rounded animate-pulse" />
                                        <div className="h-3 w-1/3 bg-gray-200 rounded animate-pulse" />
                                    </div>
                                )}
                                {childrenError && (
                                    <div className="rounded-md border border-red-200 bg-red-50 text-red-700 text-sm px-3 py-2">
                                        {childrenError}
                                    </div>
                                )}
                                {!loadingChildren && !childrenError && selectedId && (
                                    <>
                                        {children.length === 0 ? (
                                            <div className="text-sm text-gray-500">Este item no tiene hijos.</div>
                                        ) : (
                                            <ul className="space-y-3">
                                                {children.map((child) => (
                                                    <li key={child.id} className="rounded-lg border border-gray-200 p-3 hover:border-indigo-200 transition-colors">
                                                        <div className="flex items-start gap-3">
                                                            <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-semibold">
                                                                {child.title?.charAt(0)?.toUpperCase()}
                                                            </div>
                                                            <div className="flex-1 min-w-0">
                                                                <div className="flex items-center justify-between gap-2">
                                                                    <div className="truncate text-sm font-medium text-gray-900" title={child.title}>{child.title}</div>
                                                                    <Link
                                                                        href={`/cajas/menu/${child.id}/edit`}
                                                                        className="text-indigo-600 hover:text-indigo-800 text-xs font-medium shrink-0"
                                                                    >
                                                                        Editar
                                                                    </Link>
                                                                </div>
                                                                <div className="mt-0.5 text-xs text-gray-500 truncate" title={`${child.controller || '—'} | ${child.action || '—'}`}>
                                                                    {child.controller || '—'} | {child.action || '—'}
                                                                </div>
                                                                <div className="mt-2 flex flex-wrap items-center gap-2">
                                                                    <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                                                                        Tipo: {child.tipo ?? 'N/A'}
                                                                    </span>
                                                                    <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                                                                        Pos: {child.position ?? '—'}
                                                                    </span>
                                                                    <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset ${child.is_visible ? 'bg-green-50 text-green-700 ring-green-200' : 'bg-red-50 text-red-700 ring-red-200'}`}>
                                                                        {child.is_visible ? 'Visible' : 'Oculto'}
                                                                    </span>
                                                                </div>
                                                                {child.default_url && (
                                                                    <div className="mt-2 text-[11px] text-gray-500 break-all">
                                                                        {child.default_url}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                    </>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>

                {/* Modal agregar hijo */}
                {addOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center">
                        <div className="absolute inset-0 bg-black/40" onClick={() => setAddOpen(false)} />
                        <div className="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                            <div className="px-4 py-3 border-b flex items-center justify-between">
                                <h3 className="text-sm font-semibold text-gray-900">Agregar hijo al item #{selectedId}</h3>
                                <button onClick={() => setAddOpen(false)} className="text-gray-500 hover:text-gray-700">✕</button>
                            </div>
                            <div className="p-4 space-y-3">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Buscar</label>
                                    <div className="mt-1 flex gap-2">
                                        <input
                                            type="text"
                                            className="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600"
                                            placeholder="Título, controller, action"
                                            value={searchOption}
                                            onChange={(e) => setSearchOption(e.target.value)}
                                            onKeyDown={(e) => { if (e.key === 'Enter') loadOptions(searchOption); }}
                                        />
                                        <button onClick={() => loadOptions(searchOption)} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300">Buscar</button>
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Seleccionar item</label>
                                    <select
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                                        value={selectedChildId}
                                        onChange={(e) => setSelectedChildId(e.target.value)}
                                    >
                                        <option value="">— Selecciona —</option>
                                        {options.map(opt => (
                                            <option key={opt.id} value={opt.id}>
                                                {opt.title}
                                            </option>
                                        ))}
                                    </select>
                                    {optionsLoading && <p className="mt-1 text-xs text-gray-500">Cargando opciones…</p>}
                                    {optionsError && <p className="mt-1 text-xs text-red-600">{optionsError}</p>}
                                </div>
                            </div>
                            <div className="px-4 py-3 border-t flex justify-end gap-2">
                                <button onClick={() => setAddOpen(false)} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button onClick={attachChild} disabled={!selectedChildId || attaching} className="inline-flex items-center h-9 px-3 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                                    {attaching ? 'Agregando…' : 'Agregar'}
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Toast simple (alineado con Register.tsx) */}
                {toast && (
                    <div
                        className={`fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`}
                    >
                        {toast.message}
                        <button
                            type="button"
                            className="ml-3 underline text-white/90 hover:text-white"
                            onClick={() => setToast(null)}
                        >
                            Cerrar
                        </button>
                    </div>
                )}



                {data.length === 0 && (
                    <div className="text-center py-12">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay items de menu</h3>
                        <p className="mt-1 text-sm text-gray-500">Comienza creando un nuevo item de menu.</p>
                        <div className="mt-6">
                            <Link
                                href="/cajas/menu/create"
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Nuevo Item Menu
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
