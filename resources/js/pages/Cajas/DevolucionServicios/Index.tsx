import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
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
    cajasFormTextareaClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import {
    Check,
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Eye,
    Loader2,
    Play,
    RotateCcw,
    Search,
    X,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

const chipClass =
    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-cajas-border';

const chipIdleClass = `${chipClass} border-border bg-card text-foreground hover:border-cajas-border/60 hover:bg-cajas-border/5`;

const chipActiveClass = `${chipClass} border-cajas-border bg-cajas-border text-white shadow-sm`;

type Option = { value: string; label: string };

type Row = {
    id: number;
    precompra_id: number | null;
    documento: string | null;
    codser: string | null;
    numero: string | null;
    valor: number | null;
    estado_precompra: string | null;
    estado_precompra_descripcion: string | null;
    estado: string;
    estado_descripcion: string;
    fecha_solicitud: string | null;
    motivo_solicitud: string | null;
    nota_afiliado: string | null;
    abierta: boolean;
    es_solicitada: boolean;
    ver_url: string | null;
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
        estado: string;
        documento: string;
        q: string;
        per_page: number;
    };
    estadoOptions: Option[];
    estadoLabels: Record<string, string>;
    resumen: Record<string, number>;
    items: {
        data: Row[];
        meta: { pagination: PaginationMeta };
    };
};

type AccionGestion = 'en-tramite' | 'aprobar' | 'rechazar';

type ModalState = {
    accion: AccionGestion;
    id: number;
    resumen: string;
} | null;

const badgeTone: Record<string, string> = {
    SOLICITADA: 'bg-cajas-warning/15 text-cajas-warning ring-cajas-warning/25',
    EN_TRAMITE: 'bg-cajas-info/15 text-cajas-info ring-cajas-info/25',
    APROBADO: 'bg-cajas-success/15 text-cajas-success ring-cajas-success/25',
    RECHAZADA: 'bg-cajas-danger/15 text-cajas-danger ring-cajas-danger/25',
};

const accionMeta: Record<AccionGestion, { title: string; confirmLabel: string; buttonClass: string }> = {
    'en-tramite': {
        title: 'Poner en trámite',
        confirmLabel: 'Confirmar',
        buttonClass: cajasFormBtnPrimary,
    },
    aprobar: {
        title: 'Aprobar devolución',
        confirmLabel: 'Aprobar',
        buttonClass: 'cajas-btn-primary bg-cajas-success hover:bg-cajas-success/90 border-cajas-success',
    },
    rechazar: {
        title: 'Rechazar devolución',
        confirmLabel: 'Rechazar',
        buttonClass: 'cajas-btn-primary bg-cajas-danger hover:bg-cajas-danger/90 border-cajas-danger',
    },
};

function formatMoney(value: number | null | undefined): string {
    if (value == null || Number.isNaN(Number(value))) return '—';
    return `$${new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(Number(value))}`;
}

