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
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight, Filter, ImageIcon, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState, type ChangeEvent, type FormEvent } from 'react';

type BannerRow = {
    id: number;
    content_html: string | null;
    imagen: string;
    imagen_nombre: string | null;
    url_imagen: string | null;
    fecha_inicia: string | null;
    fecha_finaliza: string | null;
    estado: string;
    estado_label: string;
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
        data: BannerRow[];
        meta: {
            pagination: PaginationMeta;
        };
    };
    filters: {
        q: string;
        estado: string;
        per_page: number;
    };
};

type FormState = {
    estado: string;
    fecha_inicia: string;
    fecha_finaliza: string;
    url_imagen: string;
    content_html: string;
};

const emptyForm = (): FormState => ({
    estado: 'A',
    fecha_inicia: '',
    fecha_finaliza: '',
    url_imagen: '',
    content_html: '',
});

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

export default function Index({ items, filters }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const pagination = items.meta.pagination;
    const formErrors = page.props.errors ?? {};

    const [q, setQ] = useState(filters.q);
    const [estado, setEstado] = useState(filters.estado);
    const [perPage, setPerPage] = useState(String(filters.per_page || 10));
    const [dialogOpen, setDialogOpen] = useState(false);
    const [previewOpen, setPreviewOpen] = useState(false);
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [currentImage, setCurrentImage] = useState<string | null>(null);
    const [imageFile, setImageFile] = useState<File | null>(null);
    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm());
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        setQ(filters.q);
        setEstado(filters.estado);
        setPerPage(String(filters.per_page || 10));
    }, [filters.q, filters.estado, filters.per_page]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setEditingId(null);
            setForm(emptyForm());
            setImageFile(null);
            setImagePreview(null);
            setCurrentImage(null);
        }
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    useEffect(() => {
        return () => {
            if (imagePreview) URL.revokeObjectURL(imagePreview);
        };
    }, [imagePreview]);

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
        router.get('/cajas/banners/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const applyFilters = () => {
        visit({
            q: q.trim(),
            estado,
            per_page: Number(perPage) || 10,
            page: 1,
        });
    };

    const clearFilters = () => {
        setQ('');
        setEstado('');
        setPerPage('10');
        visit({ q: '', estado: '', per_page: 10, page: 1 });
    };

    const goToPage = (pageNumber: number) => {
        if (pageNumber < 1 || pageNumber > pagination.last_page) return;
        visit({
            q: filters.q,
            estado: filters.estado,
            per_page: filters.per_page,
            page: pageNumber,
        });
    };

    const openCreate = () => {
        setEditingId(null);
        setForm(emptyForm());
        setCurrentImage(null);
        setImageFile(null);
        setImagePreview(null);
        setDialogOpen(true);
    };

    const openEdit = (row: BannerRow) => {
        setEditingId(row.id);
        setForm({
            estado: row.estado || 'A',
            fecha_inicia: row.fecha_inicia || '',
            fecha_finaliza: row.fecha_finaliza || '',
            url_imagen: row.url_imagen || '',
            content_html: row.content_html || '',
        });
        setCurrentImage(row.imagen || null);
        setImageFile(null);
        setImagePreview(null);
        setDialogOpen(true);
    };

    const handleChange = (e: ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        if (imagePreview) URL.revokeObjectURL(imagePreview);
        setImageFile(file);
        setImagePreview(file ? URL.createObjectURL(file) : null);
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        const payload: Record<string, string | File> = {
            estado: form.estado,
            fecha_inicia: form.fecha_inicia,
            fecha_finaliza: form.fecha_finaliza,
            url_imagen: form.url_imagen.trim() || '',
            content_html: form.content_html,
        };

        if (imageFile) {
            payload.imagen = imageFile;
        }

        const options = {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        };

        if (editingId) {
            router.post(`/cajas/banners/${editingId}`, payload, options);
        } else {
            router.post('/cajas/banners', payload, options);
        }
    };

    const handleDelete = (row: BannerRow) => {
        if (!confirm(`¿Eliminar el banner #${row.id}?`)) return;

        router.delete(`/cajas/banners/${row.id}`, { preserveScroll: true });
    };

    const openPreview = (url: string) => {
        if (!url) return;
        setPreviewUrl(url);
        setPreviewOpen(true);
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');
    const shownPreview = imagePreview || currentImage;

    return (
        <AppLayout title="Banners login">
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
                                        placeholder="HTML, URL, archivo, estado..."
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') applyFilters();
                                        }}
                                    />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="estado_filter" className={cajasFormLabelClass}>
                                    Estado
                                </label>
                                <select
                                    id="estado_filter"
                                    className={cajasFormSelectClass}
                                    value={estado}
                                    onChange={(e) => setEstado(e.target.value)}
                                >
                                    <option value="">Todos</option>
                                    <option value="A">Activo</option>
                                    <option value="I">Inactivo</option>
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
                            <h3 className="text-sm font-semibold text-foreground">Galería de banners</h3>
                            <p className="text-xs text-muted-foreground">
                                {pagination.total} registro{pagination.total === 1 ? '' : 's'} en total
                            </p>
                        </div>
                    </div>

                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        {items.data.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 text-center">
                                <ImageIcon className="mb-3 size-10 text-muted-foreground/50" />
                                <p className="text-sm text-muted-foreground">No hay banners para mostrar.</p>
                            </div>
                        ) : (
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                                {items.data.map((row) => (
                                    <article key={row.id} className="overflow-hidden rounded-lg border border-border bg-card">
                                        <button
                                            type="button"
                                            className="block w-full bg-muted/30"
                                            onClick={() => openPreview(row.imagen)}
                                            disabled={!row.imagen}
                                        >
                                            {row.imagen ? (
                                                <img
                                                    src={row.imagen}
                                                    alt={`Banner ${row.id}`}
                                                    className="h-28 w-full object-cover"
                                                />
                                            ) : (
                                                <div className="flex h-28 items-center justify-center text-muted-foreground">
                                                    <ImageIcon className="size-7 opacity-40" />
                                                </div>
                                            )}
                                        </button>
                                        <div className="space-y-3 p-4">
                                            <div className="flex items-start justify-between gap-2">
                                                <div>
                                                    <p className="text-sm font-semibold text-foreground">#{row.id}</p>
                                                    <p className="text-xs text-muted-foreground">
                                                        {row.fecha_inicia || '—'} → {row.fecha_finaliza || '—'}
                                                    </p>
                                                </div>
                                                <span
                                                    className={`${cajasBadgeClass} ${
                                                        row.estado === 'A'
                                                            ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                            : 'bg-muted text-muted-foreground ring-border'
                                                    }`}
                                                >
                                                    {row.estado_label}
                                                </span>
                                            </div>
                                            {row.content_html && (
                                                <p className="line-clamp-2 text-xs text-muted-foreground">{row.content_html.replace(/<[^>]+>/g, ' ')}</p>
                                            )}
                                            <div className="flex flex-wrap gap-1.5">
                                                <button type="button" onClick={() => openEdit(row)} className={cajasActionLinkClass}>
                                                    <Pencil className="size-3.5" />
                                                    Editar
                                                </button>
                                                <button type="button" onClick={() => handleDelete(row)} className={cajasActionDangerClass}>
                                                    <Trash2 className="size-3.5" />
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                ))}
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

                <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                    <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                        <DialogHeader>
                            <DialogTitle>{editingId ? 'Editar banner' : 'Nuevo banner'}</DialogTitle>
                            <DialogDescription>
                                Define vigencia, estado e imagen (archivo o URL) para el diálogo del login.
                            </DialogDescription>
                        </DialogHeader>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                            {shownPreview && (
                                <div>
                                    <label className={cajasFormLabelClass}>Vista previa</label>
                                    <img
                                        src={shownPreview}
                                        alt="Vista previa"
                                        className="mt-1 max-h-40 w-full rounded-md border border-border object-contain"
                                    />
                                </div>
                            )}

                            <div>
                                <label htmlFor="estado" className={cajasFormLabelClass}>
                                    Estado *
                                </label>
                                <select
                                    id="estado"
                                    name="estado"
                                    className={`${cajasFormSelectClass} ${inputError('estado')}`}
                                    value={form.estado}
                                    onChange={handleChange}
                                    required
                                >
                                    <option value="A">Activo</option>
                                    <option value="I">Inactivo</option>
                                </select>
                                {formErrors.estado && <p className={cajasFormErrorClass}>{formErrors.estado}</p>}
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label htmlFor="fecha_inicia" className={cajasFormLabelClass}>
                                        Fecha inicia *
                                    </label>
                                    <input
                                        id="fecha_inicia"
                                        name="fecha_inicia"
                                        type="date"
                                        className={`${cajasFormInputClass} ${inputError('fecha_inicia')}`}
                                        value={form.fecha_inicia}
                                        onChange={handleChange}
                                        required
                                    />
                                    {formErrors.fecha_inicia && <p className={cajasFormErrorClass}>{formErrors.fecha_inicia}</p>}
                                </div>
                                <div>
                                    <label htmlFor="fecha_finaliza" className={cajasFormLabelClass}>
                                        Fecha finaliza *
                                    </label>
                                    <input
                                        id="fecha_finaliza"
                                        name="fecha_finaliza"
                                        type="date"
                                        className={`${cajasFormInputClass} ${inputError('fecha_finaliza')}`}
                                        value={form.fecha_finaliza}
                                        onChange={handleChange}
                                        required
                                    />
                                    {formErrors.fecha_finaliza && <p className={cajasFormErrorClass}>{formErrors.fecha_finaliza}</p>}
                                </div>
                            </div>

                            <div>
                                <label htmlFor="url_imagen" className={cajasFormLabelClass}>
                                    URL imagen
                                </label>
                                <input
                                    id="url_imagen"
                                    name="url_imagen"
                                    type="url"
                                    className={`${cajasFormInputClass} ${inputError('url_imagen')}`}
                                    placeholder="https://ejemplo.com/banner.jpg"
                                    maxLength={500}
                                    value={form.url_imagen}
                                    onChange={handleChange}
                                />
                                {formErrors.url_imagen && <p className={cajasFormErrorClass}>{formErrors.url_imagen}</p>}
                            </div>

                            <div>
                                <label htmlFor="imagen" className={cajasFormLabelClass}>
                                    Imagen {editingId ? '' : '(requerida si no hay URL)'}
                                </label>
                                <input
                                    id="imagen"
                                    name="imagen"
                                    type="file"
                                    accept="image/jpeg,image/png,image/jpg"
                                    className={`${cajasFormInputClass} ${inputError('imagen')}`}
                                    onChange={handleFileChange}
                                />
                                <p className="mt-1 text-xs text-muted-foreground">JPG, JPEG o PNG. Máx. 5 MB.</p>
                                {formErrors.imagen && <p className={cajasFormErrorClass}>{formErrors.imagen}</p>}
                            </div>

                            <div>
                                <label htmlFor="content_html" className={cajasFormLabelClass}>
                                    Contenido HTML
                                </label>
                                <textarea
                                    id="content_html"
                                    name="content_html"
                                    rows={4}
                                    className={`${cajasFormTextareaClass} ${inputError('content_html')}`}
                                    placeholder="Texto o HTML del diálogo promocional"
                                    value={form.content_html}
                                    onChange={handleChange}
                                />
                                {formErrors.content_html && <p className={cajasFormErrorClass}>{formErrors.content_html}</p>}
                            </div>

                            <DialogFooter>
                                <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                    Cancelar
                                </button>
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? 'Guardando...' : 'Guardar'}
                                </button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog open={previewOpen} onOpenChange={setPreviewOpen}>
                    <DialogContent className="sm:max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Vista previa</DialogTitle>
                        </DialogHeader>
                        {previewUrl && (
                            <img src={previewUrl} alt="Banner" className="max-h-[70vh] w-full rounded-md object-contain" />
                        )}
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
