import AppLayout from '@/layouts/AppLayout';
import { CajasMenuIcon } from '@/pages/Cajas/components/CajasMenuIcon';
import {
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasCheckboxClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, ListTree, Search, ShieldCheck } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type MenuItem = {
    id: number;
    title: string;
    controller: string;
    action: string;
    default_url: string;
    codapl: string;
    tipo: string;
    is_visible: boolean;
    position: number;
    icon?: string | null;
    color?: string | null;
};

type TipFun = {
    tipfun: string;
    detalle: string;
};

type Permission = {
    id: number;
    menu_item: number;
    tipfun: string;
    can_view: boolean;
    opciones: string | null;
    tipfun_details?: TipFun;
};

type TipoOption = {
    value: string;
    label: string;
};

type Props = {
    menu_items: {
        data: MenuItem[];
        meta: {
            total_menu_items: number;
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

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

function parseOpcionesJson(raw: string | null | undefined): Record<string, boolean> | null {
    if (!raw?.trim()) return null;

    try {
        const parsed = JSON.parse(raw) as unknown;

        if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
            return null;
        }

        const options: Record<string, boolean> = {};

        for (const [key, value] of Object.entries(parsed)) {
            if (typeof value === 'boolean') {
                options[key] = value;
            }
        }

        return Object.keys(options).length > 0 ? options : null;
    } catch {
        return null;
    }
}

function serializeOpcionesJson(options: Record<string, boolean>): string {
    return JSON.stringify(options);
}

function OpcionesField({ value, onChange }: { value: string | null; onChange: (value: string) => void }) {
    const parsed = parseOpcionesJson(value);

    if (parsed) {
        return (
            <div className="flex flex-wrap gap-x-4 gap-y-2">
                {Object.entries(parsed).map(([key, checked]) => (
                    <label key={key} className="inline-flex items-center gap-1.5 text-xs text-foreground">
                        <input
                            type="checkbox"
                            className={cajasCheckboxClass}
                            checked={checked}
                            onChange={(e) =>
                                onChange(
                                    serializeOpcionesJson({
                                        ...parsed,
                                        [key]: e.target.checked,
                                    }),
                                )
                            }
                        />
                        <span className="capitalize">{key.replace(/_/g, ' ')}</span>
                    </label>
                ))}
            </div>
        );
    }

    return (
        <input
            type="text"
            className={cajasFormInputClass}
            placeholder="Opciones adicionales"
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
        />
    );
}

export default function Index({ menu_items, tipos }: Props) {
    const { data, meta } = menu_items;

    const [selectedItem, setSelectedItem] = useState<MenuItem | null>(null);
    const [permissions, setPermissions] = useState<Permission[]>([]);
    const [tiposFuncionarios, setTiposFuncionarios] = useState<TipFun[]>([]);
    const [loadingPermissions, setLoadingPermissions] = useState(false);
    const [permissionsError, setPermissionsError] = useState<string | null>(null);
    const [saving, setSaving] = useState(false);
    const [toast, setToast] = useState<{ type: 'success' | 'error'; message: string } | null>(null);

    const searchParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [q, setQ] = useState<string>(searchParams.get('q') || '');
    const [tipo, setTipo] = useState<string>(searchParams.get('tipo') || '');
    const [codapl, setCodapl] = useState<string>(searchParams.get('codapl') || '');
    const perPage = meta.pagination?.per_page || 10;

    useEffect(() => {
        const sp = new URLSearchParams(window.location.search);
        setQ(sp.get('q') || '');
        setTipo(sp.get('tipo') || '');
        setCodapl(sp.get('codapl') || '');
    }, [window.location.search]);

    const currentFilterParams = useMemo(
        () => ({ q: q || undefined, tipo: tipo || undefined, codapl: codapl || undefined }),
        [q, tipo, codapl],
    );

    const applyFilters = () => {
        router.get('/cajas/menu-permission', { ...currentFilterParams, page: 1, per_page: perPage }, { preserveScroll: true });
    };

    const clearFilters = () => {
        setQ('');
        setTipo('');
        setCodapl('');
        router.get('/cajas/menu-permission', { per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const handleSelectItem = async (item: MenuItem) => {
        setSelectedItem(item);
        setLoadingPermissions(true);
        setPermissionsError(null);

        try {
            const res = await fetch(`/cajas/menu-permission/${item.id}/permissions`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const contentType = res.headers.get('content-type') || '';

            if (!res.ok) {
                throw new Error(`No fue posible cargar los permisos (${res.status})`);
            }

            if (!contentType.includes('application/json')) {
                throw new Error('La respuesta del servidor no es JSON válida');
            }

            const json = await res.json();
            setPermissions(json.permissions || []);
            setTiposFuncionarios(json.tipos_funcionarios || []);
        } catch (e: unknown) {
            setPermissions([]);
            setTiposFuncionarios([]);
            const message = e instanceof Error ? e.message : 'Error desconocido';
            setPermissionsError(message);
        } finally {
            setLoadingPermissions(false);
        }
    };

    const handlePermissionChange = (tipfun: string, field: 'can_view' | 'opciones', value: boolean | string) => {
        const existingPermissionIndex = permissions.findIndex((p) => p.tipfun === tipfun);
        const updatedPermissions = [...permissions];

        if (existingPermissionIndex > -1) {
            updatedPermissions[existingPermissionIndex] = {
                ...updatedPermissions[existingPermissionIndex],
                [field]: value,
            };
        } else if (selectedItem) {
            updatedPermissions.push({
                id: 0,
                menu_item: selectedItem.id,
                tipfun,
                can_view: field === 'can_view' ? Boolean(value) : false,
                opciones: field === 'opciones' ? String(value) : null,
            });
        }

        setPermissions(updatedPermissions);
    };

    const savePermissions = async () => {
        if (!selectedItem) return;

        setSaving(true);

        try {
            for (const p of permissions) {
                if (p.menu_item !== selectedItem.id) continue;

                const response = await fetch('/cajas/menu-permission/ajax', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        menu_item: selectedItem.id,
                        tipfun: p.tipfun,
                        can_view: p.can_view,
                        opciones: p.opciones,
                    }),
                });

                if (!response.ok) {
                    throw new Error('No fue posible guardar los permisos');
                }
            }

            await handleSelectItem(selectedItem);
            setToast({ type: 'success', message: 'Permisos guardados correctamente' });
        } catch (error) {
            console.error('Error saving permissions', error);
            setToast({ type: 'error', message: 'Error al guardar los permisos' });
        } finally {
            setSaving(false);
        }
    };

    return (
        <AppLayout title="Permisos de Menú" description="Administra los permisos para cada item del menú por tipo de funcionario">
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
                                        placeholder="Título, controller, action..."
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
                        </div>
                    </div>
                </section>

                <div className="grid grid-cols-1 gap-4 xl:grid-cols-5">
                    <div className="space-y-4 xl:col-span-2">
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
                                    <h3 className="mt-3 text-sm font-medium text-foreground">No hay items de menú</h3>
                                    <p className="mt-1 text-sm text-muted-foreground">Ajusta los filtros para encontrar items.</p>
                                </div>
                            ) : (
                                <ul className="divide-y divide-border">
                                    {data.map((menu_item) => {
                                        const isSelected = selectedItem?.id === menu_item.id;

                                        return (
                                            <li key={menu_item.id}>
                                                <button
                                                    type="button"
                                                    onClick={() => handleSelectItem(menu_item)}
                                                    className={`flex w-full items-start gap-3 px-4 py-4 text-left transition-colors sm:px-5 ${
                                                        isSelected ? 'bg-cajas-border/10' : 'hover:bg-muted/30'
                                                    }`}
                                                >
                                                    <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-cajas-header-bg ring-1 ring-cajas-border/30">
                                                        <CajasMenuIcon icon={menu_item.icon} color={menu_item.color} className="text-sm" />
                                                    </div>
                                                    <div className="min-w-0 flex-1">
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
                                                            {menu_item.controller || '—'} · {menu_item.action || '—'}
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
                                                        '/cajas/menu-permission',
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
                                                    '/cajas/menu-permission',
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
                                                    '/cajas/menu-permission',
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
                                                            '/cajas/menu-permission',
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
                                                    '/cajas/menu-permission',
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
                                                    '/cajas/menu-permission',
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

                    <aside className="xl:col-span-3">
                        <div className={`${cajasCardClass} sticky top-4`}>
                            <div className={`${cajasCardHeaderClass} flex items-center justify-between gap-3`}>
                                <div>
                                    <h4 className="text-sm font-semibold text-foreground">Permisos del item</h4>
                                    {selectedItem ? (
                                        <p className="text-xs text-muted-foreground">
                                            Item seleccionado: <span className="font-medium text-foreground">{selectedItem.title}</span>
                                        </p>
                                    ) : (
                                        <p className="text-xs text-muted-foreground">Selecciona un item para gestionar permisos</p>
                                    )}
                                </div>
                                {selectedItem && (
                                    <button
                                        type="button"
                                        onClick={savePermissions}
                                        disabled={saving}
                                        className={cajasFormBtnPrimary}
                                    >
                                        {saving ? 'Guardando...' : 'Guardar'}
                                    </button>
                                )}
                            </div>

                            <div className={`${cajasCardBodyClass} max-h-[70vh] overflow-auto`}>
                                {!selectedItem && (
                                    <div className="flex flex-col items-center justify-center py-10 text-center">
                                        <ShieldCheck className="mb-3 size-10 text-muted-foreground/50" />
                                        <p className="text-sm text-muted-foreground">Haz clic en un item de la lista para configurar sus permisos.</p>
                                    </div>
                                )}

                                {loadingPermissions && (
                                    <div className="space-y-2">
                                        <div className="h-3 w-1/2 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-2/3 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-1/3 animate-pulse rounded bg-muted" />
                                    </div>
                                )}

                                {permissionsError && (
                                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                                        {permissionsError}
                                    </div>
                                )}

                                {!loadingPermissions && !permissionsError && selectedItem && (
                                    <div className="overflow-hidden rounded-lg border border-border">
                                        <table className="min-w-full divide-y divide-border text-sm">
                                            <thead className="bg-muted/60">
                                                <tr>
                                                    <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Tipo funcionario</th>
                                                    <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Puede ver</th>
                                                    <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-border bg-card">
                                                {tiposFuncionarios.map((tf) => {
                                                    const permission = permissions.find((p) => p.tipfun === tf.tipfun);

                                                    return (
                                                        <tr key={tf.tipfun} className="transition-colors hover:bg-muted/20">
                                                            <td className="px-3 py-3 font-medium text-foreground">{tf.detalle}</td>
                                                            <td className="px-3 py-3">
                                                                <input
                                                                    type="checkbox"
                                                                    className={cajasCheckboxClass}
                                                                    checked={permission?.can_view || false}
                                                                    onChange={(e) => handlePermissionChange(tf.tipfun, 'can_view', e.target.checked)}
                                                                />
                                                            </td>
                                                            <td className="px-3 py-3 align-top">
                                                                <OpcionesField
                                                                    value={permission?.opciones ?? null}
                                                                    onChange={(nextValue) =>
                                                                        handlePermissionChange(tf.tipfun, 'opciones', nextValue)
                                                                    }
                                                                />
                                                            </td>
                                                        </tr>
                                                    );
                                                })}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>

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
