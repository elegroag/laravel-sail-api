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
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Filter, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type Option = {
    value: string | number;
    label: string;
};

type Row = {
    tipopc: string;
    tipsoc: string;
    coddoc: number;
    tipopc_detalle: string | null;
    tipsoc_detalle: string | null;
    coddoc_detalle: string | null;
    obliga: string;
    auto_generado: number;
    nota: string | null;
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
    tipopcOptions: Option[];
    tipsocOptions: Option[];
    coddocOptions: Option[];
    filters: {
        q: string;
        per_page: number;
    };
};

type FormState = {
    tipopc: string;
    tipsoc: string;
    coddoc: string;
    obliga: string;
    auto_generado: string;
    nota: string;
};

const emptyForm = (): FormState => ({
    tipopc: '',
    tipsoc: '',
    coddoc: '',
    obliga: 'N',
    auto_generado: '0',
    nota: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, tipopcOptions, tipsocOptions, coddocOptions, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingKey, setEditingKey] = useState<{ tipopc: string; tipsoc: string; coddoc: number } | null>(null);
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
            setEditingKey(null);
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
        router.get('/cajas/mercurio14/index', params, {
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
        setEditingKey(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: Row) => {
        setEditingKey({ tipopc: row.tipopc, tipsoc: row.tipsoc, coddoc: row.coddoc });
        setForm({
            tipopc: row.tipopc,
            tipsoc: row.tipsoc,
            coddoc: String(row.coddoc),
            obliga: row.obliga === 'S' ? 'S' : 'N',
            auto_generado: String(row.auto_generado === 1 ? 1 : 0),
            nota: row.nota ?? '',
        });
        setDialogOpen(true);
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        const options = {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                setDialogOpen(false);
                setEditingKey(null);
                setForm(emptyForm());
            },
        };

        if (editingKey) {
            router.put(
                `/cajas/mercurio14/${encodeURIComponent(editingKey.tipopc)}/${encodeURIComponent(editingKey.tipsoc)}/${editingKey.coddoc}`,
                {
                    obliga: form.obliga,
                    auto_generado: Number(form.auto_generado),
                    nota: form.nota.trim() || null,
                },
                options,
            );
        } else {
            router.post(
                '/cajas/mercurio14',
                {
                    tipopc: form.tipopc,
                    tipsoc: form.tipsoc,
                    coddoc: Number(form.coddoc),
                    obliga: form.obliga,
                    auto_generado: Number(form.auto_generado),
                    nota: form.nota.trim() || null,
                },
                options,
            );
        }
    };

    const handleDelete = (row: Row) => {
        if (
            !confirm(
                `¿Eliminar el documento requerido ${row.tipopc_detalle || row.tipopc} / ${row.tipsoc_detalle || row.tipsoc} / ${row.coddoc_detalle || row.coddoc}?`,
            )
        ) {
            return;
        }

        router.delete(
            `/cajas/mercurio14/${encodeURIComponent(row.tipopc)}/${encodeURIComponent(row.tipsoc)}/${row.coddoc}`,
            { preserveScroll: true },
        );
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Documentos requeridos empleadores">
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
                                        placeholder="Afiliación, sociedad, documento, nota..."
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
                            <h3 className="text-sm font-semibold text-foreground">Registros</h3>
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
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Tipo servicio</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Tipo sociedad</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Documento</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Obligatorio</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Auto generado</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {items.data.map((row) => (
                                            <tr key={`${row.tipopc}-${row.tipsoc}-${row.coddoc}`} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 text-foreground">
                                                    <div className="font-medium">{row.tipopc_detalle || row.tipopc}</div>
                                                    <div className="text-xs text-muted-foreground">{row.tipopc}</div>
                                                </td>
                                                <td className="px-3 py-2.5 text-foreground">
                                                    <div className="font-medium">{row.tipsoc_detalle || row.tipsoc}</div>
                                                    <div className="text-xs text-muted-foreground">{row.tipsoc}</div>
                                                </td>
                                                <td className="px-3 py-2.5 text-foreground">
                                                    <div className="font-medium">{row.coddoc_detalle || row.coddoc}</div>
                                                    <div className="text-xs text-muted-foreground">{row.coddoc}</div>
                                                </td>
                                                <td className="px-3 py-2.5">
                                                    <span
                                                        className={`${cajasBadgeClass} ${
                                                            row.obliga === 'S'
                                                                ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                : 'bg-muted text-muted-foreground ring-border'
                                                        }`}
                                                    >
                                                        {row.obliga === 'S' ? 'Sí' : 'No'}
                                                    </span>
                                                </td>
                                                <td className="px-3 py-2.5">
                                                    <span
                                                        className={`${cajasBadgeClass} ${
                                                            row.auto_generado === 1
                                                                ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                                : 'bg-muted text-muted-foreground ring-border'
                                                        }`}
                                                    >
                                                        {row.auto_generado === 1 ? 'Sí' : 'No'}
                                                    </span>
                                                </td>
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
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-xl">
                    <DialogHeader>
                        <DialogTitle>{editingKey ? 'Editar documento requerido' : 'Nuevo documento requerido'}</DialogTitle>
                        <DialogDescription>Asocia un documento a un tipo de afiliación y sociedad de empleador.</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <label htmlFor="tipopc" className={cajasFormLabelClass}>
                                    Tipo afiliación *
                                </label>
                                <select
                                    id="tipopc"
                                    name="tipopc"
                                    required
                                    disabled={Boolean(editingKey)}
                                    className={`${cajasFormSelectClass} ${inputError('tipopc')}`}
                                    value={form.tipopc}
                                    onChange={handleChange}
                                >
                                    <option value="">Selecciona…</option>
                                    {tipopcOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.tipopc && <p className={cajasFormErrorClass}>{formErrors.tipopc}</p>}
                            </div>

                            <div className="sm:col-span-2">
                                <label htmlFor="tipsoc" className={cajasFormLabelClass}>
                                    Tipo sociedad *
                                </label>
                                <select
                                    id="tipsoc"
                                    name="tipsoc"
                                    required
                                    disabled={Boolean(editingKey)}
                                    className={`${cajasFormSelectClass} ${inputError('tipsoc')}`}
                                    value={form.tipsoc}
                                    onChange={handleChange}
                                >
                                    <option value="">Selecciona…</option>
                                    {tipsocOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.tipsoc && <p className={cajasFormErrorClass}>{formErrors.tipsoc}</p>}
                            </div>

                            <div className="sm:col-span-2">
                                <label htmlFor="coddoc" className={cajasFormLabelClass}>
                                    Documento *
                                </label>
                                <select
                                    id="coddoc"
                                    name="coddoc"
                                    required
                                    disabled={Boolean(editingKey)}
                                    className={`${cajasFormSelectClass} ${inputError('coddoc')}`}
                                    value={form.coddoc}
                                    onChange={handleChange}
                                >
                                    <option value="">Selecciona…</option>
                                    {coddocOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.coddoc && <p className={cajasFormErrorClass}>{formErrors.coddoc}</p>}
                            </div>

                            <div>
                                <label htmlFor="obliga" className={cajasFormLabelClass}>
                                    Obligatorio *
                                </label>
                                <select
                                    id="obliga"
                                    name="obliga"
                                    required
                                    className={`${cajasFormSelectClass} ${inputError('obliga')}`}
                                    value={form.obliga}
                                    onChange={handleChange}
                                >
                                    <option value="N">No</option>
                                    <option value="S">Sí</option>
                                </select>
                                {formErrors.obliga && <p className={cajasFormErrorClass}>{formErrors.obliga}</p>}
                            </div>

                            <div>
                                <label htmlFor="auto_generado" className={cajasFormLabelClass}>
                                    Auto generado *
                                </label>
                                <select
                                    id="auto_generado"
                                    name="auto_generado"
                                    required
                                    className={`${cajasFormSelectClass} ${inputError('auto_generado')}`}
                                    value={form.auto_generado}
                                    onChange={handleChange}
                                >
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                                {formErrors.auto_generado && <p className={cajasFormErrorClass}>{formErrors.auto_generado}</p>}
                            </div>

                            <div className="sm:col-span-2">
                                <label htmlFor="nota" className={cajasFormLabelClass}>
                                    Nota / observaciones
                                </label>
                                <textarea
                                    id="nota"
                                    name="nota"
                                    rows={3}
                                    maxLength={255}
                                    className={`${cajasFormTextareaClass} ${inputError('nota')}`}
                                    value={form.nota}
                                    onChange={handleChange}
                                />
                                {formErrors.nota && <p className={cajasFormErrorClass}>{formErrors.nota}</p>}
                            </div>
                        </div>

                        <DialogFooter className="gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={processing}>
                                {processing ? 'Guardando...' : editingKey ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
