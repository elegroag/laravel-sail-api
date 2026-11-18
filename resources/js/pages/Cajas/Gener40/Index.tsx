import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
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
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, FileSpreadsheet, Filter, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type SelectOption = { value: string; label: string };

type Row = {
    codigo: string;
    detalle: string;
    controller: string | null;
    action: string | null;
    orden: number | null;
    asignaciones: number;
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
        per_page: number;
    };
    controllers: SelectOption[];
    actions: SelectOption[];
};

type FormState = {
    codigo: string;
    detalle: string;
    controller: string;
    action: string;
    orden: string;
};

const emptyForm = (): FormState => ({
    codigo: '',
    detalle: '',
    controller: '',
    action: '',
    orden: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

function withCurrentOption(options: SelectOption[], value: string): SelectOption[] {
    if (!value || options.some((opt) => opt.value === value)) {
        return options;
    }

    return [{ value, label: value }, ...options];
}

export default function Index({ items, filters, controllers, actions }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingCodigo, setEditingCodigo] = useState<string | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm());
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<Row | null>(null);
    const [deleting, setDeleting] = useState(false);

    useEffect(() => {
        setQ(filters.q);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.per_page]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setEditingCodigo(null);
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

    const controllerOptions = useMemo(() => withCurrentOption(controllers, form.controller), [controllers, form.controller]);
    const actionOptions = useMemo(() => withCurrentOption(actions, form.action), [actions, form.action]);

    const visit = (params: Record<string, string | number>) => {
        router.get('/cajas/gener40/index', params, {
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
        setEditingCodigo(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: Row) => {
        setEditingCodigo(row.codigo);
        setForm({
            codigo: row.codigo,
            detalle: row.detalle ?? '',
            controller: row.controller ?? '',
            action: row.action ?? '',
            orden: row.orden !== null && row.orden !== undefined ? String(row.orden) : '',
        });
        setDialogOpen(true);
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setForm((prev) => ({
            ...prev,
            [name]: name === 'codigo' ? value.replace(/\s+/g, '') : value,
        }));
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        const payload = {
            detalle: form.detalle.trim(),
            controller: form.controller.trim(),
            action: form.action.trim(),
            orden: form.orden.trim() === '' ? null : Number(form.orden),
            q: filters.q,
            per_page: filters.per_page,
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                setDialogOpen(false);
                setEditingCodigo(null);
                setForm(emptyForm());
            },
        };

        if (editingCodigo) {
            router.put(`/cajas/gener40/${encodeURIComponent(editingCodigo)}`, payload, options);
        } else {
            router.post(
                '/cajas/gener40',
                {
                    ...payload,
                    codigo: form.codigo.trim(),
                },
                options,
            );
        }
    };

    const openDelete = (row: Row) => {
        setDeleteTarget(row);
    };

    const cancelDelete = () => {
        if (deleting) return;
        setDeleteTarget(null);
    };

    const confirmDelete = () => {
        if (!deleteTarget) return;

        setDeleting(true);
        router.delete(`/cajas/gener40/${encodeURIComponent(deleteTarget.codigo)}`, {
            preserveScroll: true,
            onFinish: () => {
                setDeleting(false);
                setDeleteTarget(null);
            },
        });
    };

    const deleteDescription = deleteTarget
        ? deleteTarget.asignaciones > 0
            ? `Se eliminará la acción "${deleteTarget.codigo}" (${deleteTarget.detalle || 'sin detalle'}). También se revocarán ${deleteTarget.asignaciones} asignación(es) de usuarios en Gener42. Esta acción no se puede deshacer.`
            : `¿Eliminar la acción de módulo "${deleteTarget.codigo}" (${deleteTarget.detalle || 'sin detalle'})? Esta acción no se puede deshacer.`
        : '';

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Acciones de módulos">
            <div className={cajasPageClass}>
                {toast && <div className="bg-cajas-success rounded-md px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}
                {formErrors.form && !dialogOpen && (
                    <div className="border-cajas-danger/30 bg-cajas-danger/10 text-cajas-danger rounded-md border px-3 py-2 text-sm">
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
                                    <Search className="text-muted-foreground pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2" />
                                    <input
                                        id="q"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="Código, detalle, controller o action..."
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
                                <select id="per_page" className={cajasFormSelectClass} value={perPage} onChange={(e) => setPerPage(e.target.value)}>
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
                            <a href="/cajas/gener40/exportar" className={cajasFormBtnSecondary}>
                                <FileSpreadsheet className="size-4" />
                                Excel
                            </a>
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
                            <h3 className="text-foreground text-sm font-semibold">Acciones de módulos</h3>
                            <p className="text-muted-foreground text-xs">
                                {pagination.total} registro{pagination.total === 1 ? '' : 's'} en total
                            </p>
                        </div>
                    </div>

                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        {items.data.length === 0 ? (
                            <p className="text-muted-foreground py-8 text-center text-sm">No hay registros para mostrar.</p>
                        ) : (
                            <div className="border-border overflow-x-auto rounded-lg border">
                                <table className="divide-border min-w-full divide-y text-sm">
                                    <thead className="bg-muted/40">
                                        <tr>
                                            <th className="text-muted-foreground px-3 py-2 text-left font-medium">Código</th>
                                            <th className="text-muted-foreground px-3 py-2 text-left font-medium">Detalle</th>
                                            <th className="text-muted-foreground px-3 py-2 text-left font-medium">Controller</th>
                                            <th className="text-muted-foreground px-3 py-2 text-left font-medium">Action</th>
                                            <th className="text-muted-foreground px-3 py-2 text-left font-medium">Orden</th>
                                            <th className="text-muted-foreground px-3 py-2 text-right font-medium">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-border bg-card divide-y">
                                        {items.data.map((row) => (
                                            <tr key={row.codigo} className="hover:bg-muted/20">
                                                <td className="text-foreground px-3 py-2.5 font-medium">{row.codigo}</td>
                                                <td className="text-foreground px-3 py-2.5">{row.detalle || '—'}</td>
                                                <td className="text-muted-foreground px-3 py-2.5">{row.controller || '—'}</td>
                                                <td className="text-muted-foreground px-3 py-2.5">{row.action || '—'}</td>
                                                <td className="text-muted-foreground px-3 py-2.5">{row.orden ?? '—'}</td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex justify-end gap-1.5">
                                                        <button type="button" onClick={() => openEdit(row)} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </button>
                                                        <button type="button" onClick={() => openDelete(row)} className={cajasActionDangerClass}>
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
                                <p className="text-muted-foreground text-xs">
                                    Mostrando {pagination.from ?? 0}–{pagination.to ?? 0} de {pagination.total}
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
                        )}
                    </div>
                </section>
            </div>

            <ConfirmDialog
                open={deleteTarget !== null}
                title="Eliminar acción de módulo"
                description={deleteDescription}
                confirmLabel={deleteTarget && deleteTarget.asignaciones > 0 ? 'Eliminar y revocar' : 'Eliminar'}
                processing={deleting}
                onConfirm={confirmDelete}
                onCancel={cancelDelete}
            />

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>{editingCodigo ? 'Editar acción de módulo' : 'Nueva acción de módulo'}</DialogTitle>
                        <DialogDescription>Define el permiso (código) y asócialo opcionalmente a un Controller y Action del menú.</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="codigo" className={cajasFormLabelClass}>
                                    Código *
                                </label>
                                <input
                                    id="codigo"
                                    name="codigo"
                                    required
                                    maxLength={5}
                                    disabled={Boolean(editingCodigo)}
                                    className={`${cajasFormInputClass} ${inputError('codigo')}`}
                                    value={form.codigo}
                                    onChange={handleChange}
                                />
                                {formErrors.codigo && <p className={cajasFormErrorClass}>{formErrors.codigo}</p>}
                            </div>
                            <div>
                                <label htmlFor="orden" className={cajasFormLabelClass}>
                                    Orden
                                </label>
                                <input
                                    id="orden"
                                    name="orden"
                                    type="number"
                                    min={0}
                                    max={99999}
                                    className={`${cajasFormInputClass} ${inputError('orden')}`}
                                    value={form.orden}
                                    onChange={handleChange}
                                />
                                {formErrors.orden && <p className={cajasFormErrorClass}>{formErrors.orden}</p>}
                            </div>
                        </div>

                        <div>
                            <label htmlFor="detalle" className={cajasFormLabelClass}>
                                Detalle *
                            </label>
                            <input
                                id="detalle"
                                name="detalle"
                                required
                                maxLength={255}
                                className={`${cajasFormInputClass} ${inputError('detalle')}`}
                                value={form.detalle}
                                onChange={handleChange}
                            />
                            {formErrors.detalle && <p className={cajasFormErrorClass}>{formErrors.detalle}</p>}
                        </div>

                        <div>
                            <label htmlFor="controller" className={cajasFormLabelClass}>
                                Controller
                            </label>
                            <CajasSearchSelect
                                id="controller"
                                value={form.controller}
                                placeholder="Seleccione un controller"
                                options={controllerOptions}
                                onChange={(value) => setForm((prev) => ({ ...prev, controller: value }))}
                            />
                            {formErrors.controller && <p className={cajasFormErrorClass}>{formErrors.controller}</p>}
                        </div>

                        <div>
                            <label htmlFor="action" className={cajasFormLabelClass}>
                                Action
                            </label>
                            <CajasSearchSelect
                                id="action"
                                value={form.action}
                                placeholder="Seleccione una action"
                                options={actionOptions}
                                onChange={(value) => setForm((prev) => ({ ...prev, action: value }))}
                            />
                            {formErrors.action && <p className={cajasFormErrorClass}>{formErrors.action}</p>}
                        </div>

                        <DialogFooter className="gap-2 sm:gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={processing}>
                                {processing ? 'Guardando...' : editingCodigo ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
