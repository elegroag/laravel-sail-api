import AppLayout from '@/layouts/AppLayout';
import { useFetch } from '@/hooks/useFetch';
import { CajasMenuIcon } from '@/pages/Cajas/components/CajasMenuIcon';
import {
    cajasActionDangerClass,
    cajasActionLinkClass,
    cajasActionMutedClass,
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router } from '@inertiajs/react';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Eye, ListTree, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useCallback, useEffect, useMemo, useState } from 'react';

type TipoOption = {
    value: string;
    label: string;
};

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
    tipos: TipoOption[];
};

type MenuTipoRelation = {
    id: number;
    tipo: string;
    detalle: string;
    is_visible: boolean;
    position: number | null;
};

type ChildrenResponse = {
    success: boolean;
    data: any[];
    tipos?: MenuTipoRelation[];
    message: string;
};

type OptionsResponse = {
    success: boolean;
    data: Array<{ id: number; title: string; controller: string | null; action: string | null }>;
    message: string;
};

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ menu_items, tipos }: Props) {
    const { data, meta } = menu_items;

    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [selectedItem, setSelectedItem] = useState<any | null>(null);
    const [children, setChildren] = useState<any[]>([]);
    const [itemTipos, setItemTipos] = useState<MenuTipoRelation[]>([]);
    const [loadingChildren, setLoadingChildren] = useState(false);
    const [childrenError, setChildrenError] = useState<string | null>(null);

    const [addOpen, setAddOpen] = useState(false);
    const [options, setOptions] = useState<Array<{ id: number; title: string; controller: string | null; action: string | null }>>([]);
    const [optionsLoading, setOptionsLoading] = useState(false);
    const [optionsError, setOptionsError] = useState<string | null>(null);
    const [selectedChildId, setSelectedChildId] = useState<string>('');
    const [searchOption, setSearchOption] = useState<string>('');
    const [attaching, setAttaching] = useState(false);
    const [toast, setToast] = useState<{ type: 'success' | 'error'; message: string } | null>(null);

    const searchParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [q, setQ] = useState<string>(searchParams.get('q') || '');
    const [tipo, setTipo] = useState<string>(searchParams.get('tipo') || '');
    const [codapl, setCodapl] = useState<string>(searchParams.get('codapl') || '');
    const perPage = meta.pagination?.per_page || 10;

    const fetchChildren = useFetch<ChildrenResponse>({ method: 'post' });
    const fetchOptions = useFetch<OptionsResponse>({ method: 'post' });
    const fetchAttach = useFetch<{ message: string }>();

    useEffect(() => {
        const sp = new URLSearchParams(window.location.search);
        setQ(sp.get('q') || '');
        setTipo(sp.get('tipo') || '');
        setCodapl(sp.get('codapl') || '');
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [window.location.search]);

    useEffect(() => {
        if (fetchChildren.data) {
            setChildren(Array.isArray(fetchChildren.data.data) ? fetchChildren.data.data : []);
            setItemTipos(Array.isArray(fetchChildren.data.tipos) ? fetchChildren.data.tipos : []);
        }
        if (fetchChildren.error) {
            setChildren([]);
            setItemTipos([]);
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
        setSelectedItem(menu_item);
        setLoadingChildren(true);
        setChildrenError(null);
        setItemTipos([]);
        await fetchChildren.execute(`/cajas/menu/children`, {
            id: menu_item.id,
            tipo: menu_item.tipo,
            codapl: menu_item.codapl,
        });
    }, [fetchChildren]);

    useEffect(() => {
        if (fetchAttach.data) {
            setAttaching(false);
            handleDetail(selectedItem ?? { id: selectedId, tipo, codapl });
            setAddOpen(false);
            setSelectedChildId('');
            setSearchOption('');
            setToast({ type: 'success', message: fetchAttach.data.message || 'Hijo agregado correctamente' });
        }
        if (fetchAttach.error) {
            setAttaching(false);
            setToast({ type: 'error', message: fetchAttach.error });
        }
    }, [fetchAttach.data, fetchAttach.error, handleDetail, selectedId, selectedItem, tipo, codapl]);

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
                onError: () => {
                    alert('Error al eliminar el menu. Por favor, inténtalo de nuevo.');
                },
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

    const loadOptions = async (query: string) => {
        if (!selectedId) return;
        setOptionsLoading(true);
        setOptionsError(null);
        const url = new URL(window.location.origin + `/cajas/menu/options`);
        if (query) url.searchParams.set('q', query);
        await fetchOptions.execute(url.toString(), {
            q: query,
            id: selectedId,
            tipo: selectedItem?.tipo || tipo,
            codapl: selectedItem?.codapl || codapl,
        });
    };

    const attachChild = async () => {
        if (!selectedId || !selectedChildId) return;
        setAttaching(true);
        await fetchAttach.execute(`/cajas/menu/attach-child`, {
            id: selectedId,
            child_id: Number(selectedChildId),
            tipo: selectedItem?.tipo || tipo,
            codapl: selectedItem?.codapl || codapl,
        });
    };

    return (
        <AppLayout title="Menu Items" description="Lista de todos los menu en el sistema">
            <div className={cajasPageClass} key={meta.pagination?.current_page}>
                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between`}>
                        <div className="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div className="sm:col-span-2">
                                <label htmlFor="q" className={cajasFormLabelClass}>
                                    Buscar
                                </label>
                                <div className="relative mt-1">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Título, controller, action, URL..."
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') applyFilters();
                                        }}
                                    />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="tipo" className={cajasFormLabelClass}>
                                    Tipo
                                </label>
                                <select id="tipo" className={cajasFormSelectClass} value={tipo} onChange={(e) => setTipo(e.target.value)}>
                                    <option value="">Todos</option>
                                    {tipos.map((opt) => (
                                        <option key={opt.value} value={opt.value}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="codapl" className={cajasFormLabelClass}>
                                    Aplicación
                                </label>
                                <select id="codapl" className={cajasFormSelectClass} value={codapl} onChange={(e) => setCodapl(e.target.value)}>
                                    <option value="">Todas</option>
                                    <option value="CA">CA</option>
                                    <option value="ME">ME</option>
                                </select>
                            </div>
                        </div>
                        <div className="flex flex-wrap items-center gap-2">
                            <button type="button" onClick={applyFilters} className={cajasFormBtnSecondary}>
                                Filtrar
                            </button>
                            <button type="button" onClick={clearFilters} className={cajasFormBtnSecondary}>
                                Limpiar
                            </button>
                            <Link href="/cajas/menu/create" className={cajasFormBtnPrimary}>
                                <Plus className="size-4" />
                                Nuevo item Menu
                            </Link>
                        </div>
                    </div>
                </section>

                <div className="grid grid-cols-1 gap-4 xl:grid-cols-5">
                    <div className="space-y-4 xl:col-span-3">
                        <section className={cajasCardClass}>
                            <div className={`${cajasCardHeaderClass} flex items-center justify-between`}>
                                <div>
                                    <h2 className="text-sm font-semibold text-foreground">Items del menú</h2>
                                    <p className="text-xs text-muted-foreground">{meta.pagination?.total ?? data.length} registros en total</p>
                                </div>
                            </div>

                            {data.length === 0 ? (
                                <div className={`${cajasCardBodyClass} text-center`}>
                                    <ListTree className="mx-auto size-10 text-muted-foreground/60" />
                                    <h3 className="mt-3 text-sm font-medium text-foreground">No hay items de menu</h3>
                                    <p className="mt-1 text-sm text-muted-foreground">Comienza creando un nuevo item de menu.</p>
                                    <div className="mt-5">
                                        <Link href="/cajas/menu/create" className={cajasFormBtnPrimary}>
                                            <Plus className="size-4" />
                                            Nuevo Item Menu
                                        </Link>
                                    </div>
                                </div>
                            ) : (
                                <ul className="divide-y divide-border">
                                    {data.map((menu_item) => {
                                        const isSelected = selectedId === menu_item.id;

                                        return (
                                            <li key={menu_item.id}>
                                                <div
                                                    className={`flex flex-col gap-3 px-4 py-4 transition-colors sm:px-5 lg:flex-row lg:items-center lg:justify-between ${
                                                        isSelected ? 'bg-cajas-border/10' : 'hover:bg-muted/30'
                                                    }`}
                                                >
                                                    <button
                                                        type="button"
                                                        onClick={() => handleDetail(menu_item)}
                                                        className="flex min-w-0 flex-1 items-start gap-3 text-left"
                                                    >
                                                        <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-cajas-header-bg ring-1 ring-cajas-border/30">
                                                            <CajasMenuIcon icon={menu_item.icon} color={menu_item.color} className="text-sm" />
                                                        </div>
                                                        <div className="min-w-0">
                                                            <div className="flex flex-wrap items-center gap-2">
                                                                <span className="truncate text-sm font-semibold text-foreground">{menu_item.title}</span>
                                                                <span
                                                                    className={`${cajasBadgeClass} ${
                                                                        menu_item.codapl === 'CA'
                                                                            ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                            : 'bg-cajas-danger/10 text-cajas-danger ring-cajas-danger/20'
                                                                    }`}
                                                                >
                                                                    {menu_item.codapl}
                                                                </span>
                                                            </div>
                                                            <p className="mt-1 truncate text-xs text-muted-foreground">
                                                                {menu_item.controller || 'Menu Padre'} · {menu_item.action || '#'}
                                                            </p>
                                                            <div className="mt-2 flex flex-wrap gap-2">
                                                                <span
                                                                    className={`${cajasBadgeClass} ${
                                                                        menu_item.is_visible
                                                                            ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                            : 'bg-cajas-danger/10 text-cajas-danger ring-cajas-danger/20'
                                                                    }`}
                                                                >
                                                                    {menu_item.is_visible ? 'Visible' : 'Oculto'}
                                                                </span>
                                                                <span className={`${cajasBadgeClass} bg-muted text-muted-foreground ring-border`}>
                                                                    Pos: {menu_item.position}
                                                                </span>
                                                                <span className={`${cajasBadgeClass} bg-muted text-muted-foreground ring-border`}>
                                                                    Tipo: {menu_item.tipo || 'N/A'}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </button>

                                                    <div className="flex items-center gap-2">
                                                        <Link href={`/cajas/menu/${menu_item.id}/show`} className={cajasActionLinkClass}>
                                                            <Eye className="size-3.5" />
                                                            Ver
                                                        </Link>
                                                        <Link href={`/cajas/menu/${menu_item.id}/edit`} className={cajasActionMutedClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </Link>
                                                        <button
                                                            type="button"
                                                            onClick={() => handleDelete(menu_item.id, menu_item.title)}
                                                            className={cajasActionDangerClass}
                                                        >
                                                            <Trash2 className="size-3.5" />
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        );
                                    })}
                                </ul>
                            )}

                            {meta.pagination && data.length > 0 && (
                                <div className="flex flex-col gap-3 border-t border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                                    <div className="flex flex-wrap items-center justify-between gap-4 text-sm text-muted-foreground">
                                        <span>
                                            Mostrando {meta.pagination.from || 0}–{meta.pagination.to || 0} de {meta.pagination.total}
                                        </span>
                                        <label className="flex shrink-0 items-center gap-2">
                                            Por página
                                            <select
                                                id="per_page"
                                                className={`${cajasFormSelectClass} !mt-0 w-auto py-1`}
                                                value={meta.pagination.per_page}
                                                onChange={(e) =>
                                                    router.get(
                                                        '/cajas/menu',
                                                        { page: 1, per_page: Number(e.target.value), ...currentFilterParams },
                                                        { preserveScroll: true },
                                                    )
                                                }
                                            >
                                                {[5, 10, 25, 50, 100].map((n) => (
                                                    <option key={n} value={n}>
                                                        {n}
                                                    </option>
                                                ))}
                                            </select>
                                        </label>
                                    </div>
                                    <div className="flex shrink-0 items-center gap-1">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.get(
                                                    '/cajas/menu',
                                                    { page: 1, per_page: meta.pagination!.per_page, ...currentFilterParams },
                                                    { preserveState: true, preserveScroll: true },
                                                )
                                            }
                                            disabled={meta.pagination.current_page === 1}
                                            className={paginationIconButtonClass}
                                            aria-label="Primera página"
                                            title="Primera página"
                                        >
                                            <ChevronFirst className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.get(
                                                    '/cajas/menu',
                                                    {
                                                        page: Math.max(1, meta.pagination!.current_page - 1),
                                                        per_page: meta.pagination!.per_page,
                                                        ...currentFilterParams,
                                                    },
                                                    { preserveState: true, preserveScroll: true },
                                                )
                                            }
                                            disabled={meta.pagination.current_page === 1}
                                            className={paginationIconButtonClass}
                                            aria-label="Página anterior"
                                            title="Página anterior"
                                        >
                                            <ChevronLeft className="size-4" />
                                        </button>
                                        {(() => {
                                            const p = meta.pagination!;
                                            const start = Math.max(1, p.current_page - 2);
                                            const end = Math.min(p.last_page, p.current_page + 2);
                                            const pages = Array.from({ length: end - start + 1 }, (_, i) => start + i);

                                            return pages.map((num) => (
                                                <button
                                                    key={num}
                                                    type="button"
                                                    onClick={() =>
                                                        router.get(
                                                            '/cajas/menu',
                                                            { page: num, per_page: p.per_page, ...currentFilterParams },
                                                            { preserveState: true, preserveScroll: true },
                                                        )
                                                    }
                                                    className={`${paginationButtonClass} ${
                                                        num === p.current_page ? 'border-cajas-border bg-cajas-border text-cajas-text-active' : ''
                                                    }`}
                                                >
                                                    {num}
                                                </button>
                                            ));
                                        })()}
                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.get(
                                                    '/cajas/menu',
                                                    {
                                                        page: Math.min(meta.pagination!.last_page, meta.pagination!.current_page + 1),
                                                        per_page: meta.pagination!.per_page,
                                                        ...currentFilterParams,
                                                    },
                                                    { preserveState: true, preserveScroll: true },
                                                )
                                            }
                                            disabled={meta.pagination.current_page === meta.pagination.last_page}
                                            className={paginationIconButtonClass}
                                            aria-label="Página siguiente"
                                            title="Página siguiente"
                                        >
                                            <ChevronRight className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.get(
                                                    '/cajas/menu',
                                                    { page: meta.pagination!.last_page, per_page: meta.pagination!.per_page, ...currentFilterParams },
                                                    { preserveState: true, preserveScroll: true },
                                                )
                                            }
                                            disabled={meta.pagination.current_page === meta.pagination.last_page}
                                            className={paginationIconButtonClass}
                                            aria-label="Última página"
                                            title="Última página"
                                        >
                                            <ChevronLast className="size-4" />
                                        </button>
                                    </div>
                                </div>
                            )}
                        </section>
                    </div>

                    <aside className="xl:col-span-2">
                        <div className={`${cajasCardClass} sticky top-4`}>
                            <div className={`${cajasCardHeaderClass} flex items-center justify-between gap-3`}>
                                <div>
                                    <h4 className="text-sm font-semibold text-foreground">Detalle del item</h4>
                                    {selectedId ? (
                                        <p className="text-xs text-muted-foreground">
                                            Item seleccionado: <span className="font-medium text-foreground">#{selectedId}</span>
                                        </p>
                                    ) : (
                                        <p className="text-xs text-muted-foreground">Selecciona un item para ver tipos e hijos</p>
                                    )}
                                </div>
                                {selectedId && (
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs text-muted-foreground">
                                            {children.length} hijo{children.length === 1 ? '' : 's'}
                                        </span>
                                        <button type="button" onClick={openAddChild} className={cajasFormBtnSecondary}>
                                            <Plus className="size-3.5" />
                                            Agregar
                                        </button>
                                    </div>
                                )}
                            </div>

                            <div className={`${cajasCardBodyClass} max-h-[70vh] overflow-auto`}>
                                {!selectedId && (
                                    <div className="flex flex-col items-center justify-center py-10 text-center">
                                        <ListTree className="mb-3 size-10 text-muted-foreground/50" />
                                        <p className="text-sm text-muted-foreground">Haz clic en un item de la lista para explorar su jerarquía.</p>
                                    </div>
                                )}

                                {loadingChildren && (
                                    <div className="space-y-2">
                                        <div className="h-3 w-1/2 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-2/3 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-1/3 animate-pulse rounded bg-muted" />
                                    </div>
                                )}

                                {childrenError && (
                                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                                        {childrenError}
                                    </div>
                                )}

                                {!loadingChildren && !childrenError && selectedId && selectedItem && (
                                    <div className="space-y-5">
                                        <div className="rounded-lg border border-border p-3">
                                            <div className="flex items-start gap-3">
                                                <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-cajas-header-bg ring-1 ring-cajas-border/30">
                                                    <CajasMenuIcon icon={selectedItem.icon} color={selectedItem.color} className="text-sm" />
                                                </div>
                                                <div className="min-w-0 flex-1">
                                                    <div className="flex items-center justify-between gap-2">
                                                        <div className="truncate text-sm font-medium text-foreground" title={selectedItem.title}>
                                                            {selectedItem.title}
                                                        </div>
                                                        <Link href={`/cajas/menu/${selectedItem.id}/edit`} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </Link>
                                                    </div>
                                                    <p
                                                        className="mt-0.5 truncate text-xs text-muted-foreground"
                                                        title={`${selectedItem.controller || '—'} | ${selectedItem.action || '—'}`}
                                                    >
                                                        {selectedItem.controller || '—'} · {selectedItem.action || '—'}
                                                    </p>
                                                    {selectedItem.default_url && (
                                                        <p className="mt-2 break-all text-[11px] text-muted-foreground">{selectedItem.default_url}</p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div className="mb-2 flex items-center justify-between gap-2">
                                                <h5 className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                                    Tipos asociados
                                                </h5>
                                                <span className="text-xs text-muted-foreground">
                                                    {itemTipos.length} tipo{itemTipos.length === 1 ? '' : 's'}
                                                </span>
                                            </div>
                                            {itemTipos.length === 0 ? (
                                                <p className="text-sm text-muted-foreground">Este item no tiene tipos asociados.</p>
                                            ) : (
                                                <ul className="space-y-2">
                                                    {itemTipos.map((tipoItem) => (
                                                        <li
                                                            key={tipoItem.id}
                                                            className="flex items-center justify-between gap-3 rounded-lg border border-border px-3 py-2"
                                                        >
                                                            <div className="min-w-0">
                                                                <p className="truncate text-sm font-medium text-foreground">{tipoItem.detalle}</p>
                                                                <p className="text-xs text-muted-foreground">
                                                                    Código: {tipoItem.tipo}
                                                                    {tipoItem.position != null ? ` · Pos: ${tipoItem.position}` : ''}
                                                                </p>
                                                            </div>
                                                            <span
                                                                className={`${cajasBadgeClass} shrink-0 ${
                                                                    tipoItem.is_visible
                                                                        ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                        : 'bg-cajas-danger/10 text-cajas-danger ring-cajas-danger/20'
                                                                }`}
                                                            >
                                                                {tipoItem.is_visible ? 'Visible' : 'Oculto'}
                                                            </span>
                                                        </li>
                                                    ))}
                                                </ul>
                                            )}
                                        </div>

                                        <div>
                                            <div className="mb-2 flex items-center justify-between gap-2">
                                                <h5 className="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Items hijos</h5>
                                                <span className="text-xs text-muted-foreground">
                                                    {children.length} hijo{children.length === 1 ? '' : 's'}
                                                </span>
                                            </div>
                                            {children.length === 0 ? (
                                                <p className="text-sm text-muted-foreground">Este item no tiene hijos.</p>
                                            ) : (
                                                <ul className="space-y-3">
                                                    {children.map((child) => (
                                                        <li
                                                            key={child.id}
                                                            className="rounded-lg border border-border p-3 transition-colors hover:border-cajas-border/40 hover:bg-muted/20"
                                                        >
                                                            <div className="flex items-start gap-3">
                                                                <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-cajas-header-bg ring-1 ring-cajas-border/30">
                                                                    <CajasMenuIcon icon={child.icon} color={child.color} className="text-sm" />
                                                                </div>
                                                                <div className="min-w-0 flex-1">
                                                                    <div className="flex items-center justify-between gap-2">
                                                                        <div className="truncate text-sm font-medium text-foreground" title={child.title}>
                                                                            {child.title}
                                                                        </div>
                                                                        <Link href={`/cajas/menu/${child.id}/edit`} className={cajasActionLinkClass}>
                                                                            <Pencil className="size-3.5" />
                                                                            Editar
                                                                        </Link>
                                                                    </div>
                                                                    <p
                                                                        className="mt-0.5 truncate text-xs text-muted-foreground"
                                                                        title={`${child.controller || '—'} | ${child.action || '—'}`}
                                                                    >
                                                                        {child.controller || '—'} · {child.action || '—'}
                                                                    </p>
                                                                    <div className="mt-2 flex flex-wrap items-center gap-2">
                                                                        <span className={`${cajasBadgeClass} bg-muted text-muted-foreground ring-border`}>
                                                                            Tipo: {child.tipo ?? 'N/A'}
                                                                        </span>
                                                                        <span className={`${cajasBadgeClass} bg-muted text-muted-foreground ring-border`}>
                                                                            Pos: {child.position ?? '—'}
                                                                        </span>
                                                                        <span
                                                                            className={`${cajasBadgeClass} ${
                                                                                child.is_visible
                                                                                    ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                                    : 'bg-cajas-danger/10 text-cajas-danger ring-cajas-danger/20'
                                                                            }`}
                                                                        >
                                                                            {child.is_visible ? 'Visible' : 'Oculto'}
                                                                        </span>
                                                                    </div>
                                                                    {child.default_url && (
                                                                        <p className="mt-2 break-all text-[11px] text-muted-foreground">{child.default_url}</p>
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </li>
                                                    ))}
                                                </ul>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>

                {addOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div className="absolute inset-0 bg-black/40" onClick={() => setAddOpen(false)} />
                        <div className={`${cajasCardClass} relative w-full max-w-md`}>
                            <div className={`${cajasCardHeaderClass} flex items-center justify-between`}>
                                <h3 className="text-sm font-semibold text-foreground">Agregar hijo al item #{selectedId}</h3>
                                <button type="button" onClick={() => setAddOpen(false)} className="text-muted-foreground hover:text-foreground">
                                    ✕
                                </button>
                            </div>
                            <div className={`${cajasCardBodyClass} space-y-3`}>
                                <div>
                                    <label className={cajasFormLabelClass}>Buscar</label>
                                    <div className="mt-1 flex gap-2">
                                        <input
                                            type="text"
                                            className={cajasFormInputClass}
                                            placeholder="Título, controller, action"
                                            value={searchOption}
                                            onChange={(e) => setSearchOption(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key === 'Enter') loadOptions(searchOption);
                                            }}
                                        />
                                        <button type="button" onClick={() => loadOptions(searchOption)} className={cajasFormBtnSecondary}>
                                            Buscar
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label className={cajasFormLabelClass}>Seleccionar item</label>
                                    <select
                                        className={cajasFormSelectClass}
                                        value={selectedChildId}
                                        onChange={(e) => setSelectedChildId(e.target.value)}
                                    >
                                        <option value="">— Selecciona —</option>
                                        {options.map((opt) => (
                                            <option key={opt.id} value={opt.id}>
                                                {opt.title}
                                            </option>
                                        ))}
                                    </select>
                                    {optionsLoading && <p className="mt-1 text-xs text-muted-foreground">Cargando opciones…</p>}
                                    {optionsError && <p className="mt-1 text-xs text-cajas-danger">{optionsError}</p>}
                                </div>
                            </div>
                            <div className="flex justify-end gap-2 border-t border-border px-4 py-3 sm:px-5">
                                <button type="button" onClick={() => setAddOpen(false)} className={cajasFormBtnSecondary}>
                                    Cancelar
                                </button>
                                <button type="button" onClick={attachChild} disabled={!selectedChildId || attaching} className={cajasFormBtnPrimary}>
                                    {attaching ? 'Agregando…' : 'Agregar'}
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {toast && (
                    <div
                        className={`fixed right-4 bottom-4 z-50 max-w-[360px] min-w-[260px] rounded-lg px-4 py-3 text-sm shadow-lg transition-all ${
                            toast.type === 'success' ? 'bg-cajas-success text-white' : 'bg-cajas-danger text-white'
                        }`}
                    >
                        {toast.message}
                        <button type="button" className="ml-3 text-white/90 underline hover:text-white" onClick={() => setToast(null)}>
                            Cerrar
                        </button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
