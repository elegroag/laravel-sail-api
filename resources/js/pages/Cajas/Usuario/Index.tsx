import AppLayout from '@/layouts/AppLayout';
import {
    cajasActionLinkClass,
    cajasBadgeClass,
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
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Eye, Filter, Pencil, Search } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type Row = {
    documento: string;
    tipo: string;
    coddoc: string;
    nombre: string;
    email: string;
    estado: string;
    estado_detalle: string;
    tipo_detalle: string;
    coddoc_detalle: string;
    whatsapp: string;
    fecreg: string | null;
    show_url: string;
    edit_url: string;
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
    items: {
        data: Row[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    filters: {
        q: string;
        tipo: string;
        estado: string;
        per_page: number;
    };
    tipoOptions: Record<string, string>;
    estadoOptions: Record<string, string>;
};

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

function estadoBadgeClass(estado: string): string {
    if (estado === 'A') return `${cajasBadgeClass} bg-emerald-100 text-emerald-800`;
    if (estado === 'I') return `${cajasBadgeClass} bg-amber-100 text-amber-800`;
    if (estado === 'B') return `${cajasBadgeClass} bg-red-100 text-red-800`;
    return `${cajasBadgeClass} bg-muted text-muted-foreground`;
}

export default function Index({ items, filters, tipoOptions, estadoOptions }: Props) {
    const pagination = items.meta.pagination;

    const [q, setQ] = useState(filters.q);
    const [tipo, setTipo] = useState(filters.tipo);
    const [estado, setEstado] = useState(filters.estado);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));

    useEffect(() => {
        setQ(filters.q);
        setTipo(filters.tipo);
        setEstado(filters.estado);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.tipo, filters.estado, filters.per_page]);

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
        router.get('/cajas/usuario/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const currentParams = () => ({
        q: q.trim(),
        tipo,
        estado,
        per_page: Number(perPage) || 10,
    });

    const applyFilters = () => {
        visit({ ...currentParams(), page: 1 });
    };

    const clearFilters = () => {
        setQ('');
        setTipo('');
        setEstado('');
        setPerPage('10');
        visit({ q: '', tipo: '', estado: '', per_page: 10, page: 1 });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            q: filters.q,
            tipo: filters.tipo,
            estado: filters.estado,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const rowKey = (row: Row) => `${row.documento}-${row.tipo}-${row.coddoc}`;

    return (
        <AppLayout title="Usuarios externos">
            <div className={cajasPageClass}>
                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between`}>
                        <div className="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div className="sm:col-span-2">
                                <label htmlFor="q" className={cajasFormLabelClass}>
                                    Buscar
                                </label>
                                <div className="relative mt-1">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Documento, nombre o email…"
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') applyFilters();
                                        }}
                                    />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="tipo" className={cajasFormLabelClass}>
                                    Tipo
                                </label>
                                <select
                                    id="tipo"
                                    className={cajasFormSelectClass}
                                    value={tipo}
                                    onChange={(e) => setTipo(e.target.value)}
                                >
                                    <option value="">Todos</option>
                                    {Object.entries(tipoOptions).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="estado" className={cajasFormLabelClass}>
                                    Estado
                                </label>
                                <select
                                    id="estado"
                                    className={cajasFormSelectClass}
                                    value={estado}
                                    onChange={(e) => setEstado(e.target.value)}
                                >
                                    <option value="">Todos</option>
                                    {Object.entries(estadoOptions).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
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
                                    onChange={(e) => setPerPage(e.target.value)}
                                >
                                    {[5, 10, 20, 50].map((n) => (
                                        <option key={n} value={n}>
                                            {n}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="flex flex-wrap items-center gap-2">
                            <button type="button" onClick={applyFilters} className={cajasFormBtnSecondary}>
                                <Filter className="mr-1.5 size-4" />
                                Filtrar
                            </button>
                            <button type="button" onClick={clearFilters} className={cajasFormBtnSecondary}>
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <div className="overflow-x-auto rounded-lg border border-border">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Documento</th>
                                        <th className="px-4 py-3 font-medium">Nombre</th>
                                        <th className="px-4 py-3 font-medium">Tipo</th>
                                        <th className="px-4 py-3 font-medium">Email</th>
                                        <th className="px-4 py-3 font-medium">Estado</th>
                                        <th className="px-4 py-3 font-medium">Registro</th>
                                        <th className="px-4 py-3 font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={7} className="px-4 py-8 text-center text-muted-foreground">
                                                No hay usuarios para mostrar.
                                            </td>
                                        </tr>
                                    ) : (
                                        items.data.map((row) => (
                                            <tr key={rowKey(row)} className="border-b border-border/70">
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <div className="font-medium text-foreground">{row.documento}</div>
                                                    <div className="text-xs text-muted-foreground">{row.coddoc_detalle || row.coddoc}</div>
                                                </td>
                                                <td className="px-4 py-3">{row.nombre || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap">{row.tipo_detalle || row.tipo}</td>
                                                <td className="px-4 py-3">{row.email || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <span className={estadoBadgeClass(row.estado)}>{row.estado_detalle || row.estado}</span>
                                                </td>
                                                <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">{row.fecreg || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <div className="flex flex-wrap items-center gap-1.5">
                                                        <Link href={row.show_url} className={cajasActionLinkClass} prefetch>
                                                            <Eye className="size-3.5" />
                                                            Ver
                                                        </Link>
                                                        <Link href={row.edit_url} className={cajasActionLinkClass} prefetch>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </Link>
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
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => goToPage(1)}
                                >
                                    <ChevronFirst className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page <= 1}
                                    onClick={() => goToPage(pagination.current_page - 1)}
                                >
                                    <ChevronLeft className="size-4" />
                                </button>
                                {pageNumbers.map((n) => (
                                    <button
                                        key={n}
                                        type="button"
                                        className={`${paginationButtonClass} ${n === pagination.current_page ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                        onClick={() => goToPage(n)}
                                    >
                                        {n}
                                    </button>
                                ))}
                                <button
                                    type="button"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page >= pagination.last_page}
                                    onClick={() => goToPage(pagination.current_page + 1)}
                                >
                                    <ChevronRight className="size-4" />
                                </button>
                                <button
                                    type="button"
                                    className={paginationIconButtonClass}
                                    disabled={pagination.current_page >= pagination.last_page}
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
