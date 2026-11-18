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
    Scale,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Option = { value: string; label: string };

type Fila = {
    estado: string;
    estado_descripcion: string;
    estado_precompra?: string | null;
    estado_precompra_desc?: string | null;
    venta_estado?: string | null;
    precompra_id: number | string | null;
    documento: string | null;
    codben?: string | null;
    codser: string | number | null;
    numero: string | number | null;
    ref_payco: string | null;
    valor_precompra: number | null;
    fecha_pago: string | null;
    venta_marca: string | null;
    venta_documento: string | null;
    venta_fecha: string | null;
    venta_valpago: number | null;
    venta_valor_forma_pago: number | null;
    puede_registrar?: boolean;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    per_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

type Props = {
    filters: {
        fecha_inicio: string;
        fecha_fin: string;
        estado: string;
        per_page: number;
    };
    estadoOptions: Option[];
    estadoLabels: Record<string, string>;
    resumen: Record<string, number>;
    items: {
        data: Fila[];
        meta: { pagination: PaginationMeta };
    };
    queried: boolean;
    error: string | null;
};

const badgeTone: Record<string, string> = {
    CONCILIADA: 'bg-cajas-success/15 text-cajas-success ring-cajas-success/25',
    PAGADA_SIN_VENTA: 'bg-cajas-danger/15 text-cajas-danger ring-cajas-danger/25',
    VALOR_DIFERENTE: 'bg-cajas-warning/15 text-cajas-warning ring-cajas-warning/25',
    VENTA_SIN_PRECOMPRA_PA: 'bg-muted text-muted-foreground ring-border',
    ACEPTADA_EPAYCO_NO_PA: 'bg-cajas-border/15 text-cajas-border ring-cajas-border/25',
    SIN_TX_EPAYCO: 'bg-cajas-info/15 text-cajas-info ring-cajas-info/25',
    VENTA_NO_APROBADA: 'bg-foreground/10 text-foreground ring-foreground/20',
};

const resumenTone: Record<string, string> = {
    CONCILIADA: 'border-cajas-success/40 text-cajas-success',
    PAGADA_SIN_VENTA: 'border-cajas-danger/40 text-cajas-danger',
    VALOR_DIFERENTE: 'border-cajas-warning/40 text-cajas-warning',
    VENTA_SIN_PRECOMPRA_PA: 'border-border text-foreground',
    ACEPTADA_EPAYCO_NO_PA: 'border-cajas-border/40 text-cajas-border',
    SIN_TX_EPAYCO: 'border-cajas-info/40 text-cajas-info',
    VENTA_NO_APROBADA: 'border-foreground/30 text-foreground',
};

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function formatMoney(value: number | null | undefined): string {
    if (value == null || Number.isNaN(Number(value))) return '—';
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

export default function Index({ filters, estadoOptions, estadoLabels, resumen, items, queried, error }: Props) {
    const pagination = items.meta.pagination;

    const [fechaInicio, setFechaInicio] = useState(filters.fecha_inicio);
    const [fechaFin, setFechaFin] = useState(filters.fecha_fin);
    const [estado, setEstado] = useState(filters.estado);
    const [perPage, setPerPage] = useState(String(filters.per_page || 15));
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [registeringId, setRegisteringId] = useState<string | number | null>(null);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        setFechaInicio(filters.fecha_inicio);
        setFechaFin(filters.fecha_fin);
        setEstado(filters.estado);
        setPerPage(String(filters.per_page || 15));
        setProcessing(false);
    }, [filters.fecha_inicio, filters.fecha_fin, filters.estado, filters.per_page, queried, pagination.current_page]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

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
        router.get('/cajas/conciliacion-servicios/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const handleConciliar = (event?: FormEvent) => {
        event?.preventDefault();
        if (!fechaInicio || !fechaFin) {
            setLocalError('Indique fecha inicio y fecha fin.');
            return;
        }
        visit({
            queried: 1,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            estado,
            per_page: Number(perPage) || 15,
            page: 1,
        });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            queried: 1,
            fecha_inicio: filters.fecha_inicio,
            fecha_fin: filters.fecha_fin,
            estado: filters.estado,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const exportCsv = () => {
        if (!fechaInicio || !fechaFin) {
            setLocalError('Indique fecha inicio y fecha fin.');
            return;
        }
        const qs = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            estado,
        });
        window.location.href = `/cajas/conciliacion-servicios/reporte/csv?${qs.toString()}`;
    };

    const registrarSubsidio = async (id: string | number) => {
        setRegisteringId(id);
        setLocalError(null);
        try {
            const response = await fetch(`/cajas/admservicios/registrar-subsidio/${id}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
            });
            const payload = (await response.json().catch(() => null)) as {
                success?: boolean;
                msj?: string;
                message?: string;
                registrada?: boolean;
            } | null;

            const ok = !!(payload?.success);
            const msg = payload?.msj || payload?.message || (ok ? 'Registrado' : 'No se pudo registrar');
            if (!ok) {
                setLocalError(msg);
                return;
            }
            setToast(msg);
            if (payload?.registrada) {
                visit({
                    queried: 1,
                    fecha_inicio: filters.fecha_inicio,
                    fecha_fin: filters.fecha_fin,
                    estado: filters.estado,
                    per_page: filters.per_page,
                    page: pagination.current_page,
                });
            }
        } catch (err) {
            setLocalError(err instanceof Error ? err.message : 'Error al registrar en Subsidio.');
        } finally {
            setRegisteringId(null);
        }
    };

    const displayError = localError || error;

    return (
        <AppLayout title="Conciliación servicios">
            <LoadingOverlay show={processing} message="Conciliando…" />

            <div className={cajasPageClass}>
                {toast && <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}

                <div>
                    <h1 className="text-base font-semibold text-foreground">Conciliación servicios vs Subsidio</h1>
                    <p className="text-sm text-muted-foreground">Cruce de precompras PA con ventas por referencia ePayco</p>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form
                            onSubmit={handleConciliar}
                            className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5"
                            autoComplete="off"
                        >
                            <div>
                                <label htmlFor="fecha_inicio" className={cajasFormLabelClass}>
                                    Desde
                                </label>
                                <CajasDateField
                                    id="fecha_inicio"
                                    value={fechaInicio}
                                    disabled={processing}
                                    onChange={setFechaInicio}
                                />
                            </div>
                            <div>
                                <label htmlFor="fecha_fin" className={cajasFormLabelClass}>
                                    Hasta
                                </label>
                                <CajasDateField id="fecha_fin" value={fechaFin} disabled={processing} onChange={setFechaFin} />
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
                                    {[10, 15, 25, 50, 100].map((n) => (
                                        <option key={n} value={n}>
                                            {n}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex items-end gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Scale className="mr-1.5 h-4 w-4" />}
                                    Conciliar
                                </button>
                                <button type="button" className={cajasFormBtnSecondary} onClick={exportCsv} disabled={processing}>
                                    <Download className="mr-1.5 h-4 w-4" />
                                    CSV
                                </button>
                            </div>
                        </form>
                        {displayError && <p className={`${cajasFormErrorClass} mt-3`}>{displayError}</p>}
                    </div>
                </div>

                {queried && (
                    <div className="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                        {Object.entries(estadoLabels).map(([codigo, label]) => (
                            <div key={codigo} className={`rounded-xl border bg-card px-4 py-3 shadow-sm ${resumenTone[codigo] ?? 'border-border'}`}>
                                <p className="text-xs text-muted-foreground">{label}</p>
                                <p className="text-xl font-semibold">{resumen[codigo] ?? 0}</p>
                            </div>
                        ))}
                    </div>
                )}

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Resultados</h2>
                        <p className="text-xs text-muted-foreground">
                            {queried
                                ? `${pagination.total} fila${pagination.total === 1 ? '' : 's'} con el filtro actual`
                                : 'Seleccione fechas y pulse Conciliar'}
                        </p>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {!queried ? (
                            <p className="text-sm text-muted-foreground">Aún no se ha ejecutado la conciliación.</p>
                        ) : (
                            <>
                                <div className="overflow-x-auto rounded-lg border border-border">
                                    <table className="min-w-full text-left text-sm">
                                        <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                            <tr>
                                                <th className="px-3 py-2 font-medium">Estado</th>
                                                <th className="px-3 py-2 font-medium">Precompra</th>
                                                <th className="px-3 py-2 font-medium">Documento</th>
                                                <th className="px-3 py-2 font-medium">Servicio</th>
                                                <th className="px-3 py-2 font-medium">Ref ePayco</th>
                                                <th className="px-3 py-2 font-medium">Valor precompra</th>
                                                <th className="px-3 py-2 font-medium">Fecha pago</th>
                                                <th className="px-3 py-2 font-medium">Venta Subsidio</th>
                                                <th className="px-3 py-2 font-medium">Valor venta</th>
                                                <th className="px-3 py-2 font-medium">Fecha venta</th>
                                                <th className="px-3 py-2 font-medium text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {items.data.length === 0 ? (
                                                <tr>
                                                    <td colSpan={11} className="px-3 py-8 text-center text-muted-foreground">
                                                        Sin diferencias ni coincidencias en el rango indicado.
                                                    </td>
                                                </tr>
                                            ) : (
                                                items.data.map((fila, index) => {
                                                    const ventaLabel =
                                                        fila.venta_marca && fila.venta_documento
                                                            ? `${fila.venta_marca}-${fila.venta_documento}`
                                                            : '—';
                                                    const valorVenta =
                                                        fila.venta_valor_forma_pago != null
                                                            ? fila.venta_valor_forma_pago
                                                            : fila.venta_valpago;

                                                    return (
                                                        <tr
                                                            key={`${fila.estado}-${fila.precompra_id ?? 'x'}-${fila.ref_payco ?? index}`}
                                                            className="border-b border-border/70 align-top"
                                                        >
                                                            <td className="px-3 py-2">
                                                                <span className={`${cajasBadgeClass} ${badgeTone[fila.estado] ?? 'bg-muted text-muted-foreground ring-border'}`}>
                                                                    {fila.estado_descripcion}
                                                                </span>
                                                                {fila.estado_precompra &&
                                                                    ['VENTA_SIN_PRECOMPRA_PA', 'ACEPTADA_EPAYCO_NO_PA'].includes(fila.estado) && (
                                                                        <p className="mt-1 text-xs text-muted-foreground">
                                                                            Precompra: {fila.estado_precompra_desc || fila.estado_precompra}
                                                                        </p>
                                                                    )}
                                                                {fila.estado === 'VENTA_NO_APROBADA' && fila.venta_estado && (
                                                                    <p className="mt-1 text-xs text-muted-foreground">Venta: {fila.venta_estado}</p>
                                                                )}
                                                            </td>
                                                            <td className="px-3 py-2 whitespace-nowrap">
                                                                {fila.precompra_id ? (
                                                                    <a
                                                                        href={`/cajas/admservicios/ver/${fila.precompra_id}`}
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        className="text-cajas-border hover:underline"
                                                                    >
                                                                        #{fila.precompra_id}
                                                                    </a>
                                                                ) : (
                                                                    '—'
                                                                )}
                                                            </td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{fila.documento || '—'}</td>
                                                            <td className="px-3 py-2 whitespace-nowrap">
                                                                {fila.codser != null && fila.codser !== ''
                                                                    ? `${fila.codser} / ${fila.numero ?? '—'}`
                                                                    : '—'}
                                                            </td>
                                                            <td className="px-3 py-2">
                                                                <code className="text-xs">{fila.ref_payco || '—'}</code>
                                                            </td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{formatMoney(fila.valor_precompra)}</td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{fila.fecha_pago || '—'}</td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{ventaLabel}</td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{formatMoney(valorVenta)}</td>
                                                            <td className="px-3 py-2 whitespace-nowrap">{fila.venta_fecha || '—'}</td>
                                                            <td className="px-3 py-2 text-center">
                                                                {fila.puede_registrar && fila.precompra_id ? (
                                                                    <button
                                                                        type="button"
                                                                        className="inline-flex items-center rounded-md border border-cajas-danger/40 px-2 py-1 text-xs font-medium text-cajas-danger hover:bg-cajas-danger/10 disabled:opacity-50"
                                                                        disabled={registeringId === fila.precompra_id || processing}
                                                                        onClick={() => void registrarSubsidio(fila.precompra_id as string | number)}
                                                                    >
                                                                        {registeringId === fila.precompra_id ? 'Registrando…' : 'Registrar en Subsidio'}
                                                                    </button>
                                                                ) : (
                                                                    '—'
                                                                )}
                                                            </td>
                                                        </tr>
                                                    );
                                                })
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
