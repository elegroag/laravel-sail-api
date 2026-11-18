import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Download,
    Search,
    Table2,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type TipopcItem = {
    tipopc: string;
    detalle: string;
    dias: number | string | null;
    cantidad: number;
};

type Asesor = {
    usuario: string;
    nombre: string;
    login: string;
    items: TipopcItem[];
    total: number;
    tipos: number;
};

type Solicitud = {
    id: number | string;
    documento: string;
    nombre: string;
    fecsol: string;
    dias: string;
    estado: string;
};

type Props = {
    asesores: Asesor[];
    summary: {
        total_asesores: number;
        asesores_con_carga: number;
        total_pendientes: number;
    };
};

function iniciales(nombre: string): string {
    return (
        nombre
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map((parte) => parte.charAt(0).toUpperCase())
            .join('') || 'AS'
    );
}

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function postDownload(url: string) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = '_blank';

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = csrfToken();
    form.appendChild(token);

    document.body.appendChild(form);
    form.submit();
    form.remove();
}

export default function CargaLaboral({ asesores, summary }: Props) {
    const [processing, setProcessing] = useState(false);
    const [dialogOpen, setDialogOpen] = useState(false);
    const [dialogTitle, setDialogTitle] = useState('Solicitudes pendientes');
    const [dialogSubtitle, setDialogSubtitle] = useState('');
    const [esEmpresa, setEsEmpresa] = useState(false);
    const [solicitudes, setSolicitudes] = useState<Solicitud[]>([]);
    const [dialogError, setDialogError] = useState<string | null>(null);
    const [q, setQ] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(10);

    useEffect(() => {
        setCurrentPage(1);
    }, [q, solicitudes]);

    const filteredRows = useMemo(() => {
        const term = q.trim().toLowerCase();
        if (!term) return solicitudes;
        return solicitudes.filter((row) =>
            [row.id, row.documento, row.nombre, row.estado].some((value) => String(value).toLowerCase().includes(term)),
        );
    }, [q, solicitudes]);

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

    const openDetalle = async (asesor: Asesor, item: TipopcItem) => {
        if (item.cantidad <= 0) return;

        setDialogOpen(true);
        setDialogTitle(item.detalle || 'Solicitudes pendientes');
        setDialogSubtitle(`${asesor.nombre} · pendientes`);
        setDialogError(null);
        setSolicitudes([]);
        setQ('');
        setProcessing(true);

        try {
            const response = await fetch('/cajas/consulta/solicitudes_carga_laboral', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    tipopc: item.tipopc,
                    usuario: asesor.usuario,
                    detalle: item.detalle,
                }),
            });

            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error(payload.msj || 'No fue posible consultar las solicitudes.');
            }

            setDialogTitle(payload.detalle || item.detalle || 'Solicitudes pendientes');
            setDialogSubtitle(`${asesor.nombre} · ${payload.count ?? 0} pendiente(s)`);
            setEsEmpresa(Boolean(payload.es_empresa));
            setSolicitudes(Array.isArray(payload.solicitudes) ? payload.solicitudes : []);
        } catch (error) {
            setDialogError(error instanceof Error ? error.message : 'Error al consultar.');
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Carga Laboral">
            <LoadingOverlay show={processing} message="Consultando solicitudes…" />

            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-base font-semibold text-foreground">Carga laboral por asesor</h1>
                        <p className="text-sm text-muted-foreground">
                            Solicitudes pendientes (estado P) agrupadas por tipo de operación
                        </p>
                    </div>
                    <button
                        type="button"
                        className={cajasFormBtnSecondary}
                        onClick={() => postDownload('/cajas/consulta/reporte_excel_carga_laboral')}
                    >
                        <Download className="mr-1.5 h-4 w-4" />
                        Exportar Excel
                    </button>
                </div>

                <div className="rounded-lg border border-cajas-border/30 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    Cada tarjeta corresponde a un asesor activo. Los conteos incluyen únicamente solicitudes en estado{' '}
                    <strong>pendiente</strong>. Haz clic en un tipopc con pendientes para ver el detalle.
                </div>

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div className={`${cajasCardClass} p-4`}>
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Asesores</p>
                        <p className="mt-1 text-2xl font-semibold text-foreground">{summary.total_asesores}</p>
                    </div>
                    <div className={`${cajasCardClass} p-4`}>
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Con pendientes</p>
                        <p className="mt-1 text-2xl font-semibold text-foreground">{summary.asesores_con_carga}</p>
                    </div>
                    <div className={`${cajasCardClass} p-4`}>
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">Total pendientes</p>
                        <p className="mt-1 text-2xl font-semibold text-foreground">{summary.total_pendientes}</p>
                    </div>
                </div>

                {asesores.length === 0 ? (
                    <div className={`${cajasCardClass} p-6 text-sm text-muted-foreground`}>
                        No hay asesores activos con tipopc asignados.
                    </div>
                ) : (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {asesores.map((asesor) => (
                            <div key={asesor.usuario} className={cajasCardClass}>
                                <div className={`${cajasCardHeaderClass} flex items-center justify-between gap-3`}>
                                    <div className="flex items-center gap-3">
                                        <span className="inline-flex h-10 w-10 items-center justify-center rounded-full bg-cajas-border/15 text-sm font-semibold text-cajas-border">
                                            {iniciales(asesor.nombre)}
                                        </span>
                                        <div>
                                            <h2 className="text-sm font-semibold text-foreground">{asesor.nombre}</h2>
                                            <p className="text-xs text-muted-foreground">Código: {asesor.usuario}</p>
                                        </div>
                                    </div>
                                    <div
                                        className={`rounded-md px-2.5 py-1 text-center ${
                                            asesor.total === 0 ? 'bg-muted text-muted-foreground' : 'bg-cajas-border/15 text-cajas-border'
                                        }`}
                                    >
                                        <div className="text-base font-semibold leading-none">{asesor.total}</div>
                                        <div className="text-[10px] uppercase tracking-wide">total</div>
                                    </div>
                                </div>
                                <div className={cajasCardBodyClass}>
                                    {asesor.items.length === 0 ? (
                                        <p className="text-sm text-muted-foreground">Sin tipopc asignados</p>
                                    ) : (
                                        <ul className="divide-y divide-border">
                                            {asesor.items.map((item) => {
                                                const clickable = item.cantidad > 0;
                                                return (
                                                    <li key={`${asesor.usuario}-${item.tipopc}`}>
                                                        <button
                                                            type="button"
                                                            disabled={!clickable}
                                                            onClick={() => openDetalle(asesor, item)}
                                                            className={`flex w-full items-center justify-between gap-3 py-2.5 text-left text-sm transition-colors ${
                                                                clickable
                                                                    ? 'hover:text-cajas-border focus:outline-none focus:ring-2 focus:ring-cajas-border'
                                                                    : 'cursor-default opacity-70'
                                                            }`}
                                                        >
                                                            <span className="inline-flex items-center gap-2">
                                                                {clickable && <Table2 className="h-3.5 w-3.5 text-cajas-border" />}
                                                                <span>{item.detalle}</span>
                                                            </span>
                                                            <span
                                                                className={`inline-flex min-w-8 justify-center rounded-full px-2 py-0.5 text-xs font-semibold ${
                                                                    item.cantidad === 0
                                                                        ? 'bg-muted text-muted-foreground'
                                                                        : item.cantidad >= 10
                                                                          ? 'bg-cajas-danger/15 text-cajas-danger'
                                                                          : 'bg-cajas-border/15 text-cajas-border'
                                                                }`}
                                                            >
                                                                {item.cantidad}
                                                            </span>
                                                        </button>
                                                    </li>
                                                );
                                            })}
                                        </ul>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="max-h-[90vh] overflow-hidden sm:max-w-4xl">
                    <DialogHeader>
                        <DialogTitle>{dialogTitle}</DialogTitle>
                        <DialogDescription>{dialogSubtitle}</DialogDescription>
                    </DialogHeader>

                    {dialogError ? (
                        <div className="rounded-md border border-cajas-danger/40 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                            {dialogError}
                        </div>
                    ) : (
                        <div className="space-y-3">
                            <div className="flex flex-wrap items-center justify-between gap-3">
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
                                <select
                                    className={`${cajasFormSelectClass} w-auto min-w-[4.5rem]`}
                                    value={perPage}
                                    onChange={(e) => {
                                        setPerPage(Number(e.target.value) || 10);
                                        setCurrentPage(1);
                                    }}
                                >
                                    {[5, 10, 25, 50, 100].map((n) => (
                                        <option key={n} value={n}>
                                            {n} / pág.
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="max-h-[50vh] overflow-auto rounded-lg border border-border">
                                {filteredRows.length === 0 ? (
                                    <p className="p-6 text-sm text-muted-foreground">No hay solicitudes para mostrar.</p>
                                ) : (
                                    <table className="min-w-full text-left text-sm">
                                        <thead className="sticky top-0 border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                            <tr>
                                                <th className="px-3 py-2 font-medium">ID</th>
                                                <th className="px-3 py-2 font-medium">{esEmpresa ? 'NIT' : 'Documento'}</th>
                                                <th className="px-3 py-2 font-medium">{esEmpresa ? 'Razón social' : 'Nombre'}</th>
                                                <th className="px-3 py-2 font-medium">Fecha sol.</th>
                                                <th className="px-3 py-2 font-medium">Días</th>
                                                <th className="px-3 py-2 font-medium">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody key={`carga-page-${safePage}-${perPage}`}>
                                            {pagedRows.map((row) => (
                                                <tr key={`${safePage}-${row.id}`} className="border-b border-border/70">
                                                    <td className="px-3 py-2 whitespace-nowrap">{row.id}</td>
                                                    <td className="px-3 py-2 whitespace-nowrap">{row.documento}</td>
                                                    <td className="px-3 py-2">{row.nombre}</td>
                                                    <td className="px-3 py-2 whitespace-nowrap">{row.fecsol || '—'}</td>
                                                    <td className="px-3 py-2 whitespace-nowrap">{row.dias || '—'}</td>
                                                    <td className="px-3 py-2 whitespace-nowrap">
                                                        <span className="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                                                            {row.estado}
                                                        </span>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                )}
                            </div>

                            {filteredRows.length > 0 && (
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
                    )}

                    <DialogFooter>
                        <button type="button" className={cajasFormBtnPrimary} onClick={() => setDialogOpen(false)}>
                            Cerrar
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
