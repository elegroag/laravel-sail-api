import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { PaginationMeta, SolicitudResumen } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnSecondary,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router } from '@inertiajs/react';
import { ArrowLeft, ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, RefreshCw } from 'lucide-react';
import { useMemo, useState } from 'react';

type Aporte = {
    perapo: string;
    fecrec: string;
    fecsis: string;
    cedtra: string;
    codsuc: string;
    numero: string | number;
    valapo: string | number;
    valnom: string | number;
};

type Props = {
    solicitud: SolicitudResumen;
    resultado: {
        success: boolean;
        msj: string;
        from_cache: boolean;
        aportes: Aporte[];
        pagination: PaginationMeta;
        per_page_options: number[];
    };
};

const COLUMNS: { key: keyof Aporte; label: string; numeric?: boolean }[] = [
    { key: 'perapo', label: 'Periodo aportes' },
    { key: 'fecrec', label: 'Fecha recibo' },
    { key: 'fecsis', label: 'Fecha sistema' },
    { key: 'cedtra', label: 'Cédula trabajador' },
    { key: 'codsuc', label: 'Sucursal' },
    { key: 'numero', label: 'Número' },
    { key: 'valapo', label: 'Valor aportes', numeric: true },
    { key: 'valnom', label: 'Valor nómina', numeric: true },
];

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

const formatNumber = (value: string | number) => {
    const n = Number(value);
    return Number.isFinite(n) && value !== '' ? n.toLocaleString('es-CO') : String(value ?? '');
};

export default function Aportes({ solicitud, resultado }: Props) {
    const pagination = resultado.pagination;
    const [navigating, setNavigating] = useState(false);

    const pageNumbers = useMemo(() => {
        const last = pagination.last_page;
        const current = pagination.current_page;
        const windowSize = 5;
        let start = Math.max(1, current - Math.floor(windowSize / 2));
        const end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const baseUrl = `${solicitud.urls.show.replace(/\/$/, '')}/aportes`;

    const go = (params: Record<string, string | number | boolean>) => {
        router.get(baseUrl, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => setNavigating(true),
            onFinish: () => setNavigating(false),
        });
    };

    return (
        <AppLayout title={`Aportes ${solicitud.nit}`}>
            <LoadingOverlay show={navigating} message="Cargando aportes…" />
            <div className={`${cajasPageClass} ${navigating ? 'pointer-events-none select-none' : ''}`}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">Aportes de la empresa</h1>
                        <p className="text-muted-foreground mt-1 flex flex-wrap items-center gap-2 text-sm">
                            <span>
                                {solicitud.razsoc} · NIT {solicitud.nit}
                            </span>
                            <EstadoBadge estado={solicitud.estado} label={solicitud.estado_detalle} />
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <button
                            type="button"
                            className={cajasFormBtnSecondary}
                            onClick={() => go({ page: 1, per_page: pagination.per_page, refresh: 1 })}
                        >
                            <RefreshCw className="mr-1.5 size-4" />
                            Actualizar
                        </button>
                        <Link href={solicitud.urls.show} className={cajasFormBtnSecondary}>
                            <ArrowLeft className="mr-1.5 size-4" />
                            Volver al detalle
                        </Link>
                    </div>
                </div>

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-wrap items-center justify-between gap-3`}>
                        <div>
                            <h2 className="text-foreground text-sm font-semibold">
                                Aportes registrados en SISU ({pagination.total})
                            </h2>
                            {resultado.from_cache && (
                                <p className="text-muted-foreground mt-1 text-xs">Mostrando datos en caché. Use Actualizar para consultar SISU de nuevo.</p>
                            )}
                        </div>
                        <div className="w-28">
                            <label htmlFor="per_page" className={cajasFormLabelClass}>
                                Por página
                            </label>
                            <select
                                id="per_page"
                                className={cajasFormSelectClass}
                                value={pagination.per_page}
                                onChange={(e) => go({ page: 1, per_page: Number(e.target.value) })}
                            >
                                {resultado.per_page_options.map((n) => (
                                    <option key={n} value={n}>
                                        {n}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {!resultado.success && resultado.msj && (
                            <p className="mb-3 rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">{resultado.msj}</p>
                        )}
                        {pagination.total === 0 ? (
                            <p className="text-muted-foreground text-sm">No hay aportes aplicados por parte de la empresa.</p>
                        ) : (
                            <>
                                <div className="border-border overflow-x-auto rounded-lg border">
                                    <table className="min-w-full text-left text-sm">
                                        <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                                            <tr>
                                                {COLUMNS.map((c) => (
                                                    <th key={c.key} className={`px-4 py-3 font-medium ${c.numeric ? 'text-right' : ''}`}>
                                                        {c.label}
                                                    </th>
                                                ))}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {resultado.aportes.map((row, index) => (
                                                <tr key={`${row.numero}-${row.cedtra}-${row.perapo}-${index}`} className="border-border/70 border-b last:border-b-0">
                                                    {COLUMNS.map((c) => (
                                                        <td
                                                            key={c.key}
                                                            className={`whitespace-nowrap px-4 py-2.5 ${c.numeric ? 'text-right tabular-nums' : ''}`}
                                                        >
                                                            {c.numeric ? formatNumber(row[c.key]) : String(row[c.key] ?? '')}
                                                        </td>
                                                    ))}
                                                </tr>
                                            ))}
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
                                            onClick={() => go({ page: 1, per_page: pagination.per_page })}
                                        >
                                            <ChevronFirst className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            aria-label="Página anterior"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page <= 1}
                                            onClick={() => go({ page: pagination.current_page - 1, per_page: pagination.per_page })}
                                        >
                                            <ChevronLeft className="size-4" />
                                        </button>
                                        {pageNumbers.map((n) => (
                                            <button
                                                key={n}
                                                type="button"
                                                className={`${paginationButtonClass} ${n === pagination.current_page ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                                onClick={() => go({ page: n, per_page: pagination.per_page })}
                                            >
                                                {n}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            aria-label="Página siguiente"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page >= pagination.last_page}
                                            onClick={() => go({ page: pagination.current_page + 1, per_page: pagination.per_page })}
                                        >
                                            <ChevronRight className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            aria-label="Última página"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page >= pagination.last_page}
                                            onClick={() => go({ page: pagination.last_page, per_page: pagination.per_page })}
                                        >
                                            <ChevronLast className="size-4" />
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
