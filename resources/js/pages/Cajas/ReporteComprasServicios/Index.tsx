import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Download,
    Loader2,
    Search,
    ShoppingCart,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Option = { value: string; label: string };

type Row = {
    id: number | string;
    documento: string | null;
    codben: string | null;
    codser: string | null;
    numero: string | null;
    valor: string | number | null;
    estado: string;
    estado_detalle: string;
    ref_payco: string | null;
    transaction_id: string | null;
    approval_code: string | null;
    fecha_precompra: string | null;
    fecha_pago: string | null;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    per_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type Resumen = {
    por_estado: Record<string, number>;
    valor_total: number;
    valor_pagado: number;
};

type Props = {
    filters: {
        fecini: string;
        fecfin: string;
        estado: string;
        per_page: number;
    };
    estadoOptions: Option[];
    estadoLabels: Record<string, string>;
    resumen: Resumen;
    items: {
        data: Row[];
        meta: { pagination: PaginationMeta };
    };
    queried: boolean;
    error: string | null;
};

const badgeTone: Record<string, string> = {
    PE: 'bg-cajas-warning/15 text-cajas-warning ring-cajas-warning/25',
    PA: 'bg-cajas-success/15 text-cajas-success ring-cajas-success/25',
    DE: 'bg-[#7a6b9a]/15 text-[#5c4d7a] ring-[#7a6b9a]/30',
    RE: 'bg-cajas-danger/15 text-cajas-danger ring-cajas-danger/25',
    AB: 'bg-foreground/10 text-foreground ring-foreground/20',
    VE: 'bg-cajas-warning/15 text-cajas-warning ring-cajas-warning/25',
    DV: 'bg-cajas-info/15 text-cajas-info ring-cajas-info/25',
};

const chipTone: Record<string, string> = {
    PE: 'border-cajas-warning/40 text-cajas-warning',
    PA: 'border-cajas-success/40 text-cajas-success',
    DE: 'border-[#7a6b9a]/40 text-[#5c4d7a]',
    RE: 'border-cajas-danger/40 text-cajas-danger',
    AB: 'border-foreground/30 text-foreground',
    VE: 'border-cajas-warning/40 text-cajas-warning',
    DV: 'border-cajas-info/40 text-cajas-info',
};

function formatMoney(value: number | string | null | undefined): string {
    if (value == null || value === '' || Number.isNaN(Number(value))) return '—';
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(value));
}

