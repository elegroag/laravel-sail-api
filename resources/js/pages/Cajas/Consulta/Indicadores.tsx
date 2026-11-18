import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Download,
    Loader2,
    Search,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Row = {
    usuario: string;
    nombre: string;
    tipopc: string;
    detalle: string;
    dias: number;
    estado_aprobado: number;
    estado_rechazo: number;
    estado_pendiente: number;
    estado_devuelto: number;
    total: number;
    total_vencido: number;
};

type Props = {
    filters: {
        fecini: string;
        fecfin: string;
    };
    rows: Row[];
    queried: boolean;
};

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

export default function Indicadores({ filters, rows: initialRows, queried: initialQueried }: Props) {
    const [fecini, setFecini] = useState(filters.fecini);
    const [fecfin, setFecfin] = useState(filters.fecfin);
    const [rows, setRows] = useState<Row[]>(initialRows);
    const [queried, setQueried] = useState(initialQueried);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [q, setQ] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(25);

    useEffect(() => {
        setCurrentPage(1);
    }, [q, rows]);

    const filteredRows = useMemo(() => {
        const term = q.trim().toLowerCase();
        if (!term) return rows;
        return rows.filter((row) =>
            [row.nombre, row.usuario, row.detalle, row.tipopc].some((value) => String(value).toLowerCase().includes(term)),
        );
    }, [q, rows]);

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

    const handleConsulta = async (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);

        if (!fecini || !fecfin) {
            setLocalError('Complete el rango de fechas.');
            return;
        }

        setProcessing(true);
        try {
            const response = await fetch('/cajas/consulta/consulta_indicadores', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ fecini, fecfin }),
            });

            const payload = await response.json();
            if (!response.ok || !payload.success) {
                const message =
                    payload.message ||
                    payload.msj ||
                    (payload.errors ? Object.values(payload.errors).flat().join(' ') : null) ||
                    'No fue posible consultar indicadores.';
                throw new Error(String(message));
            }

            setRows(Array.isArray(payload.rows) ? payload.rows : []);
            setQueried(true);
            setQ('');
        } catch (error) {
            setLocalError(error instanceof Error ? error.message : 'Error al consultar.');
        } finally {
            setProcessing(false);
        }
    };

    const handleExcel = () => {
        if (!fecini || !fecfin) {
            setLocalError('Complete el rango de fechas antes de exportar.');
            return;
        }
        postDownload('/cajas/consulta/reporte_excel_indicadores', { fecini, fecfin });
    };

    return (
        <AppLayout title="Indicadores">
            <LoadingOverlay show={processing} message="Consultando indicadores…" />

            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <div>
                    <h1 className="text-base font-semibold text-foreground">Consulta de indicadores</h1>
                    <p className="text-sm text-muted-foreground">Resumen por asesor y tipo de operación según rango de fechas</p>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleConsulta} className="grid grid-cols-1 gap-4 md:grid-cols-4" autoComplete="off">
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
                            <div className="flex items-end gap-2 md:col-span-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                    Consultar
                                </button>
                                <button type="button" className={cajasFormBtnSecondary} onClick={handleExcel} disabled={processing}>
                                    <Download className="mr-1.5 h-4 w-4" />
                                    Excel
                                </button>
                            </div>
                        </form>
                        {localError && <p className={`${cajasFormErrorClass} mt-3`}>{localError}</p>}
                    </div>
                </div>

                <div className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-wrap items-center justify-between gap-3`}>
                        <div>
                            <h2 className="text-sm font-semibold text-foreground">Resultados</h2>
                            <p className="text-xs text-muted-foreground">
                                {queried
                                    ? `${filteredRows.length} registro${filteredRows.length === 1 ? '' : 's'}`
                                    : 'Ejecute una consulta para ver resultados'}
                            </p>
                        </div>
                        {queried && (
                            <div className="relative min-w-[220px] flex-1 sm:max-w-xs">
                                <Search className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <input
                                    type="search"
                                    className={`${cajasFormInputClass} pl-9`}
                                    placeholder="Buscar…"
                                    value={q}
                                    onChange={(e) => setQ(e.target.value)}
                                />
                            </div>
                        )}
                    </div>
                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        <div className="overflow-x-auto -mx-4 sm:-mx-5">
                            {!queried ? (
                                <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">Seleccione fechas y pulse Consultar.</p>
                            ) : filteredRows.length === 0 ? (
                                <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">No hay datos para el criterio seleccionado.</p>
                            ) : (
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                        <tr>
                                            <th className="px-4 py-3 font-medium sm:px-5">Nombre</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Usuario</th>
                                            <th className="px-4 py-3 font-medium sm:px-5">Acc/Estado</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Aprobado</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Rechazado</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Pendiente</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Devuelto</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Total</th>
                                            <th className="px-4 py-3 text-center font-medium sm:px-5">Vencidos</th>
                                        </tr>
                                    </thead>
                                    <tbody key={`indicadores-page-${safePage}-${perPage}`}>
                                        {pagedRows.map((row) => (
                                            <tr key={`${safePage}-${row.usuario}-${row.tipopc}`} className="border-b border-border/70">
                                                <td className="px-4 py-3 sm:px-5">{row.nombre}</td>
                                                <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.usuario}</td>
                                                <td className="px-4 py-3 sm:px-5">{row.detalle}</td>
                                                <td className="px-4 py-3 text-center sm:px-5">{row.estado_aprobado}</td>
                                                <td className="px-4 py-3 text-center sm:px-5">{row.estado_rechazo}</td>
                                                <td className="px-4 py-3 text-center sm:px-5">{row.estado_pendiente}</td>
                                                <td className="px-4 py-3 text-center sm:px-5">{row.estado_devuelto}</td>
                                                <td className="px-4 py-3 text-center font-medium sm:px-5">{row.total}</td>
                                                <td className="px-4 py-3 text-center sm:px-5">{row.total_vencido}</td>
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
                                        <button type="button" className={paginationIconButtonClass} disabled={safePage <= 1} onClick={() => setCurrentPage(1)}>
                                            <ChevronFirst className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage <= 1}
                                            onClick={() => setCurrentPage(safePage - 1)}
                                        >
                                            <ChevronLeft className="size-4" />
                                        </button>
                                        {pageNumbers.map((n) => (
                                            <button
                                                key={n}
                                                type="button"
                                                className={`${paginationButtonClass} ${n === safePage ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                                onClick={() => setCurrentPage(n)}
                                            >
                                                {n}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage >= totalPages}
                                            onClick={() => setCurrentPage(safePage + 1)}
                                        >
                                            <ChevronRight className="size-4" />
                                        </button>
                                        <button
                                            type="button"
                                            className={paginationIconButtonClass}
                                            disabled={safePage >= totalPages}
                                            onClick={() => setCurrentPage(totalPages)}
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