export default function Index({ filters, estadoOptions, estadoLabels, resumen, items }: Props) {
    const pagination = items.meta.pagination;

    const [estado, setEstado] = useState(filters.estado);
    const [documento, setDocumento] = useState(filters.documento);
    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 15));
    const [processing, setProcessing] = useState(false);
    const [modal, setModal] = useState<ModalState>(null);
    const [nota, setNota] = useState('');
    const [accionProcessing, setAccionProcessing] = useState(false);

    useEffect(() => {
        setEstado(filters.estado);
        setDocumento(filters.documento);
        setQ(filters.q);
        setPerPage(String(filters.per_page || 15));
        setProcessing(false);
    }, [filters, pagination.current_page]);

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
        setProcessing(true);
        router.get('/cajas/devolucion-servicios/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const currentParams = (overrides: Record<string, string | number> = {}) => ({
        estado,
        documento: documento.trim(),
        q: q.trim(),
        per_page: Number(perPage) || 15,
        page: 1,
        ...overrides,
    });

    const applyFilters = (event?: FormEvent) => {
        event?.preventDefault();
        visit(currentParams());
    };

    const clearFilters = () => {
        setEstado('');
        setDocumento('');
        setQ('');
        setPerPage('15');
        visit({
            estado: '',
            documento: '',
            q: '',
            per_page: 15,
            page: 1,
        });
    };

    const selectEstado = (value: string) => {
        setEstado(value);
        visit(currentParams({ estado: value, page: 1 }));
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit(
            currentParams({
                estado: filters.estado,
                documento: filters.documento,
                q: filters.q,
                per_page: filters.per_page,
                page: pageNumber,
            }),
        );
    };

    const openModal = (accion: AccionGestion, row: Row) => {
        setNota('');
        setModal({
            accion,
            id: row.id,
            resumen: `${accionMeta[accion].title} #${row.id}${row.precompra_id ? ` — precompra #${row.precompra_id}` : ''}`,
        });
    };

    const closeModal = () => {
        if (accionProcessing) return;
        setModal(null);
        setNota('');
    };

    const confirmarGestion = () => {
        if (!modal) return;
        setAccionProcessing(true);
        router.post(
            `/cajas/devolucion-servicios/${modal.id}/${modal.accion}`,
            { nota: nota.trim() || null },
            {
                preserveScroll: true,
                onFinish: () => {
                    setAccionProcessing(false);
                    setModal(null);
                    setNota('');
                },
            },
        );
    };

    const modalConfig = modal ? accionMeta[modal.accion] : null;

    return (
        <AppLayout title="Devoluciones de servicios">
            <LoadingOverlay show={processing} message="Actualizando bandeja…" />

            <div className={cajasPageClass}>
                <FlashMessages />

                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <RotateCcw className="size-5 text-cajas-border" />
                        Devoluciones de servicios
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Bandeja de solicitudes de devolución creadas desde Mercurio sobre precompras vencidas
                    </p>
                </div>

                <section className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <p className="mb-2 text-xs font-medium text-muted-foreground">Filtrar por estado</p>
                        <div className="flex flex-wrap gap-2" role="group" aria-label="Filtro rápido por estado">
                            <button
                                type="button"
                                className={estado === '' ? chipActiveClass : chipIdleClass}
                                onClick={() => selectEstado('')}
                                disabled={processing}
                            >
                                Todos
                                <span className="inline-flex min-w-5 items-center justify-center rounded-full bg-white/90 px-1.5 py-0.5 text-[10px] font-bold text-cajas-border">
                                    {resumen.TODOS ?? 0}
                                </span>
                            </button>
                            {estadoOptions.map((opt) => (
                                <button
                                    key={opt.value}
                                    type="button"
                                    className={estado === opt.value ? chipActiveClass : chipIdleClass}
                                    onClick={() => selectEstado(opt.value)}
                                    disabled={processing}
                                >
                                    {opt.label}
                                    <span
                                        className={`inline-flex min-w-5 items-center justify-center rounded-full px-1.5 py-0.5 text-[10px] font-bold ${
                                            estado === opt.value
                                                ? 'bg-white/90 text-cajas-border'
                                                : 'bg-muted text-muted-foreground'
                                        }`}
                                    >
                                        {resumen[opt.value] ?? 0}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <form
                            onSubmit={applyFilters}
                            className="mb-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5"
                            autoComplete="off"
                        >
                            <div>
                                <label htmlFor="documento" className={cajasFormLabelClass}>
                                    Documento
                                </label>
                                <input
                                    id="documento"
                                    type="text"
                                    className={cajasFormInputClass}
                                    placeholder="Cédula titular"
                                    value={documento}
                                    disabled={processing}
                                    onChange={(e) => setDocumento(e.target.value)}
                                />
                            </div>
                            <div className="xl:col-span-2">
                                <label htmlFor="q" className={cajasFormLabelClass}>
                                    Buscar
                                </label>
                                <div className="relative mt-1">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Ref, servicio, motivo, nota…"
                                        value={q}
                                        disabled={processing}
                                        onChange={(e) => setQ(e.target.value)}
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
                                    <Search className="mr-1.5 size-4" />
                                    Buscar
                                </button>
                                <button type="button" className={cajasFormBtnSecondary} onClick={clearFilters} disabled={processing}>
                                    Limpiar
                                </button>
                            </div>
                        </form>

                        <p className="mb-3 text-sm text-muted-foreground">
                            Total: <strong className="text-foreground">{pagination.total}</strong>
                            {filters.estado ? (
                                <span className="ml-2">
                                    · Estado: <strong className="text-foreground">{estadoLabels[filters.estado] || filters.estado}</strong>
                                </span>
                            ) : null}
                        </p>

                        <div className="overflow-x-auto rounded-lg border border-border">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th className="px-3 py-2 font-medium">#</th>
                                        <th className="px-3 py-2 font-medium">Precompra</th>
                                        <th className="px-3 py-2 font-medium">Documento</th>
                                        <th className="px-3 py-2 font-medium">Servicio</th>
                                        <th className="px-3 py-2 font-medium text-right">Valor</th>
                                        <th className="px-3 py-2 font-medium">Estado precompra</th>
                                        <th className="px-3 py-2 font-medium">Estado devolución</th>
                                        <th className="px-3 py-2 font-medium">Fecha solicitud</th>
                                        <th className="px-3 py-2 font-medium">Motivo / nota afiliado</th>
                                        <th className="px-3 py-2 font-medium text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={10} className="px-3 py-8 text-center text-muted-foreground">
                                                No hay solicitudes de devolución con los filtros aplicados.
                                            </td>
                                        </tr>
                                    ) : (
                                        items.data.map((row) => (
                                            <tr key={row.id} className="border-b border-border/70">
                                                <td className="px-3 py-2 whitespace-nowrap">{row.id}</td>
                                                <td className="px-3 py-2 whitespace-nowrap">
                                                    {row.ver_url && row.precompra_id ? (
                                                        <a
                                                            href={row.ver_url}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="text-cajas-border hover:underline"
                                                        >
                                                            #{row.precompra_id}
                                                        </a>
                                                    ) : (
                                                        '—'
                                                    )}
                                                </td>
                                                <td className="px-3 py-2 whitespace-nowrap">{row.documento || '—'}</td>
                                                <td className="px-3 py-2 whitespace-nowrap">
                                                    {row.codser ? `${row.codser}${row.numero ? ` — ${row.numero}` : ''}` : '—'}
                                                </td>
                                                <td className="px-3 py-2 whitespace-nowrap text-right">{formatMoney(row.valor)}</td>
                                                <td className="px-3 py-2 whitespace-nowrap">
                                                    {row.estado_precompra_descripcion || row.estado_precompra || '—'}
                                                </td>
                                                <td className="px-3 py-2 whitespace-nowrap">
                                                    <span
                                                        className={`${cajasBadgeClass} ${badgeTone[row.estado] ?? 'bg-muted text-muted-foreground ring-border'}`}
                                                    >
                                                        {row.estado_descripcion || row.estado}
                                                    </span>
                                                </td>
                                                <td className="px-3 py-2 whitespace-nowrap">{row.fecha_solicitud || '—'}</td>
                                                <td className="px-3 py-2">
                                                    <div>{row.motivo_solicitud || '—'}</div>
                                                    {row.nota_afiliado ? (
                                                        <div className="mt-1 text-xs text-muted-foreground">{row.nota_afiliado}</div>
                                                    ) : null}
                                                </td>
                                                <td className="px-3 py-2">
                                                    <div className="flex flex-wrap items-center justify-center gap-1">
                                                        {row.ver_url ? (
                                                            <a
                                                                href={row.ver_url}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className={cajasActionLinkClass}
                                                                title="Ver detalle precompra"
                                                            >
                                                                <Eye className="size-3.5" />
                                                            </a>
                                                        ) : null}
                                                        {row.abierta ? (
                                                            <>
                                                                {row.es_solicitada ? (
                                                                    <button
                                                                        type="button"
                                                                        className={cajasActionLinkClass}
                                                                        title="Poner en trámite"
                                                                        disabled={processing || accionProcessing}
                                                                        onClick={() => openModal('en-tramite', row)}
                                                                    >
                                                                        <Play className="size-3.5" />
                                                                    </button>
                                                                ) : null}
                                                                <button
                                                                    type="button"
                                                                    className={cajasActionMutedClass}
                                                                    title="Aprobar"
                                                                    disabled={processing || accionProcessing}
                                                                    onClick={() => openModal('aprobar', row)}
                                                                >
                                                                    <Check className="size-3.5 text-cajas-success" />
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    className={cajasActionDangerClass}
                                                                    title="Rechazar"
                                                                    disabled={processing || accionProcessing}
                                                                    onClick={() => openModal('rechazar', row)}
                                                                >
                                                                    <X className="size-3.5" />
                                                                </button>
                                                            </>
                                                        ) : null}
                                                    </div>
                                                </td>
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
                    </div>
                </section>
            </div>

            <Dialog open={!!modal} onOpenChange={(next) => !next && closeModal()}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{modalConfig?.title ?? 'Gestionar devolución'}</DialogTitle>
                        <DialogDescription>{modal?.resumen}</DialogDescription>
                    </DialogHeader>
                    <div>
                        <label htmlFor="nota_gestion" className={cajasFormLabelClass}>
                            Nota de gestión (opcional)
                        </label>
                        <textarea
                            id="nota_gestion"
                            className={`${cajasFormTextareaClass} mt-1`}
                            rows={3}
                            maxLength={1000}
                            placeholder="Observación del operador…"
                            value={nota}
                            disabled={accionProcessing}
                            onChange={(e) => setNota(e.target.value)}
                        />
                    </div>
                    <DialogFooter>
                        <button type="button" className={cajasFormBtnSecondary} onClick={closeModal} disabled={accionProcessing}>
                            Cancelar
                        </button>
                        <button
                            type="button"
                            className={modalConfig?.buttonClass ?? cajasFormBtnPrimary}
                            onClick={confirmarGestion}
                            disabled={accionProcessing}
                        >
                            {accionProcessing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                            {modalConfig?.confirmLabel ?? 'Confirmar'}
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
