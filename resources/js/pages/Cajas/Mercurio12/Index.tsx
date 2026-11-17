import AppLayout from '@/layouts/AppLayout';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    cajasActionDangerClass,
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
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Filter, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type DocumentoRow = {
    coddoc: number;
    detalle: string;
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
        data: DocumentoRow[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    filters: {
        q: string;
        per_page: number;
    };
};

type FormState = {
    coddoc: string;
    detalle: string;
};

const emptyForm = (): FormState => ({
    coddoc: '',
    detalle: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingCoddoc, setEditingCoddoc] = useState<number | null>(null);
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
            setEditingCoddoc(null);
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
        let end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const visit = (params: Record<string, string | number>) => {
        router.get('/cajas/mercurio12/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const applyFilters = () => {
        visit({
            q: q.trim(),
            per_page: Number(perPage) || 10,
            page: 1,
        });
    };

    const clearFilters = () => {
        setQ('');
        setPerPage('10');
        visit({ q: '', per_page: 10, page: 1 });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            q: filters.q,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const openCreate = () => {
        setEditingCoddoc(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: DocumentoRow) => {
        setEditingCoddoc(row.coddoc);
        setForm({
            coddoc: String(row.coddoc),
            detalle: row.detalle ?? '',
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
            coddoc: Number(form.coddoc),
            detalle: form.detalle.trim(),
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                setDialogOpen(false);
                setEditingCoddoc(null);
                setForm(emptyForm());
            },
        };

        if (editingCoddoc !== null) {
            router.put(`/cajas/mercurio12/${editingCoddoc}`, payload, options);
        } else {
            router.post('/cajas/mercurio12', payload, options);
        }
    };

    const handleDelete = (row: DocumentoRow) => {
        if (!confirm(`¿Eliminar el documento "${row.coddoc}"?`)) {
            return;
        }

        router.delete(`/cajas/mercurio12/${row.coddoc}`, {
            preserveScroll: true,
        });
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Documentos">
            <div className={cajasPageClass}>
                {toast && (
                    <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>
                )}
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
                                        placeholder="Código o detalle..."
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
                            <button
                                type="button"
                                onClick={applyFilters}
                                className={`${cajasFormBtnSecondary} justify-center px-2.5`}
                                aria-label="Filtrar"
                                title="Filtrar"
                            >
                                <Filter className="size-4" />
                            </button>
                            <button type="button" onClick={clearFilters} className={cajasFormBtnSecondary}>
                                Limpiar
                            </button>
                            <button type="button" onClick={openCreate} className={cajasFormBtnPrimary}>
                                <Plus className="size-4" />
                                Nuevo
                            </button>
                        </div>
                    </div>
                </section>

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex items-center justify-between`}>
                        <div>
                            <h3 className="text-sm font-semibold text-foreground">Documentos</h3>
                            <p className="text-xs text-muted-foreground">
                                {pagination.total} registro{pagination.total === 1 ? '' : 's'} en total
                            </p>
                        </div>
                    </div>

                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        {items.data.length === 0 ? (
                            <p className="py-8 text-center text-sm text-muted-foreground">No hay registros para mostrar.</p>
                        ) : (
                            <div className="overflow-x-auto rounded-lg border border-border">
                                <table className="min-w-full divide-y divide-border text-sm">
                                    <thead className="bg-muted/40">
                                        <tr>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Documento</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Detalle</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {items.data.map((row) => (
                                            <tr key={row.coddoc} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 font-medium text-foreground">{row.coddoc}</td>
                                                <td className="px-3 py-2.5 text-foreground">{row.detalle}</td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex justify-end gap-1.5">
                                                        <button type="button" onClick={() => openEdit(row)} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </button>
                                                        <button type="button" onClick={() => handleDelete(row)} className={cajasActionDangerClass}>
                                                            <Trash2 className="size-3.5" />
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}

                        {pagination.last_page > 1 && (
                            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p className="text-xs text-muted-foreground">
                                    Mostrando {pagination.from ?? 0}–{pagination.to ?? 0} de {pagination.total}
                                </p>
                                <div className="flex flex-wrap items-center gap-1.5">
                                    <button type="button" className={paginationIconButtonClass} disabled={pagination.current_page <= 1} onClick={() => goToPage(1)}>
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
                        )}
                    </div>
                </section>
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>{editingCoddoc !== null ? 'Editar documento' : 'Nuevo documento'}</DialogTitle>
                        <DialogDescription>Código y detalle del documento requerido.</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                        <div>
                            <label htmlFor="coddoc" className={cajasFormLabelClass}>
                                Documento *
                            </label>
                            <input
                                id="coddoc"
                                name="coddoc"
                                type="number"
                                required
                                min={1}
                                disabled={editingCoddoc !== null}
                                className={`${cajasFormInputClass} ${inputError('coddoc')}`}
                                value={form.coddoc}
                                onChange={handleChange}
                            />
                            {formErrors.coddoc && <p className={cajasFormErrorClass}>{formErrors.coddoc}</p>}
                        </div>

                        <div>
                            <label htmlFor="detalle" className={cajasFormLabelClass}>
                                Detalle *
                            </label>
                            <input
                                id="detalle"
                                name="detalle"
                                required
                                maxLength={60}
                                className={`${cajasFormInputClass} ${inputError('detalle')}`}
                                value={form.detalle}
                                onChange={handleChange}
                            />
                            {formErrors.detalle && <p className={cajasFormErrorClass}>{formErrors.detalle}</p>}
                        </div>

                        <DialogFooter className="gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={processing}>
                                {processing ? 'Guardando...' : editingCoddoc !== null ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
