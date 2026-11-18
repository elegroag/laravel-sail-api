import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
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
    cajasInputErrorClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Filter, Pencil, Plus, Search } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type Row = {
    codapl: string;
    webser: string | null;
    path: string | null;
    urlonl: string | null;
    puncom: string | number | null;
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
    canCreate: boolean;
    filters: {
        q: string;
        per_page: number;
    };
};

type FormState = {
    codapl: string;
    webser: string;
    path: string;
    urlonl: string;
    puncom: string;
};

const emptyForm = (): FormState => ({
    codapl: '',
    webser: '',
    path: '',
    urlonl: '',
    puncom: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, canCreate, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingCodapl, setEditingCodapl] = useState<string | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm());
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        setQ(filters.q);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.per_page]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setEditingCodapl(null);
            setForm(emptyForm());
        }
    }, [page.props.flash?.success]);

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

    const visit = (params: Record<string, string | number>) => {
        router.get('/cajas/mercurio50/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const applyFilters = () => {
        visit({ q: q.trim(), per_page: Number(perPage) || 10, page: 1 });
    };

    const clearFilters = () => {
        setQ('');
        setPerPage('10');
        visit({ q: '', per_page: 10, page: 1 });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({ q: filters.q, per_page: filters.per_page, page: pageNumber });
    };

    const openCreate = () => {
        setEditingCodapl(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: Row) => {
        setEditingCodapl(row.codapl);
        setForm({
            codapl: row.codapl,
            webser: row.webser ?? '',
            path: row.path ?? '',
            urlonl: row.urlonl ?? '',
            puncom: row.puncom != null ? String(row.puncom) : '',
        });
        setDialogOpen(true);
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        const payload = {
            codapl: form.codapl.trim(),
            webser: form.webser.trim() || null,
            path: form.path.trim() || null,
            urlonl: form.urlonl.trim() || null,
            puncom: form.puncom.trim() || null,
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                setDialogOpen(false);
                setEditingCodapl(null);
                setForm(emptyForm());
            },
        };

        if (editingCodapl) {
            router.put(`/cajas/mercurio50/${encodeURIComponent(editingCodapl)}`, payload, options);
        } else {
            router.post('/cajas/mercurio50', payload, options);
        }
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Configuración básica">
            <div className={cajasPageClass}>
                {toast && <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}
                {formErrors.form && !dialogOpen && (
                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                        {formErrors.form}
                    </div>
                )}

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between`}>
                        <div className="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
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
                                        placeholder="Aplicativo, webservice, path, url…"
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') applyFilters();
                                        }}
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
                            {canCreate && (
                                <button type="button" onClick={openCreate} className={cajasFormBtnPrimary}>
                                    <Plus className="mr-1.5 size-4" />
                                    Nuevo
                                </button>
                            )}
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <div className="overflow-x-auto rounded-lg border border-border">
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Código aplicativo</th>
                                        <th className="px-4 py-3 font-medium">Url webservice</th>
                                        <th className="px-4 py-3 font-medium">Path</th>
                                        <th className="px-4 py-3 font-medium">Url online</th>
                                        <th className="px-4 py-3 font-medium">Puntos por compartir</th>
                                        <th className="px-4 py-3 font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                                No hay registros para mostrar.
                                            </td>
                                        </tr>
                                    ) : (
                                        items.data.map((row) => (
                                            <tr key={row.codapl} className="border-b border-border/70">
                                                <td className="px-4 py-3 whitespace-nowrap font-medium text-foreground">{row.codapl}</td>
                                                <td className="px-4 py-3">{row.webser || '—'}</td>
                                                <td className="px-4 py-3">{row.path || '—'}</td>
                                                <td className="px-4 py-3">{row.urlonl || '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap">{row.puncom ?? '—'}</td>
                                                <td className="px-4 py-3 whitespace-nowrap">
                                                    <button type="button" className={cajasActionLinkClass} onClick={() => openEdit(row)}>
                                                        <Pencil className="size-3.5" />
                                                        Editar
                                                    </button>
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

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>{editingCodapl ? 'Editar configuración básica' : 'Nueva configuración básica'}</DialogTitle>
                        <DialogDescription>Parámetros del aplicativo móvil / integración.</DialogDescription>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                        <div>
                            <label htmlFor="codapl" className={cajasFormLabelClass}>
                                Aplicativo
                            </label>
                            <input
                                id="codapl"
                                name="codapl"
                                className={`${cajasFormInputClass} ${inputError('codapl')}`}
                                value={form.codapl}
                                onChange={handleChange}
                                disabled={!!editingCodapl || processing}
                                required={!editingCodapl}
                            />
                            {formErrors.codapl && <p className={cajasFormErrorClass}>{formErrors.codapl}</p>}
                        </div>
                        <div>
                            <label htmlFor="webser" className={cajasFormLabelClass}>
                                WebService
                            </label>
                            <input
                                id="webser"
                                name="webser"
                                className={`${cajasFormInputClass} ${inputError('webser')}`}
                                value={form.webser}
                                onChange={handleChange}
                                disabled={processing}
                            />
                            {formErrors.webser && <p className={cajasFormErrorClass}>{formErrors.webser}</p>}
                        </div>
                        <div>
                            <label htmlFor="path" className={cajasFormLabelClass}>
                                Path
                            </label>
                            <input
                                id="path"
                                name="path"
                                className={`${cajasFormInputClass} ${inputError('path')}`}
                                value={form.path}
                                onChange={handleChange}
                                disabled={processing}
                            />
                            {formErrors.path && <p className={cajasFormErrorClass}>{formErrors.path}</p>}
                        </div>
                        <div>
                            <label htmlFor="urlonl" className={cajasFormLabelClass}>
                                Url online
                            </label>
                            <input
                                id="urlonl"
                                name="urlonl"
                                className={`${cajasFormInputClass} ${inputError('urlonl')}`}
                                value={form.urlonl}
                                onChange={handleChange}
                                disabled={processing}
                            />
                            {formErrors.urlonl && <p className={cajasFormErrorClass}>{formErrors.urlonl}</p>}
                        </div>
                        <div>
                            <label htmlFor="puncom" className={cajasFormLabelClass}>
                                Puntos por compartir
                            </label>
                            <input
                                id="puncom"
                                name="puncom"
                                type="number"
                                className={`${cajasFormInputClass} ${inputError('puncom')}`}
                                value={form.puncom}
                                onChange={handleChange}
                                disabled={processing}
                            />
                            {formErrors.puncom && <p className={cajasFormErrorClass}>{formErrors.puncom}</p>}
                        </div>
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}
                        <DialogFooter>
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                {processing ? 'Guardando…' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