export default function Index({ filters, estadoOptions, estadoLabels, resumen, items, queried, error }: Props) {
    const pagination = items.meta.pagination;

    const [fecini, setFecini] = useState(filters.fecini);
    const [fecfin, setFecfin] = useState(filters.fecfin);
    const [estado, setEstado] = useState(filters.estado);
    const [perPage, setPerPage] = useState(String(filters.per_page || 25));
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    useEffect(() => {
        setFecini(filters.fecini);
        setFecfin(filters.fecfin);
        setEstado(filters.estado);
        setPerPage(String(filters.per_page || 25));
        setProcessing(false);
    }, [filters.fecini, filters.fecfin, filters.estado, filters.per_page, queried, pagination.current_page]);

    const pageNumbers = useMemo(() => {
        const last = pagination.last_page;
        const current = pagination.current_page;
        const windowSize = 5;
        let start = Math.max(1, current - Math.floor(windowSize / 2));
        const end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const visit = (params: Record<string, string | number | boolean>) => {
        setProcessing(true);
        setLocalError(null);
        router.get('/cajas/reporte-compras-servicios/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const handleConsultar = (event?: FormEvent) => {
        event?.preventDefault();
        if (!fecini || !fecfin) {
            setLocalError('Indique el rango de fechas.');
            return;
        }
        visit({
            queried: 1,
            fecini,
            fecfin,
            estado,
            per_page: Number(perPage) || 25,
            page: 1,
        });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            queried: 1,
            fecini: filters.fecini,
            fecfin: filters.fecfin,
            estado: filters.estado,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const exportExcel = () => {
        if (!fecini || !fecfin) {
            setLocalError('Indique el rango de fechas.');
            return;
        }
        if (!queried || pagination.total === 0) {
            setLocalError('Consulte primero y asegúrese de tener resultados para exportar.');
            return;
        }
        const qs = new URLSearchParams({
            fecini: filters.fecini,
            fecfin: filters.fecfin,
            estado: filters.estado,
        });
        window.location.href = `/cajas/reporte-compras-servicios/exportar?${qs.toString()}`;
    };

    const displayError = localError || error;
    const chips = Object.entries(resumen.por_estado || {}).filter(([, count]) => Number(count) > 0);

    return (
        <AppLayout title="Ventas en línea de servicios">
            <LoadingOverlay show={processing} message="Consultando ventas en línea…" />

            <div className={cajasPageClass}>
                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <ShoppingCart className="size-5 text-cajas-border" />
                        Ventas en línea de servicios
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Consulte preventas y ventas del portal ecommerce por rango de fechas y estado
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    Los datos corresponden a las <strong>preventas</strong> y <strong>ventas en línea</strong> del catálogo
                    de servicios. El rango se aplica sobre la <strong>fecha de preventa</strong>.
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros de consulta</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form
                            onSubmit={handleConsultar}
                            className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5"
                            autoComplete="off"
                        >
                            <div>
                                <label htmlFor="fecini" className={cajasFormLabelClass}>
                                    Fecha inicial
                                </label>
                                <CajasDateField id="fecini" value={fecini} disabled={processing} onChange={setFecini} />
                            </div>
                            <div>
                                <label htmlFor="fecfin" className={cajasFormLabelClass}>
                                    Fecha final
                                </label>
                                <CajasDateField id="fecfin" value={fecfin} disabled={processing} onChange={setFecfin} />
                            </div>
                            <div>
                                <label htmlFor="estado" className={cajasFormLabelClass}>
                                    Estado
                                </label>
                                <select
                                    id="estado"
                                    className={cajasFormSelectClass}
                                    value={estado}
                                    disabled={processing}
                                    onChange={(e) => setEstado(e.target.value)}
                                >
                                    <option value="">Todos</option>
                                    {estadoOptions.map((opt) => (
                                        <option key={opt.value} value={opt.value}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="per_page" className={cajasFormLabelClass}>
                                    Por página
                                </label>
                                <select
                                    id="per_page"
                                    className={cajasFormSelectClass}
                                    value={perPage}
                                    disabled={processing}
                                    onChange={(e) => setPerPage(e.target.value)}
                                >
                                    {[10, 25, 50, 100].map((n) => (
                                        <option key={n} value={n}>
                                            {n}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex items-end gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                    Consultar
                                </button>
                                <button
                                    type="button"
                                    className={cajasFormBtnSecondary}
                                    onClick={exportExcel}
                                    disabled={processing || !queried || pagination.total === 0}
                                >
                                    <Download className="mr-1.5 h-4 w-4" />
                                    Excel
                                </button>
                            </div>
                        </form>
                        {displayError && <p className={`${cajasFormErrorClass} mt-3`}>{displayError}</p>}
                    </div>
                </div>

                {queried && (
                    <>
                        <div className="flex flex-col gap-2 rounded-xl border border-border bg-card px-4 py-3 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                            <p className="text-sm text-foreground">
                                Total: <strong>{pagination.total}</strong>
                                <span className="ml-2 text-xs text-muted-foreground">
                                    ({filters.fecini} a {filters.fecfin})
                                </span>
                            </p>
                            <p className="text-sm text-foreground">
                                Valor total: <strong>{formatMoney(resumen.valor_total)}</strong>
                                <span className="mx-2 text-muted-foreground">·</span>
                                Pagado: <strong>{formatMoney(resumen.valor_pagado)}</strong>
                            </p>
                        </div>

                        {chips.length > 0 && (
                            <div className="flex flex-wrap gap-2">
                                {chips.map(([codigo, count]) => (
                                    <span
                                        key={codigo}
                                        className={`inline-flex items-center rounded-full border bg-card px-3 py-1 text-xs font-medium ${chipTone[codigo] ?? 'border-border text-foreground'}`}
                                    >
                                        {codigo} {estadoLabels[codigo] || codigo}: {count}
                                    </span>
                                ))}
                            </div>
                        )}
                    </>
                )}

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Resultados</h2>
                        <p className="text-xs text-muted-foreground">
                            {queried
                                ? `${pagination.total} registro${pagination.total === 1 ? '' : 's'} con el filtro actual`
                                : 'Seleccione fechas y pulse Consultar'}
                        </p>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {!queried ? (
                            <p className="text-sm text-muted-foreground">Aún no se ha ejecutado la consulta.</p>
                        ) : (
                            <>
                                <div className="overflow-x-auto rounded-lg border border-border">
                                    <table className="min-w-full text-left text-sm">
                                        <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                            <tr>
                                                <th className="px-3 py-2 font-medium">ID</th>
                                                <th className="px-3 py-2 font-medium">Documento</th>
                                                <th className="px-3 py-2 font-medium">Beneficiario</th>
                                                <th className="px-3 py-2 font-medium">Servicio</th>
                                                <th className="px-3 py-2 font-medium">Apertura</th>
                                                <th className="px-3 py-2 font-medium">Valor</th>
                                                <th className="px-3 py-2 font-medium">Estado</th>
                                                <th className="px-3 py-2 font-medium">Ref. ePayco</th>
                                                <th className="px-3 py-2 font-medium">Transaction ID</th>
                                                <th className="px-3 py-2 font-medium">Approval code</th>
                                                <th className="px-3 py-2 font-medium">Fecha preventa</th>
                                                <th className="px-3 py-2 font-medium">Fecha pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {items.data.length === 0 ? (
                                                <tr>
                                                    <td colSpan={12} className="px-3 py-8 text-center text-muted-foreground">
                                                        Sin resultados para los filtros indicados.
                                                    </td>
                                                </tr>
                                            ) : (
                                                items.data.map((row) => (
                                                    <tr key={row.id} className="border-b border-border/70">
                                                        <td className="px-3 py-2 whitespace-nowrap">
                                                            <a
                                                                href={`/cajas/admservicios/ver/${row.id}`}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="text-cajas-border hover:underline"
                                                            >
                                                                #{row.id}
                                                            </a>
                                                        </td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.documento || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.codben || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.codser || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.numero || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{formatMoney(row.valor)}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">
                                                            <span
                                                                className={`${cajasBadgeClass} ${badgeTone[row.estado] ?? 'bg-muted text-muted-foreground ring-border'}`}
                                                            >
                                                                {row.estado_detalle || row.estado}
                                                            </span>
                                                        </td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.ref_payco || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">
                                                            <code className="text-xs">{row.transaction_id || '—'}</code>
                                                        </td>
                                                        <td className="px-3 py-2 whitespace-nowrap">
                                                            <code className="text-xs">{row.approval_code || '—'}</code>
                                                        </td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.fecha_precompra || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.fecha_pago || '—'}</td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>

                                <div className="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p className="text-xs text-muted-foreground">
                                        {pagination.total === 0
                                            ? 'Sin resultados'
                                            : `Mostrando ${pagination.from ?? 0}–${pagination.to ?? 0} de ${pagination.total}`}
                                    </p>
                                    <div className="flex flex-wrap items-center gap-1.5">
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page <= 1 || processing}
                                            onClick={() => goToPage(1)}
                                        >
                                            <ChevronFirst className="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page <= 1 || processing}
                                            onClick={() => goToPage(pagination.current_page - 1)}
                                        >
                                            <ChevronLeft className="h-4 w-4" />
                                        </button>
                                        {pageNumbers.map((n) => (
                                            <button
                                                key={n}
                                                type="button"
                                                className={`${paginationButtonClass} ${n === pagination.current_page ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                                disabled={processing}
                                                onClick={() => goToPage(n)}
                                            >
                                                {n}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page >= pagination.last_page || processing}
                                            onClick={() => goToPage(pagination.current_page + 1)}
                                        >
                                            <ChevronRight className="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={pagination.current_page >= pagination.last_page || processing}
                                            onClick={() => goToPage(pagination.last_page)}
                                        >
                                            <ChevronLast className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
