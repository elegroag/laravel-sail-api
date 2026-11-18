import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import {
    cajasActionLinkClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router, usePage } from '@inertiajs/react';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Download, Eye, FileSpreadsheet, Loader2, Search } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Option = { value: string; label: string };

type Row = {
    id: number | string;
    documento: string;
    nombre: string;
    responsable: string;
    fecha: string;
    fecsol: string | null;
    fecapr: string;
    radicado: string | null;
    dias_vencidos: number | string;
    extra: string | null;
    estado: string;
    estado_tipo: string;
};

type Props = {
    tipopcOptions: Option[];
    filters: {
        tipopc: string;
        fecini: string;
        fecfin: string;
    };
    result: {
        data: Row[];
        hasExtra: boolean;
        total: number;
        columns: string[];
    };
    queried: boolean;
};

function parseFecha(valor: string): Date | null {
    const [anio, mes, dia] = String(valor).split('-').map(Number);
    if (!anio || !mes || !dia) return null;
    const date = new Date(anio, mes - 1, dia);
    return Number.isNaN(date.getTime()) ? null : date;
}

function rangoSuperaTresMeses(fecini: string, fecfin: string): boolean {
    const inicio = parseFecha(fecini);
    const fin = parseFecha(fecfin);
    if (!inicio || !fin) return false;
    const limite = new Date(inicio);
    limite.setMonth(limite.getMonth() + 3);
    return fin > limite;
}

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function postDownload(url: string, payload: Record<string, string>) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = '_blank';

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = csrfToken();
    form.appendChild(token);

    Object.entries(payload).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    form.remove();
}

