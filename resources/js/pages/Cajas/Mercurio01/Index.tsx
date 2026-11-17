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

type Mercurio01Row = {
    codapl: string;
    email: string | null;
    clave: string | null;
    path: string | null;
    ftpserver: string | null;
    pathserver: string | null;
    userserver: string | null;
    passserver: string | null;
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
        data: Mercurio01Row[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    filters: {
        q: string;
        per_page: number;
    };
};

type FormState = Mercurio01Row;

const emptyForm = (): FormState => ({
    codapl: '',
    email: '',
    clave: '',
    path: '',
    ftpserver: '',
    pathserver: '',
    userserver: '',
    passserver: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;

    const [q, setQ] = useState(filters.q);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingCodapl, setEditingCodapl] = useState<string | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm);
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    const formErrors = page.props.errors ?? {};

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
        let end = Math.min(last, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [pagination.current_page, pagination.last_page]);

    const visit = (params: Record<string, string | number>) => {
        router.get('/cajas/mercurio01/index', params, {
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
        setEditingCodapl(null);
        setForm(emptyForm());
        setDialogOpen(true);
    };

    const openEdit = (row: Mercurio01Row) => {
        setEditingCodapl(row.codapl);
        setForm({
            codapl: row.codapl ?? '',
            email: row.email ?? '',
            clave: row.clave ?? '',
            path: row.path ?? '',
            ftpserver: row.ftpserver ?? '',
            pathserver: row.pathserver ?? '',
            userserver: row.userserver ?? '',
            passserver: row.passserver ?? '',
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
            ...form,
            codapl: form.codapl.trim(),
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
            router.put(`/cajas/mercurio01/${encodeURIComponent(editingCodapl)}`, payload, options);
        } else {
            router.post('/cajas/mercurio01', payload, options);
        }
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Configuración básica">
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
                                        placeholder="Aplicativo, email, path, server..."
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
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Aplicativo</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Email</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Path</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Server</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {items.data.map((row) => (
                                            <tr key={row.codapl} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 font-medium text-foreground">{row.codapl}</td>
                                                <td className="px-3 py-2.5 text-foreground">{row.email || '—'}</td>
                                                <td className="max-w-[220px] truncate px-3 py-2.5 text-muted-foreground" title={row.path || undefined}>
                                                    {row.path || '—'}
                                                </td>
                                                <td className="px-3 py-2.5 text-muted-foreground">{row.ftpserver || '—'}</td>
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
                <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>{editingCodapl ? 'Editar configuración' : 'Nueva configuración'}</DialogTitle>
                        <DialogDescription>Datos básicos de correo y servidor para el aplicativo.</DialogDescription>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label htmlFor="codapl" className={cajasFormLabelClass}>
                                    Aplicativo *
                                </label>
                                <input
                                    id="codapl"
                                    name="codapl"
                                    required
                                    maxLength={2}
                                    disabled={Boolean(editingCodapl)}
                                    className={`${cajasFormInputClass} ${inputError('codapl')}`}
                                    value={form.codapl}
                                    onChange={handleChange}
                                />
                                {formErrors.codapl && <p className={cajasFormErrorClass}>{formErrors.codapl}</p>}
                            </div>
                            <div>
                                <label htmlFor="email" className={cajasFormLabelClass}>
                                    Email
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    className={`${cajasFormInputClass} ${inputError('email')}`}
                                    value={form.email ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.email && <p className={cajasFormErrorClass}>{formErrors.email}</p>}
                            </div>
                            <div>
                                <label htmlFor="clave" className={cajasFormLabelClass}>
                                    Clave
                                </label>
                                <input
                                    id="clave"
                                    name="clave"
                                    type="password"
                                    autoComplete="new-password"
                                    className={`${cajasFormInputClass} ${inputError('clave')}`}
                                    value={form.clave ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.clave && <p className={cajasFormErrorClass}>{formErrors.clave}</p>}
                            </div>
                            <div>
                                <label htmlFor="path" className={cajasFormLabelClass}>
                                    Path *
                                </label>
                                <input
                                    id="path"
                                    name="path"
                                    required
                                    maxLength={45}
                                    className={`${cajasFormInputClass} ${inputError('path')}`}
                                    value={form.path ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.path && <p className={cajasFormErrorClass}>{formErrors.path}</p>}
                            </div>
                            <div>
                                <label htmlFor="ftpserver" className={cajasFormLabelClass}>
                                    FTP server
                                </label>
                                <input
                                    id="ftpserver"
                                    name="ftpserver"
                                    className={`${cajasFormInputClass} ${inputError('ftpserver')}`}
                                    value={form.ftpserver ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.ftpserver && <p className={cajasFormErrorClass}>{formErrors.ftpserver}</p>}
                            </div>
                            <div>
                                <label htmlFor="pathserver" className={cajasFormLabelClass}>
                                    Path server
                                </label>
                                <input
                                    id="pathserver"
                                    name="pathserver"
                                    className={`${cajasFormInputClass} ${inputError('pathserver')}`}
                                    value={form.pathserver ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.pathserver && <p className={cajasFormErrorClass}>{formErrors.pathserver}</p>}
                            </div>
                            <div>
                                <label htmlFor="userserver" className={cajasFormLabelClass}>
                                    User server
                                </label>
                                <input
                                    id="userserver"
                                    name="userserver"
                                    className={`${cajasFormInputClass} ${inputError('userserver')}`}
                                    value={form.userserver ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.userserver && <p className={cajasFormErrorClass}>{formErrors.userserver}</p>}
                            </div>
                            <div>
                                <label htmlFor="passserver" className={cajasFormLabelClass}>
                                    Pass server
                                </label>
                                <input
                                    id="passserver"
                                    name="passserver"
                                    type="password"
                                    autoComplete="new-password"
                                    className={`${cajasFormInputClass} ${inputError('passserver')}`}
                                    value={form.passserver ?? ''}
                                    onChange={handleChange}
                                />
                                {formErrors.passserver && <p className={cajasFormErrorClass}>{formErrors.passserver}</p>}
                            </div>
                        </div>

                        <DialogFooter className="gap-2 sm:gap-2">
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={`${cajasFormBtnPrimary} disabled:opacity-60`} disabled={processing}>
                                {processing ? 'Guardando...' : editingCodapl ? 'Actualizar' : 'Guardar'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
