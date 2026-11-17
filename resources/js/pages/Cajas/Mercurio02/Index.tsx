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

type Mercurio02Row = {
    codcaj: string;
    nit: string;
    razsoc: string;
    sigla: string;
    email: string;
    direccion: string;
    telefono: string;
    codciu: string;
    pagweb: string;
    pagfac: string | null;
    pagtwi: string | null;
    pagyou: string | null;
};

type CiudadOption = {
    value: string;
    label: string;
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
        data: Mercurio02Row[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    ciudades: CiudadOption[];
    canCreate: boolean;
    filters: {
        q: string;
        per_page: number;
    };
};

type FormState = Mercurio02Row;

const emptyForm = (): FormState => ({
    codcaj: '',
    nit: '',
    razsoc: '',
    sigla: '',
    email: '',
    direccion: '',
    telefono: '',
    codciu: '',
    pagweb: '',
    pagfac: '',
    pagtwi: '',
    pagyou: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, ciudades, canCreate, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingCodcaj, setEditingCodcaj] = useState<string | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm);
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    const formErrors = page.props.errors ?? {};

    const ciudadLabelByCode = useMemo(() => {
        const map = new Map<string, string>();
        for (const ciudad of ciudades) {
            map.set(ciudad.value, ciudad.label);
        }
        return map;
    }, [ciudades]);

    useEffect(() => {
        setQ(filters.q);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.per_page]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setEditingCodcaj(null);
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
        router.get('/cajas/mercurio02/index', params, {
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
        setEditingCodcaj(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: Mercurio02Row) => {
        setEditingCodcaj(row.codcaj);
        setForm({
            codcaj: row.codcaj ?? '',
            nit: row.nit ?? '',
            razsoc: row.razsoc ?? '',
            sigla: row.sigla ?? '',
            email: row.email ?? '',
            direccion: row.direccion ?? '',
            telefono: row.telefono ?? '',
            codciu: row.codciu ?? '',
            pagweb: row.pagweb ?? '',
            pagfac: row.pagfac ?? '',
            pagtwi: row.pagtwi ?? '',
            pagyou: row.pagyou ?? '',
        });
        setDialogOpen(true);
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        const payload = {
            ...form,
            codcaj: form.codcaj.trim(),
        };

        const options = {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
            onSuccess: () => {
                setDialogOpen(false);
                setEditingCodcaj(null);
                setForm(emptyForm());
            },
        };

        if (editingCodcaj) {
            router.put(`/cajas/mercurio02/${encodeURIComponent(editingCodcaj)}`, payload, options);
        } else {
            router.post('/cajas/mercurio02', payload, options);
        }
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Datos Caja">
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
                                        placeholder="Caja, NIT, razón social, email..."
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
                            {canCreate && (
                                <button type="button" onClick={openCreate} className={cajasFormBtnPrimary}>
                                    <Plus className="size-4" />
                                    Nuevo
                                </button>
                            )}
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
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Caja</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">NIT</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Razón social</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Ciudad</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {items.data.map((row) => (
                                            <tr key={row.codcaj} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 font-medium text-foreground">{row.codcaj}</td>
                                                <td className="px-3 py-2.5 text-foreground">{row.nit}</td>
                                                <td className="max-w-[240px] truncate px-3 py-2.5 text-foreground" title={row.razsoc}>
                                                    {row.razsoc}
                                                </td>
                                                <td className="px-3 py-2.5 text-muted-foreground">
                                                    {ciudadLabelByCode.get(row.codciu) || row.codciu || '—'}
                                                </td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex justify-end">
                                                        <button type="button" onClick={() => openEdit(row)} className={cajasActionLinkClass}>
                                                            <Pencil className="size-3.5" />
                                                            Editar
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
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>{editingCodcaj ? 'Editar datos de caja' : 'Nuevos datos de caja'}</DialogTitle>
                        <DialogDescription>Información institucional y de contacto de la caja.</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="codcaj" className={cajasFormLabelClass}>
                                    Código caja *
                                </label>
                                <input
                                    id="codcaj"
                                    name="codcaj"
                                    required
                                    maxLength={6}
                                    disabled={Boolean(editingCodcaj)}
                                    className={`${cajasFormInputClass} ${inputError('codcaj')}`}
                                    value={form.codcaj}
                                    onChange={handleChange}
                                />
                                {formErrors.codcaj && <p className={cajasFormErrorClass}>{formErrors.codcaj}</p>}
                            </div>
                            <div>
                                <label htmlFor="nit" className={cajasFormLabelClass}>
                                    NIT *
                                </label>
                                <input
                                    id="nit"
                                    name="nit"
                                    required
                                    maxLength={13}
                                    className={`${cajasFormInputClass} ${inputError('nit')}`}
                                    value={form.nit}
                                    onChange={handleChange}
                                />
                                {formErrors.nit && <p className={cajasFormErrorClass}>{formErrors.nit}</p>}
                            </div>
                            <div className="sm:col-span-2">
                                <label htmlFor="razsoc" className={cajasFormLabelClass}>
                                    Razón social *
                                </label>
                                <input
                                    id="razsoc"
                                    name="razsoc"
                                    required
                                    maxLength={60}
                                    className={`${cajasFormInputClass} ${inputError('razsoc')}`}
                                    value={form.razsoc}
                                    onChange={handleChange}
                                />
                                {formErrors.razsoc && <p className={cajasFormErrorClass}>{formErrors.razsoc}</p>}
                            </div>
                            <div>
                                <label htmlFor="sigla" className={cajasFormLabelClass}>
                                    Sigla *
                                </label>
                                <input
                                    id="sigla"
                                    name="sigla"
                                    required
                                    maxLength={30}
                                    className={`${cajasFormInputClass} ${inputError('sigla')}`}
                                    value={form.sigla}
                                    onChange={handleChange}
                                />
                                {formErrors.sigla && <p className={cajasFormErrorClass}>{formErrors.sigla}</p>}
                            </div>
                            <div>
                                <label htmlFor="email" className={cajasFormLabelClass}>
                                    Correo electrónico *
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    maxLength={90}
                                    className={`${cajasFormInputClass} ${inputError('email')}`}
                                    value={form.email}
                                    onChange={handleChange}
                                />
                                {formErrors.email && <p className={cajasFormErrorClass}>{formErrors.email}</p>}
                            </div>
                            <div className="sm:col-span-2">
                                <label htmlFor="direccion" className={cajasFormLabelClass}>
                                    Dirección *
                                </label>
                                <input
                                    id="direccion"
                                    name="direccion"
                                    required
                                    maxLength={90}
                                    className={`${cajasFormInputClass} ${inputError('direccion')}`}
                                    value={form.direccion}
                                    onChange={handleChange}
                                />
                                {formErrors.direccion && <p className={cajasFormErrorClass}>{formErrors.direccion}</p>}
                            </div>
                            <div>
                                <label htmlFor="telefono" className={cajasFormLabelClass}>
                                    Teléfono *
                                </label>
                                <input
                                    id="telefono"
                                    name="telefono"
                                    required
                                    maxLength={20}
                                    className={`${cajasFormInputClass} ${inputError('telefono')}`}
                                    value={form.telefono}
                                    onChange={handleChange}
                                />
                                {formErrors.telefono && <p className={cajasFormErrorClass}>{formErrors.telefono}</p>}
                            </div>
                            <div>
                                <label htmlFor="codciu" className={cajasFormLabelClass}>
                                    Ciudad *
                                </label>
                                <select
                                    id="codciu"
                                    name="codciu"
                                    required
                                    className={`${cajasFormSelectClass} ${inputError('codciu')}`}
                                    value={form.codciu}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccione…</option>
                                    {ciudades.map((ciudad) => (
                                        <option key={ciudad.value} value={ciudad.value}>
                                            {ciudad.label}
                                        </option>
                                    ))}
                                    {form.codciu && !ciudadLabelByCode.has(form.codciu) && (
                                        <option value={form.codciu}>{form.codciu}</option>
                                    )}
                                </select>
                                {formErrors.codciu && <p className={cajasFormErrorClass}>{formErrors.codciu}</p>}
                                {ciudades.length === 0 && (
                                    <p className="mt-1 text-xs text-muted-foreground">No se pudieron cargar ciudades desde la API.</p>
                                )}
                            </div>
                            <div>
                                <label htmlFor="pagweb" className={cajasFormLabelClass}>
                                    Sitio web *
                                </label>
                                <input
                                    id="pagweb"
                                    name="pagweb"
                                    required
                                    maxLength={45}
                                    className={`${cajasFormInputClass} ${inputError('pagweb')}`}
                                    value={form.pagweb}
                                    onChange={handleChange}
                                />
                                {formErrors.pagweb && <p className={cajasFormErrorClass}>{formErrors.pagweb}</p>}
                            </div>
                            <div>
                                <label htmlFor="pagfac" className={cajasFormLabelClass}>
                                    Facebook
                                </label>
                                <input
                                    id="pagfac"
                                    name="pagfac"
                                    maxLength={45}
                                    className={`${cajasFormInputClass} ${inputError('pagfac')}`}
                                    value={form.pagfac ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.pagfac && <p className={cajasFormErrorClass}>{formErrors.pagfac}</p>}
                            </div>
                            <div>
                                <label htmlFor="pagtwi" className={cajasFormLabelClass}>
                                    Twitter
                                </label>
                                <input
                                    id="pagtwi"
                                    name="pagtwi"
                                    maxLength={45}
                                    className={`${cajasFormInputClass} ${inputError('pagtwi')}`}
                                    value={form.pagtwi ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.pagtwi && <p className={cajasFormErrorClass}>{formErrors.pagtwi}</p>}
                            </div>
                            <div>
                                <label htmlFor="pagyou" className={cajasFormLabelClass}>
                                    YouTube
                                </label>
                                <input
                                    id="pagyou"
                                    name="pagyou"
                                    maxLength={45}
                                    className={`${cajasFormInputClass} ${inputError('pagyou')}`}
                                    value={form.pagyou ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.pagyou && <p className={cajasFormErrorClass}>{formErrors.pagyou}</p>}
                            </div>
                        </div>

                        <DialogFooter className="gap-2 sm:gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={processing}>
                                {processing ? 'Guardando...' : editingCodcaj ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