export default function Index({ tipopcOptions, filters, result, queried }: Props) {
    const page = usePage<{ errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [tipopc, setTipopc] = useState(filters.tipopc);
    const [fecini, setFecini] = useState(filters.fecini);
    const [fecfin, setFecfin] = useState(filters.fecfin);
    const [q, setQ] = useState('');
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(25);

    useEffect(() => {
        setTipopc(filters.tipopc);
        setFecini(filters.fecini);
        setFecfin(filters.fecfin);
    }, [filters.tipopc, filters.fecini, filters.fecfin]);

    const filteredRows = useMemo(() => {
        const term = q.trim().toLowerCase();
        if (!term) return result.data;
        return result.data.filter((row) =>
            [row.documento, row.nombre, row.responsable, row.radicado, row.estado, row.extra]
                .filter(Boolean)
                .some((value) => String(value).toLowerCase().includes(term)),
        );
    }, [q, result.data]);

    // Al cambiar consulta o búsqueda, volver a la primera página.
    useEffect(() => {
        setCurrentPage(1);
    }, [q, result.data, filters.tipopc, filters.fecini, filters.fecfin]);

    const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage) || 1);

    const safePage = Math.min(Math.max(currentPage, 1), totalPages);

    const pagedRows = useMemo(() => {
        const start = (safePage - 1) * perPage;
        return filteredRows.slice(start, start + perPage);
    }, [filteredRows, safePage, perPage]);

    const from = filteredRows.length === 0 ? 0 : (safePage - 1) * perPage + 1;
    const to = Math.min(safePage * perPage, filteredRows.length);

    const pageNumbers = useMemo(() => {
        const windowSize = 5;
        let start = Math.max(1, safePage - Math.floor(windowSize / 2));
        let end = Math.min(totalPages, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [safePage, totalPages]);

    const goToPage = (page: number) => {
        setCurrentPage(Math.min(Math.max(page, 1), totalPages));
    };

    const changePerPage = (value: number) => {
        setPerPage(value);
        setCurrentPage(1);
    };

    const runConsulta = (params: { tipopc: string; fecini: string; fecfin: string }) => {
        setQ('');
        setCurrentPage(1);
        router.get('/cajas/auditoria/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onCancel: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);

        if (!tipopc || !fecini || !fecfin) {
            setLocalError('Complete tipo de opción y el rango de fechas.');
            return;
        }

        const proceed = () => runConsulta({ tipopc, fecini, fecfin });

        if (rangoSuperaTresMeses(fecini, fecfin)) {
            const ok = window.confirm(
                'El rango seleccionado supera 3 meses. Por el volumen de datos, la consulta puede tardar más. ¿Desea continuar?',
            );
            if (!ok) return;
        }

        proceed();
    };

    const download = (kind: 'reporte' | 'exportar') => {
        if (!tipopc || !fecini || !fecfin) {
            setLocalError('Complete los filtros antes de generar el archivo.');
            return;
        }

        postDownload(`/cajas/auditoria/${kind}`, { tipopc, fecini, fecfin, format: 'xlsx' });
    };

    const detalleHref = (row: Row) => {
        const tipo = tipopc || filters.tipopc;
        const params = new URLSearchParams();
        if (fecini || filters.fecini) params.set('fecini', fecini || filters.fecini);
        if (fecfin || filters.fecfin) params.set('fecfin', fecfin || filters.fecfin);
        const query = params.toString();
        return `/cajas/auditoria/${encodeURIComponent(tipo)}/${row.id}${query ? `?${query}` : ''}`;
    };

    return (
        <AppLayout title="Consulta Histórica">
            <LoadingOverlay show={processing} message="Consultando auditoría…" />
            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <div>
                    <h1 className="text-base font-semibold text-foreground">Consulta Histórica</h1>
                    <p className="text-sm text-muted-foreground">Auditoría de solicitudes por tipo de opción y rango de fechas</p>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-4 md:grid-cols-4" autoComplete="off">
                            <div>
                                <label htmlFor="tipopc" className={cajasFormLabelClass}>
                                    Tipo opción
                                </label>
                                <CajasSearchSelect
                                    id="tipopc"
                                    value={tipopc}
                                    options={tipopcOptions}
                                    disabled={processing}
                                    onChange={setTipopc}
                                />
                                {formErrors.tipopc && <p className={cajasFormErrorClass}>{formErrors.tipopc}</p>}
                            </div>

                            <div>
                                <label htmlFor="fecini" className={cajasFormLabelClass}>
                                    Fecha inicial
                                </label>
                                <CajasDateField id="fecini" value={fecini} disabled={processing} onChange={setFecini} />
                                {formErrors.fecini && <p className={cajasFormErrorClass}>{formErrors.fecini}</p>}
                            </div>

                            <div>
                                <label htmlFor="fecfin" className={cajasFormLabelClass}>
                                    Fecha final
                                </label>
                                <CajasDateField id="fecfin" value={fecfin} disabled={processing} onChange={setFecfin} />
                                {formErrors.fecfin && <p className={cajasFormErrorClass}>{formErrors.fecfin}</p>}
                            </div>

                            <div className="flex items-end gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? (
                                        <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />
                                    ) : (
                                        <Search className="mr-1.5 h-4 w-4" />
                                    )}
                                    {processing ? 'Consultando…' : 'Consultar'}
                                </button>
                            </div>
                        </form>

                        {(localError || formErrors.form) && (
                            <p className={`${cajasFormErrorClass} mt-3`}>{localError || formErrors.form}</p>
                        )}

                        <div className="mt-4 flex flex-wrap gap-2">
                            <button
                                type="button"
                                className={cajasFormBtnSecondary}
                                onClick={() => download('reporte')}
                                disabled={processing}
                            >
                                <FileSpreadsheet className="mr-1.5 h-4 w-4" />
                                Reporte
                            </button>
                            <button
                                type="button"
                                className={cajasFormBtnSecondary}
                                onClick={() => download('exportar')}
                                disabled={!queried || processing}
                            >
                                <Download className="mr-1.5 h-4 w-4" />
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-wrap items-center justify-between gap-3`}>
                        <div>
                            <h2 className="text-sm font-semibold text-foreground">Resultados</h2>
                            <p className="text-xs text-muted-foreground">
                                {queried
                                    ? `${filteredRows.length} registro${filteredRows.length === 1 ? '' : 's'}${q.trim() ? ` (filtrados de ${result.total})` : ''}`
                                    : 'Ejecute una consulta para ver resultados'}
                            </p>
                        </div>
                        {queried && (
                            <div className="flex flex-wrap items-center gap-3">
                                <div className="flex items-center gap-2">
                                    <label htmlFor="per_page" className="text-xs text-muted-foreground">
                                        Por página
                                    </label>
                                    <select
                                        id="per_page"
                                        className={`${cajasFormSelectClass} w-auto min-w-[4.5rem]`}
                                        value={perPage}
                                        onChange={(e) => changePerPage(Number(e.target.value) || 25)}
                                    >
                                        {[10, 25, 50, 100].map((n) => (
                                            <option key={n} value={n}>
                                                {n}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="relative min-w-[220px] flex-1 sm:max-w-xs">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        type="search"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Buscar en resultados…"
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        <div className="overflow-x-auto -mx-4 sm:-mx-5">
                            {!queried ? (
                                <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">Seleccione filtros y pulse Consultar.</p>
                            ) : filteredRows.length === 0 ? (
                                <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">No hay datos para el criterio seleccionado.</p>
                            ) : (
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                        <tr>
                                            <th className="px-4 py-3 font-medium sm:px-5">Documento</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Nombre</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Responsable</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Fecha</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Fecsol</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Fecapr</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Radicado</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Días</th>
                                            {result.hasExtra && <th className="px-4 py-3 font-medium sm:px-5">Extra</th>}
                                            <th className="px-4 py-3 font-medium sm:px-5">Estado</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody key={`auditoria-page-${safePage}-${perPage}`}>
                                        {pagedRows.map((row, index) => (
                                            <tr key={`${safePage}-${row.id}-${row.radicado ?? index}`} className="border-b border-border/70">
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.documento}</td>
                                                <td className="px-4 py-3 sm:px-5">{row.nombre}</td>
                                                <td className="px-4 py-3 sm:px-5">{row.responsable}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.fecha}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.fecsol || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.fecapr || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.radicado || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.dias_vencidos}</td>
                                                {result.hasExtra && (
                                                    <td className="max-w-xs truncate px-4 py-3 sm:px-5">{row.extra || '—'}</td>
                                                )}
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.estado}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">
                                                    <Link href={detalleHref(row)} className={cajasActionLinkClass}>
                                                        <Eye className="h-3.5 w-3.5" />
                                                        Ver detalle
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>

                        {queried && filteredRows.length > 0 && (
                            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p className="text-xs text-muted-foreground">
                                    Mostrando {from}–{to} de {filteredRows.length}
                                </p>
                                {totalPages > 1 && (
                                    <div className="flex flex-wrap items-center gap-1.5">
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage <= 1}
                                            onClick={() => goToPage(1)}
                                        >
                                            <ChevronFirst className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage <= 1}
                                            onClick={() => goToPage(safePage - 1)}
                                        >
                                            <ChevronLeft className="size-4" />
                                        </button>
                                        {pageNumbers.map((n) => (
                                            <button
                                                key={n}
                                                type="button"
                                                className={`${paginationButtonClass} ${n === safePage ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                                onClick={() => goToPage(n)}
                                            >
                                                {n}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage >= totalPages}
                                            onClick={() => goToPage(safePage + 1)}
                                        >
                                            <ChevronRight className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage >= totalPages}
                                            onClick={() => goToPage(totalPages)}
                                        >
                                            <ChevronLast className="size-4" />
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
