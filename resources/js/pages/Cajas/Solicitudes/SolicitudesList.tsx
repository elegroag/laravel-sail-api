import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { EstadoOption, PaginationMeta, SolicitudFilters, SolicitudRow } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasActionLinkClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router } from '@inertiajs/react';
import { Bell, ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Eye, Pencil, Search } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type Props = {
    title: string;
    baseUrl: string;
    items: { data: SolicitudRow[]; meta: { pagination: PaginationMeta } };
    filters: SolicitudFilters;
    estadoOptions: EstadoOption[];
    perPageOptions: number[];
    identificacionLabel: string;
    nombreLabel: string;
    searchPlaceholder: string;
    showRecepcion?: boolean;
};

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

const ALERTA_CLASS: Record<SolicitudRow['alerta'], string> = {
    normal: 'text-sky-500',
    warning: 'text-amber-500',
    danger: 'text-red-500',
};

export default function SolicitudesList({
    title,
    baseUrl,
    items,
    filters,
    estadoOptions,
    perPageOptions,
    identificacionLabel,
    nombreLabel,
    searchPlaceholder,
    showRecepcion = true,
}: Props) {
    const pagination = items.meta.pagination;
    const [q, setQ] = useState(filters.q);
    const [navigating, setNavigating] = useState(false);

    useEffect(() => {
        setQ(filters.q);
    }, [filters.q]);

    const pageNumbers = useMemo(() => {
        const last = pagination.last_page;
        const current = pagination.current_page;
        const windowSize = 5;
        let start = Math.max(1, current - Math.floor(windowSize / 2));
        const end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const visit = (params: Record<string, string | number>) => {
        router.get(baseUrl, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => setNavigating(true),
            onFinish: () => setNavigating(false),
        });
    };

    const applySearch = () => visit({ estado: filters.estado, q: q.trim(), per_page: filters.per_page, page: 1 });

    return (
        <AppLayout title={title}>
            <LoadingOverlay show={navigating} message="Cargando solicitudes…" />
            <div className={`${cajasPageClass} ${navigating ? 'pointer-events-none select-none' : ''}`}>
                <FlashMessages />

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} space-y-4`}>
                        <div className="flex flex-wrap gap-2" role="tablist" aria-label="Estado de la solicitud">
                            {estadoOptions.map((option) => {
                                const active = option.value === filters.estado;
                                return (
                                    <button
                                        key={option.value}
                                        type="button"
                                        role="tab"
                                        aria-selected={active}
                                        onClick={() => visit({ estado: option.value, q: q.trim(), per_page: filters.per_page, page: 1 })}
                                        className={`inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm font-medium transition-colors ${
                                            active
                                                ? 'border-cajas-border bg-cajas-border/10 text-foreground'
                                                : 'border-border text-muted-foreground hover:bg-muted'
                                        }`}
                                    >
                                        {option.label}
                                        <span className="bg-muted text-foreground rounded px-1.5 text-xs tabular-nums">{option.count}</span>
                                    </button>
                                );
                            })}
                        </div>

                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div className="flex-1">
                                <label htmlFor="q" className={cajasFormLabelClass}>
                                    Buscar
                                </label>
                                <div className="relative mt-1">
                                    <Search className="text-muted-foreground pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder={searchPlaceholder}
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') applySearch();
                                        }}
                                    />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="per_page" className={cajasFormLabelClass}>
                                    Por página
                                </label>
                                <select
                                    id="per_page"
                                    className={cajasFormSelectClass}
                                    value={filters.per_page}
                                    onChange={(e) => visit({ estado: filters.estado, q: filters.q, per_page: Number(e.target.value), page: 1 })}
                                >
                                    {perPageOptions.map((n) => (
                                        <option key={n} value={n}>
                                            {n}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex gap-2">
                                <button type="button" onClick={applySearch} className={cajasFormBtnSecondary}>
                                    Buscar
                                </button>
                                {(filters.q !== '' || q !== '') && (
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setQ('');
                                            visit({ estado: filters.estado, q: '', per_page: filters.per_page, page: 1 });
                                        }}
                                        className={cajasFormBtnSecondary}
                                    >
                                        Limpiar
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <div className="border-border overflow-x-auto rounded-lg border">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Días</th>
                                        {showRecepcion && <th className="px-4 py-3 font-medium">Recepción</th>}
                                        <th className="px-4 py-3 font-medium">{identificacionLabel}</th>
                                        <th className="px-4 py-3 font-medium">{nombreLabel}</th>
                                        <th className="px-4 py-3 font-medium">Estado</th>
                                        <th className="px-4 py-3 font-medium">Fecha solicitud</th>
                                        <th className="px-4 py-3 font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={showRecepcion ? 7 : 6} className="text-muted-foreground px-4 py-8 text-center">
                                                No hay registros que mostrar.
                                            </td>
                                        </tr>
                                    ) : (
                                        items.data.map((row) => (
                                            <tr key={row.id} className="border-border/70 border-b">
                                                <td className="whitespace-nowrap px-4 py-3">
                                                    <span className="inline-flex items-center gap-1.5 tabular-nums">
                                                        <Bell className={`size-4 ${ALERTA_CLASS[row.alerta]}`} aria-hidden />
                                                        {row.dias}
                                                    </span>
                                                </td>
                                                {showRecepcion && <td className="whitespace-nowrap px-4 py-3">{row.recepcion}</td>}
                                                <td className="whitespace-nowrap px-4 py-3 font-medium">{row.identificacion}</td>
                                                <td className="px-4 py-3">{row.nombre || '—'}</td>
                                                <td className="whitespace-nowrap px-4 py-3">
                                                    <EstadoBadge estado={row.estado} label={row.estado_detalle} />
                                                </td>
                                                <td className="text-muted-foreground whitespace-nowrap px-4 py-3">{row.fecsol || '—'}</td>
                                                <td className="whitespace-nowrap px-4 py-3">
                                                    <div className="flex flex-wrap items-center gap-1.5">
                                                        <Link href={row.show_url} className={cajasActionLinkClass} prefetch>
                                                            <Eye className="size-3.5" />
                                                            Ver
                                                        </Link>
                                                        {row.edit_url && (
                                                            <Link href={row.edit_url} className={cajasActionLinkClass} prefetch>
                                                                <Pencil className="size-3.5" />
                                                                Editar
                                                            </Link>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-muted-foreground text-xs">
                                {pagination.total === 0
                                    ? 'Sin resultados'
                                    : `Mostrando ${pagination.from ?? 0}–${pagination.to ?? 0} de ${pagination.total}`}
                            </p>
                            <div className="flex flex-wrap items-center gap-1.5">
                                <button
                                    type="button"
                                    aria-label="Primera página"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => visit({ ...filters, page: 1 })}
                                >
                                    <ChevronFirst className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    aria-label="Página anterior"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => visit({ ...filters, page: pagination.current_page - 1 })}
                                >
                                    <ChevronLeft className="size-4" />
                                </button>
                                {pageNumbers.map((n) => (
                                    <button
                                        key={n}
                                        type="button"
                                        className={`${paginationButtonClass} ${n === pagination.current_page ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                        onClick={() => visit({ ...filters, page: n })}
                                    >
                                        {n}
                                    </button>
                                ))}
                                <button
                                    type="button"
                                    aria-label="Página siguiente"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => visit({ ...filters, page: pagination.current_page + 1 })}
                                >
                                    <ChevronRight className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    aria-label="Última página"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => visit({ ...filters, page: pagination.last_page })}
                                >
                                    <ChevronLast className="size-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
