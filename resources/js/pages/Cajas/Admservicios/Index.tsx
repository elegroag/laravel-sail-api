import AppLayout from '@/layouts/AppLayout';
import {
    cajasActionLinkClass,
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
import { router } from '@inertiajs/react';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Download,
    Eye,
    Filter,
    Search,
    ShoppingBag,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

const chipClass =
    'inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-cajas-border';

const chipIdleClass = `${chipClass} border-border bg-card text-foreground hover:border-cajas-border/60 hover:bg-cajas-border/5`;

const chipActiveClass = `${chipClass} border-cajas-border bg-cajas-border text-white shadow-sm`;

type Option = { value: string; label: string };

type Row = {
    id: number;
    documento: string;
    codben: string;
    codser: string;
    numero: string;
    valor: number;
    estado: string;
    estado_descripcion: string;
    ref_payco: string;
    transaction_id: string | null;
    approval_code: string | null;
    fecha_precompra: string | null;
    fecha_pago: string | null;
    motivo: string;
    ver_url: string;
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
        codser: string;
        numero: string;
        codben: string;
        ref_payco: string;
        q: string;
        per_page: number;
    };
    estadoOptions: Option[];
    estadoLabels: Record<string, string>;
    items: {
        data: Row[];
        meta: { pagination: PaginationMeta };
    };
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

function formatMoney(value: number | null | undefined): string {
    if (value == null || Number.isNaN(Number(value))) return '—';
    return `$${new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(Number(value))}`;
}

export default function Index({ filters, estadoOptions, items }: Props) {
    const pagination = items.meta.pagination;

    const [estado, setEstado] = useState(filters.estado);
    const [documento, setDocumento] = useState(filters.documento);
    const [codser, setCodser] = useState(filters.codser);
    const [numero, setNumero] = useState(filters.numero);
    const [codben, setCodben] = useState(filters.codben);
    const [refPayco, setRefPayco] = useState(filters.ref_payco);
    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [processing, setProcessing] = useState(false);

    useEffect(() => {
        setEstado(filters.estado);
        setDocumento(filters.documento);
        setCodser(filters.codser);
        setNumero(filters.numero);
        setCodben(filters.codben);
        setRefPayco(filters.ref_payco);
        setQ(filters.q);
        setPerPage(String(filters.per_page || 10));
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
        router.get('/cajas/admservicios/index', params, {
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
        codser: codser.trim(),
        numero: numero.trim(),
        codben: codben.trim(),
        ref_payco: refPayco.trim(),
        q: q.trim(),
        per_page: Number(perPage) || 10,
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
        setCodser('');
        setNumero('');
        setCodben('');
        setRefPayco('');
        setQ('');
        setPerPage('10');
        visit({
            estado: '',
            documento: '',
            codser: '',
            numero: '',
            codben: '',
            ref_payco: '',
            q: '',
            per_page: 10,
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
                codser: filters.codser,
                numero: filters.numero,
                codben: filters.codben,
                ref_payco: filters.ref_payco,
                q: filters.q,
                per_page: filters.per_page,
                page: pageNumber,
            }),
        );
    };

    const exportCsv = () => {
        const qs = new URLSearchParams({
            estado: filters.estado,
            documento: filters.documento,
            codser: filters.codser,
            numero: filters.numero,
            codben: filters.codben,
            ref_payco: filters.ref_payco,
            q: filters.q,
        });
        window.location.href = `/cajas/admservicios/reporte/csv?${qs.toString()}`;
    };

    return (
        <AppLayout title="Ventas de servicios">
            <div className={cajasPageClass}>
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                            <ShoppingBag className="size-5 text-cajas-border" />
                            Ventas de servicios
                        </h1>
                        <p className="text-sm text-muted-foreground">Consulta administrativa de precompras y pagos del portal</p>
                    </div>
                    <button type="button" className={cajasFormBtnSecondary} onClick={exportCsv} disabled={processing}>
                        <Download className="mr-1.5 size-4" />
                        Exportar CSV
                    </button>
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
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <form
                            onSubmit={applyFilters}
                            className="mb-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4"
                            autoComplete="off"
                        >
                            <div className="xl:col-span-2">
                                <label htmlFor="q" className={cajasFormLabelClass}>
                                    Búsqueda rápida
                                </label>
                                <div className="relative mt-1">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Documento, servicio, apertura, ref. ePayco…"
                                        value={q}
                                        disabled={processing}
                                        onChange={(e) => setQ(e.target.value)}
                                    />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="documento" className={cajasFormLabelClass}>
                                    Documento
                                </label>
                                <input
                                    id="documento"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={documento}
                                    disabled={processing}
                                    onChange={(e) => setDocumento(e.target.value)}
                                />
                            </div>
                            <div>
                                <label htmlFor="ref_payco" className={cajasFormLabelClass}>
                                    Referencia ePayco
                                </label>
                                <input
                                    id="ref_payco"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={refPayco}
                                    disabled={processing}
                                    onChange={(e) => setRefPayco(e.target.value)}
                                />
                            </div>
                            <div>
                                <label htmlFor="codser" className={cajasFormLabelClass}>
                                    Servicio
                                </label>
                                <input
                                    id="codser"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={codser}
                                    disabled={processing}
                                    onChange={(e) => setCodser(e.target.value)}
                                />
                            </div>
                            <div>
                                <label htmlFor="numero" className={cajasFormLabelClass}>
                                    Apertura
                                </label>
                                <input
                                    id="numero"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={numero}
                                    disabled={processing}
                                    onChange={(e) => setNumero(e.target.value)}
                                />
                            </div>
                            <div>
                                <label htmlFor="codben" className={cajasFormLabelClass}>
                                    Beneficiario
                                </label>
                                <input
                                    id="codben"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={codben}
                                    disabled={processing}
                                    onChange={(e) => setCodben(e.target.value)}
                                />
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
                                    {[5, 10, 20, 50, 100].map((n) => (
                                        <option key={n} value={n}>
                                            {n}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex items-end gap-2 md:col-span-2 xl:col-span-4">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    <Filter className="mr-1.5 size-4" />
                                    Filtrar
                                </button>
                                <button type="button" className={cajasFormBtnSecondary} onClick={clearFilters} disabled={processing}>
                                    Limpiar
                                </button>
                            </div>
                        </form>

                        <div className="overflow-x-auto rounded-lg border border-border">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th className="px-3 py-3 font-medium">#</th>
                                        <th className="px-3 py-3 font-medium">Documento</th>
                                        <th className="px-3 py-3 font-medium">Beneficiario</th>
                                        <th className="px-3 py-3 font-medium">Servicio</th>
                                        <th className="px-3 py-3 font-medium">Valor</th>
                                        <th className="px-3 py-3 font-medium">Estado</th>
                                        <th className="px-3 py-3 font-medium">Referencia ePayco</th>
                                        <th className="px-3 py-3 font-medium">Transaction ID</th>
                                        <th className="px-3 py-3 font-medium">Approval code</th>
                                        <th className="px-3 py-3 font-medium">Fecha precompra</th>
                                        <th className="px-3 py-3 font-medium">Fecha pago</th>
                                        <th className="px-3 py-3 font-medium">Motivo</th>
                                        <th className="px-3 py-3 text-center font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={13} className="px-4 py-8 text-center text-muted-foreground">
                                                No se encontraron registros con los filtros aplicados.
                                            </td>
                                        </tr>
                                    ) : (
                                        items.data.map((row) => (
                                            <tr
                                                key={row.id}
                                                className="border-b border-border/70 transition-colors hover:bg-muted/30"
                                                onDoubleClick={() => window.open(row.ver_url, '_blank', 'noopener')}
                                                title="Doble clic para ver el detalle"
                                            >
                                                <td className="px-3 py-2.5 whitespace-nowrap font-medium">{row.id}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{row.documento || '—'}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{row.codben || '—'}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">
                                                    {row.codser || '—'}
                                                    {row.numero ? ` - ${row.numero}` : ''}
                                                </td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{formatMoney(row.valor)}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">
                                                    <span className={`${cajasBadgeClass} ${badgeTone[row.estado] ?? 'bg-muted text-muted-foreground ring-border'}`}>
                                                        {row.estado_descripcion}
                                                    </span>
                                                </td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{row.ref_payco || '—'}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">
                                                    <code className="text-xs">{row.transaction_id || '—'}</code>
                                                </td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">
                                                    <code className="text-xs">{row.approval_code || '—'}</code>
                                                </td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{row.fecha_precompra || '—'}</td>
                                                <td className="px-3 py-2.5 whitespace-nowrap">{row.fecha_pago || '—'}</td>
                                                <td className="max-w-[220px] truncate px-3 py-2.5" title={row.motivo || undefined}>
                                                    {row.motivo || '—'}
                                                </td>
                                                <td className="px-3 py-2.5 text-center whitespace-nowrap">
                                                    <a
                                                        href={row.ver_url}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className={cajasActionLinkClass}
                                                        title="Ver detalle y transacciones"
                                                    >
                                                        <Eye className="size-3.5" />
                                                        Ver
                                                    </a>
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
                                    <ChevronFirst className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page <= 1 || processing}
                                    onClick={() => goToPage(pagination.current_page - 1)}
                                >
                                    <ChevronLeft className="size-4" />
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
                                    <ChevronRight className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page >= pagination.last_page || processing}
                                    onClick={() => goToPage(pagination.last_page)}
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
