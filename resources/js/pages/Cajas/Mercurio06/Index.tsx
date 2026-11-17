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
    cajasActionMutedClass,
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
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Filter,
    ListTree,
    Pencil,
    Plus,
    Search,
    Trash2,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type TipoRow = {
    tipo: string;
    detalle: string;
};

type CampoRow = {
    tipo: string;
    campo: string;
    detalle: string;
    orden: number;
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
        data: TipoRow[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    filters: {
        q: string;
        per_page: number;
        campos_tipo: string | null;
    };
    campos: CampoRow[];
    camposTipo: string | null;
};

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

const emptyTipo = () => ({ tipo: '', detalle: '' });
const emptyCampo = () => ({ campo: '', detalle: '', orden: 1 });

export default function Index({ items, filters, campos, camposTipo }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [toast, setToast] = useState<string | null>(null);

    const [tipoDialogOpen, setTipoDialogOpen] = useState(false);
    const [editingTipo, setEditingTipo] = useState<string | null>(null);
    const [tipoForm, setTipoForm] = useState(emptyTipo());
    const [tipoProcessing, setTipoProcessing] = useState(false);

    const [camposDialogOpen, setCamposDialogOpen] = useState(Boolean(camposTipo));
    const [campoFormOpen, setCampoFormOpen] = useState(false);
    const [editingCampo, setEditingCampo] = useState<string | null>(null);
    const [campoForm, setCampoForm] = useState(emptyCampo());
    const [campoProcessing, setCampoProcessing] = useState(false);

    useEffect(() => {
        setQ(filters.q);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.per_page]);

    useEffect(() => {
        setCamposDialogOpen(Boolean(camposTipo));
        if (!camposTipo) {
            setCampoFormOpen(false);
            setEditingCampo(null);
            setCampoForm(emptyCampo());
        }
    }, [camposTipo]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setTipoDialogOpen(false);
            setEditingTipo(null);
            setTipoForm(emptyTipo());
            setCampoFormOpen(false);
            setEditingCampo(null);
            setCampoForm(emptyCampo());
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

    const visit = (params: Record<string, string | number | null | undefined>, options?: { only?: string[] }) => {
        const query: Record<string, string | number> = {};
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                query[key] = value;
            }
        });

        router.get('/cajas/mercurio06/index', query, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: options?.only,
        });
    };

    const baseQuery = () => ({
        q: filters.q,
        per_page: filters.per_page,
        campos_tipo: filters.campos_tipo,
    });

    const applyFilters = () => {
        visit({ q: q.trim(), per_page: Number(perPage) || 10, page: 1, campos_tipo: filters.campos_tipo });
    };

    const clearFilters = () => {
        setQ('');
        setPerPage('10');
        visit({ q: '', per_page: 10, page: 1, campos_tipo: filters.campos_tipo });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({ ...baseQuery(), page: pageNumber });
    };

    const openCreateTipo = () => {
        setEditingTipo(null);
        setTipoForm(emptyTipo());
        setTipoDialogOpen(true);
    };

    const openEditTipo = (row: TipoRow) => {
        setEditingTipo(row.tipo);
        setTipoForm({ tipo: row.tipo, detalle: row.detalle });
        setTipoDialogOpen(true);
    };

    const submitTipo = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setTipoProcessing(true);

        const payload = {
            tipo: tipoForm.tipo.trim(),
            detalle: tipoForm.detalle.trim(),
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setTipoProcessing(false),
        };

        if (editingTipo) {
            router.put(`/cajas/mercurio06/${encodeURIComponent(editingTipo)}`, payload, options);
        } else {
            router.post('/cajas/mercurio06', payload, options);
        }
    };

    const deleteTipo = (row: TipoRow) => {
        if (!confirm(`¿Eliminar el tipo "${row.tipo}"? También se borrarán sus campos asociados.`)) {
            return;
        }

        router.delete(`/cajas/mercurio06/${encodeURIComponent(row.tipo)}`, {
            preserveScroll: true,
        });
    };

    const openCampos = (tipo: string) => {
        visit({ ...baseQuery(), campos_tipo: tipo }, { only: ['campos', 'camposTipo', 'filters'] });
    };

    const closeCampos = () => {
        setCamposDialogOpen(false);
        visit({ q: filters.q, per_page: filters.per_page, page: pagination.current_page, campos_tipo: null }, {
            only: ['campos', 'camposTipo', 'filters'],
        });
    };

    const openCreateCampo = () => {
        setEditingCampo(null);
        setCampoForm(emptyCampo());
        setCampoFormOpen(true);
    };

    const openEditCampo = (row: CampoRow) => {
        setEditingCampo(row.campo);
        setCampoForm({
            campo: row.campo,
            detalle: row.detalle,
            orden: row.orden,
        });
        setCampoFormOpen(true);
    };

    const submitCampo = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (!camposTipo) return;

        setCampoProcessing(true);
        const payload = {
            campo: campoForm.campo.trim(),
            detalle: campoForm.detalle.trim(),
            orden: Number(campoForm.orden) || 0,
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setCampoProcessing(false),
        };

        if (editingCampo) {
            router.put(
                `/cajas/mercurio06/${encodeURIComponent(camposTipo)}/campos/${encodeURIComponent(editingCampo)}`,
                payload,
                options,
            );
        } else {
            router.post(`/cajas/mercurio06/${encodeURIComponent(camposTipo)}/campos`, payload, options);
        }
    };

    const deleteCampo = (row: CampoRow) => {
        if (!confirm(`¿Eliminar el campo "${row.campo}"?`)) return;

        router.delete(
            `/cajas/mercurio06/${encodeURIComponent(row.tipo)}/campos/${encodeURIComponent(row.campo)}`,
            { preserveScroll: true },
        );
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Tipos de acceso">
            <div className={cajasPageClass}>
                {toast && (
                    <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>
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
                            <button type="button" onClick={openCreateTipo} className={cajasFormBtnPrimary}>
                                <Plus className="size-4" />
                                Nuevo
                            </button>
                        </div>
                    </div>
                </section>

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex items-center justify-between`}>
                        <div>
                            <h3 className="text-sm font-semibold text-foreground">Tipos de acceso</h3>
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
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Tipo</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Detalle</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {items.data.map((row) => (
                                            <tr key={row.tipo} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 font-medium text-foreground">{row.tipo}</td>
                                                <td className="px-3 py-2.5 text-foreground">{row.detalle}</td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex flex-wrap justify-end gap-1.5">
                                                        <button type="button" onClick={() => openCampos(row.tipo)} className={cajasActionMutedClass}>
                                                            <ListTree className="size-3.5" />
                                                            Campos
                                                        </button>
                                                        <button type="button" onClick={() => openEditTipo(row)} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </button>
                                                        <button type="button" onClick={() => deleteTipo(row)} className={cajasActionDangerClass}>
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

            <Dialog open={tipoDialogOpen} onOpenChange={setTipoDialogOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>{editingTipo ? 'Editar tipo de acceso' : 'Nuevo tipo de acceso'}</DialogTitle>
                        <DialogDescription>Código y descripción del tipo de acceso.</DialogDescription>
                    </DialogHeader>
                    <form onSubmit={submitTipo} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}
                        <div>
                            <label htmlFor="tipo" className={cajasFormLabelClass}>
                                Tipo *
                            </label>
                            <input
                                id="tipo"
                                name="tipo"
                                required
                                maxLength={2}
                                disabled={Boolean(editingTipo)}
                                className={`${cajasFormInputClass} ${inputError('tipo')}`}
                                value={tipoForm.tipo}
                                onChange={(e) => setTipoForm((prev) => ({ ...prev, tipo: e.target.value }))}
                            />
                            {formErrors.tipo && <p className={cajasFormErrorClass}>{formErrors.tipo}</p>}
                        </div>
                        <div>
                            <label htmlFor="detalle" className={cajasFormLabelClass}>
                                Detalle *
                            </label>
                            <input
                                id="detalle"
                                name="detalle"
                                required
                                maxLength={40}
                                className={`${cajasFormInputClass} ${inputError('detalle')}`}
                                value={tipoForm.detalle}
                                onChange={(e) => setTipoForm((prev) => ({ ...prev, detalle: e.target.value }))}
                            />
                            {formErrors.detalle && <p className={cajasFormErrorClass}>{formErrors.detalle}</p>}
                        </div>
                        <DialogFooter className="gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setTipoDialogOpen(false)} disabled={tipoProcessing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={tipoProcessing}>
                                {tipoProcessing ? 'Guardando...' : editingTipo ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog
                open={camposDialogOpen}
                onOpenChange={(open) => {
                    if (!open) closeCampos();
                    else setCamposDialogOpen(true);
                }}
            >
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>Campos del tipo {camposTipo}</DialogTitle>
                        <DialogDescription>Configura los campos asociados a este tipo de acceso.</DialogDescription>
                    </DialogHeader>

                    <div className="space-y-4">
                        {formErrors.campo_form && <p className={cajasFormErrorClass}>{formErrors.campo_form}</p>}

                        <div className="flex justify-end">
                            <button type="button" onClick={openCreateCampo} className={cajasFormBtnPrimary}>
                                <Plus className="size-4" />
                                Agregar campo
                            </button>
                        </div>

                        {campoFormOpen && (
                            <form onSubmit={submitCampo} className="rounded-lg border border-border bg-muted/20 p-4 space-y-3">
                                <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div>
                                        <label htmlFor="campo" className={cajasFormLabelClass}>
                                            Campo *
                                        </label>
                                        <input
                                            id="campo"
                                            required
                                            maxLength={20}
                                            disabled={Boolean(editingCampo)}
                                            className={`${cajasFormInputClass} ${inputError('campo')}`}
                                            value={campoForm.campo}
                                            onChange={(e) => setCampoForm((prev) => ({ ...prev, campo: e.target.value }))}
                                        />
                                        {formErrors.campo && <p className={cajasFormErrorClass}>{formErrors.campo}</p>}
                                    </div>
                                    <div>
                                        <label htmlFor="detalle_campo" className={cajasFormLabelClass}>
                                            Detalle *
                                        </label>
                                        <input
                                            id="detalle_campo"
                                            required
                                            maxLength={45}
                                            className={`${cajasFormInputClass} ${inputError('detalle')}`}
                                            value={campoForm.detalle}
                                            onChange={(e) => setCampoForm((prev) => ({ ...prev, detalle: e.target.value }))}
                                        />
                                    </div>
                                    <div>
                                        <label htmlFor="orden" className={cajasFormLabelClass}>
                                            Orden *
                                        </label>
                                        <input
                                            id="orden"
                                            type="number"
                                            required
                                            min={0}
                                            className={`${cajasFormInputClass} ${inputError('orden')}`}
                                            value={campoForm.orden}
                                            onChange={(e) => setCampoForm((prev) => ({ ...prev, orden: Number(e.target.value) }))}
                                        />
                                        {formErrors.orden && <p className={cajasFormErrorClass}>{formErrors.orden}</p>}
                                    </div>
                                </div>
                                <div className="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        className={cajasFormBtnSecondary}
                                        onClick={() => {
                                            setCampoFormOpen(false);
                                            setEditingCampo(null);
                                            setCampoForm(emptyCampo());
                                        }}
                                        disabled={campoProcessing}
                                    >
                                        Cancelar
                                    </button>
                                    <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={campoProcessing}>
                                        {campoProcessing ? 'Guardando...' : editingCampo ? 'Actualizar' : 'Adicionar'}
                                    </button>
                                </div>
                            </form>
                        )}

                        <div className="overflow-x-auto rounded-lg border border-border">
                            <table className="min-w-full divide-y divide-border text-sm">
                                <thead className="bg-muted/40">
                                    <tr>
                                        <th className="px-3 py-2 text-left font-medium text-muted-foreground">Campo</th>
                                        <th className="px-3 py-2 text-left font-medium text-muted-foreground">Detalle</th>
                                        <th className="px-3 py-2 text-left font-medium text-muted-foreground">Orden</th>
                                        <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border bg-card">
                                    {campos.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-3 py-6 text-center text-muted-foreground">
                                                No hay campos para este tipo.
                                            </td>
                                        </tr>
                                    ) : (
                                        campos.map((row) => (
                                            <tr key={`${row.tipo}-${row.campo}`} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 font-medium text-foreground">{row.campo}</td>
                                                <td className="px-3 py-2.5 text-foreground">{row.detalle}</td>
                                                <td className="px-3 py-2.5 text-muted-foreground">{row.orden}</td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex justify-end gap-1.5">
                                                        <button type="button" onClick={() => openEditCampo(row)} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
                                                        </button>
                                                        <button type="button" onClick={() => deleteCampo(row)} className={cajasActionDangerClass}>
                                                            <Trash2 className="size-3.5" />
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <DialogFooter>
                        <button type="button" className={cajasFormBtnSecondary} onClick={closeCampos}>
                            Cerrar
                        </button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
