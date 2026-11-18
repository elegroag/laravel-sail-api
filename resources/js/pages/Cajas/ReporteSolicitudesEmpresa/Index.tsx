import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasCheckboxClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
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
    Loader2,
    Printer,
    Search,
    Building2,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Option = { value: string; label: string };

type Row = {
    tipopc: number;
    tipo_label: string;
    id: number | string | null;
    ruuid: string;
    documento_afiliado: string;
    nombre: string;
    nit: string;
    fecsol: string | null;
    estado: string;
    fecha_cierre: string | null;
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
        documento: string;
        coddoc: string;
        fecini: string;
        fecfin: string;
        estado: string;
        tipopcs: string[];
        per_page: number;
        refresh: boolean;
    };
    tiposDocumento: Option[];
    tipopcOptions: Option[];
    estadoOptions: Option[];
    empresa: { documento: string; coddoc: string; tipo: string } | null;
    fromCache: boolean;
    items: {
        data: Row[];
        meta: { pagination: PaginationMeta };
    };
    queried: boolean;
    error: string | null;
};

export default function Index({
    filters,
    tiposDocumento,
    tipopcOptions,
    estadoOptions,
    empresa,
    fromCache,
    items,
    queried,
    error,
}: Props) {
    const pagination = items.meta.pagination;

    const [documento, setDocumento] = useState(filters.documento);
    const [coddoc, setCoddoc] = useState(filters.coddoc);
    const [fecini, setFecini] = useState(filters.fecini);
    const [fecfin, setFecfin] = useState(filters.fecfin);
    const [estado, setEstado] = useState(filters.estado);
    const [tipopcs, setTipopcs] = useState<string[]>(filters.tipopcs.length ? filters.tipopcs : tipopcOptions.map((o) => o.value));
    const [perPage, setPerPage] = useState(String(filters.per_page || 25));
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    useEffect(() => {
        setDocumento(filters.documento);
        setCoddoc(filters.coddoc);
        setFecini(filters.fecini);
        setFecfin(filters.fecfin);
        setEstado(filters.estado);
        setTipopcs(filters.tipopcs.length ? filters.tipopcs : tipopcOptions.map((o) => o.value));
        setPerPage(String(filters.per_page || 25));
        setProcessing(false);
    }, [filters, queried, pagination.current_page, tipopcOptions]);

    const pageNumbers = useMemo(() => {
        const last = pagination.last_page;
        const current = pagination.current_page;
        const windowSize = 5;
        let start = Math.max(1, current - Math.floor(windowSize / 2));
        const end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const visit = (params: Record<string, string | number | boolean | string[]>) => {
        setProcessing(true);
        setLocalError(null);
        router.get('/cajas/reporte-solicitudes-empresa/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const toggleTipopc = (value: string) => {
        setTipopcs((prev) => (prev.includes(value) ? prev.filter((v) => v !== value) : [...prev, value]));
    };

    const handleConsultar = (event?: FormEvent) => {
        event?.preventDefault();
        if (!documento.trim()) {
            setLocalError('Indique el documento de la empresa.');
            return;
        }
        if (!coddoc) {
            setLocalError('Seleccione el tipo de documento.');
            return;
        }
        if (!fecini || !fecfin) {
            setLocalError('Indique el rango de fechas.');
            return;
        }
        if (tipopcs.length === 0) {
            setLocalError('Seleccione al menos un tipo de solicitud.');
            return;
        }

        visit({
            queried: 1,
            refresh: 1,
            documento: documento.trim(),
            coddoc,
            fecini,
            fecfin,
            estado,
            tipopcs,
            per_page: Number(perPage) || 25,
            page: 1,
        });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            queried: 1,
            refresh: 0,
            documento: filters.documento,
            coddoc: filters.coddoc,
            fecini: filters.fecini,
            fecfin: filters.fecfin,
            estado: filters.estado,
            tipopcs: filters.tipopcs,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const displayError = localError || error;

    return (
        <AppLayout title="Solicitudes por empresa aportante">
            <LoadingOverlay show={processing} message={filters.refresh || !queried ? 'Consultando solicitudes…' : 'Cargando página…'} />

            <div className={`${cajasPageClass} print:p-0`}>
                <div className="print:hidden">
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <Building2 className="size-5 text-cajas-border" />
                        Solicitudes por empresa aportante
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Consulte en HTML las solicitudes cargadas por una empresa aportante en un rango de fechas
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground print:hidden">
                    Use el <strong>documento</strong> y <strong>tipo de documento</strong> de la empresa aportante. Puede
                    filtrar por trabajador, cónyuge y/o beneficiario, y por estado de la solicitud.
                </div>

                <div className={`${cajasCardClass} print:hidden`}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros de consulta</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleConsultar} className="space-y-4" autoComplete="off">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div>
                                    <label htmlFor="documento" className={cajasFormLabelClass}>
                                        Documento (NIT)
                                    </label>
                                    <input
                                        id="documento"
                                        type="text"
                                        className={cajasFormInputClass}
                                        maxLength={20}
                                        value={documento}
                                        disabled={processing}
                                        onChange={(e) => setDocumento(e.target.value)}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="coddoc" className={cajasFormLabelClass}>
                                        Tipo documento
                                    </label>
                                    <CajasSearchSelect
                                        id="coddoc"
                                        value={coddoc}
                                        options={tiposDocumento}
                                        disabled={processing}
                                        placeholder="Seleccione"
                                        onChange={setCoddoc}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="estado" className={cajasFormLabelClass}>
                                        Estado de solicitud
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
                            </div>

                            <div>
                                <p className={`${cajasFormLabelClass} mb-2`}>Tipos de solicitud</p>
                                <div className="flex flex-wrap gap-4">
                                    {tipopcOptions.map((opt) => (
                                        <label key={opt.value} className="inline-flex items-center gap-2 text-sm text-foreground">
                                            <input
                                                type="checkbox"
                                                className={cajasCheckboxClass}
                                                checked={tipopcs.includes(opt.value)}
                                                disabled={processing}
                                                onChange={() => toggleTipopc(opt.value)}
                                            />
                                            {opt.label}
                                        </label>
                                    ))}
                                </div>
                            </div>

                            <div className="flex flex-wrap items-center gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                    Consultar
                                </button>
                                <button
                                    type="button"
                                    className={cajasFormBtnSecondary}
                                    disabled={processing || !queried || pagination.total === 0}
                                    onClick={() => window.print()}
                                >
                                    <Printer className="mr-1.5 h-4 w-4" />
                                    Imprimir
                                </button>
                            </div>
                        </form>
                        {displayError && <p className={`${cajasFormErrorClass} mt-3`}>{displayError}</p>}
                    </div>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Resultados</h2>
                        <p className="text-xs text-muted-foreground">
                            {!queried
                                ? 'Complete los filtros y pulse Consultar'
                                : empresa
                                  ? `Empresa ${empresa.documento} (coddoc ${empresa.coddoc}) — Total: ${pagination.total}${fromCache ? ' (desde caché)' : ''}`
                                  : `${pagination.total} registro(s)`}
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
                                                <th className="px-3 py-2 font-medium">Tipo</th>
                                                <th className="px-3 py-2 font-medium">Radicado</th>
                                                <th className="px-3 py-2 font-medium">Documento afiliado</th>
                                                <th className="px-3 py-2 font-medium">Nombre</th>
                                                <th className="px-3 py-2 font-medium">NIT laboral</th>
                                                <th className="px-3 py-2 font-medium">Fecha solicitud</th>
                                                <th className="px-3 py-2 font-medium">Estado</th>
                                                <th className="px-3 py-2 font-medium">Fecha estado/aprobación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {items.data.length === 0 ? (
                                                <tr>
                                                    <td colSpan={8} className="px-3 py-8 text-center text-muted-foreground">
                                                        Sin resultados para los filtros indicados.
                                                    </td>
                                                </tr>
                                            ) : (
                                                items.data.map((row, index) => (
                                                    <tr
                                                        key={`${row.tipopc}-${row.id ?? 'x'}-${row.ruuid || index}`}
                                                        className="border-b border-border/70"
                                                    >
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.tipo_label}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.ruuid || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.documento_afiliado || '—'}</td>
                                                        <td className="px-3 py-2">{row.nombre || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.nit || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.fecsol || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.estado || '—'}</td>
                                                        <td className="px-3 py-2 whitespace-nowrap">{row.fecha_cierre || '—'}</td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>

                                <div className="mt-4 flex flex-col gap-3 print:hidden sm:flex-row sm:items-center sm:justify-between">
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
