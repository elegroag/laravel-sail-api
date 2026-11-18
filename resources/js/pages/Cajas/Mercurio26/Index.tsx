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
import { ArrowDown, ArrowUp, ImageIcon, Plus, Trash2, Video } from 'lucide-react';
import { useEffect, useState, type ChangeEvent, type FormEvent } from 'react';

type GaleriaItem = {
    numero: number;
    archivo: string;
    archivo_nombre: string | null;
    tipo: string;
    tipo_label: string;
    nota: string | null;
    orden: number;
    estado: string;
};

type Props = {
    items: GaleriaItem[];
};

type FormState = {
    tipo: string;
    nota: string;
};

const emptyForm = (): FormState => ({
    tipo: 'F',
    nota: '',
});

export default function Index({ items }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [dialogOpen, setDialogOpen] = useState(false);
    const [previewOpen, setPreviewOpen] = useState(false);
    const [previewItem, setPreviewItem] = useState<GaleriaItem | null>(null);
    const [form, setForm] = useState<FormState>(emptyForm());
    const [archivo, setArchivo] = useState<File | null>(null);
    const [archivoPreview, setArchivoPreview] = useState<string | null>(null);
    const [processing, setProcessing] = useState(false);
    const [busyNumero, setBusyNumero] = useState<number | null>(null);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setForm(emptyForm());
            setArchivo(null);
            if (archivoPreview) URL.revokeObjectURL(archivoPreview);
            setArchivoPreview(null);
        }
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    useEffect(() => {
        return () => {
            if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        };
    }, [archivoPreview]);

    const openCreate = () => {
        setForm(emptyForm());
        setArchivo(null);
        if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        setArchivoPreview(null);
        setDialogOpen(true);
    };

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        setArchivo(file);
        setArchivoPreview(file ? URL.createObjectURL(file) : null);

        if (file) {
            const ext = file.name.split('.').pop()?.toLowerCase() ?? '';
            if (ext === 'mp4') {
                setForm((prev) => ({ ...prev, tipo: 'V' }));
            } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                setForm((prev) => ({ ...prev, tipo: 'F' }));
            }
        }
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (!archivo) return;

        setProcessing(true);
        router.post(
            '/cajas/mercurio26',
            {
                tipo: form.tipo,
                nota: form.nota.trim(),
                archivo,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    const move = (numero: number, direction: 'arriba' | 'abajo') => {
        setBusyNumero(numero);
        router.post(`/cajas/mercurio26/${numero}/${direction}`, {}, {
            preserveScroll: true,
            onFinish: () => setBusyNumero(null),
        });
    };

    const handleDelete = (item: GaleriaItem) => {
        if (!confirm(`¿Eliminar el ítem #${item.numero} de la galería?`)) return;

        setBusyNumero(item.numero);
        router.delete(`/cajas/mercurio26/${item.numero}`, {
            preserveScroll: true,
            onFinish: () => setBusyNumero(null),
        });
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    return (
        <AppLayout title="Galería">
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
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between`}>
                        <div>
                            <h1 className="text-sm font-semibold text-foreground">Galería principal</h1>
                            <p className="text-xs text-muted-foreground">
                                {items.length} ítem{items.length === 1 ? '' : 's'} · fotos y videos promocionales
                            </p>
                        </div>
                        <button type="button" onClick={openCreate} className={cajasFormBtnPrimary}>
                            <Plus className="size-4" />
                            Agregar contenido
                        </button>
                    </div>

                    <div className={`${cajasCardBodyClass}`}>
                        {items.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 text-center">
                                <ImageIcon className="mb-3 size-10 text-muted-foreground/50" />
                                <p className="text-sm text-muted-foreground">No hay contenido en la galería.</p>
                            </div>
                        ) : (
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                                {items.map((item) => {
                                    const busy = busyNumero === item.numero;

                                    return (
                                        <article key={item.numero} className="overflow-hidden rounded-lg border border-border bg-card">
                                            <button
                                                type="button"
                                                className="block w-full bg-muted/30"
                                                onClick={() => {
                                                    setPreviewItem(item);
                                                    setPreviewOpen(true);
                                                }}
                                                disabled={!item.archivo}
                                            >
                                                {item.tipo === 'V' ? (
                                                    item.archivo ? (
                                                        <video
                                                            src={item.archivo}
                                                            className="h-28 w-full object-cover"
                                                            muted
                                                            playsInline
                                                        />
                                                    ) : (
                                                        <div className="flex h-28 items-center justify-center text-muted-foreground">
                                                            <Video className="size-7 opacity-40" />
                                                        </div>
                                                    )
                                                ) : item.archivo ? (
                                                    <img
                                                        src={item.archivo}
                                                        alt={item.nota || `Ítem ${item.numero}`}
                                                        className="h-28 w-full object-cover"
                                                    />
                                                ) : (
                                                    <div className="flex h-28 items-center justify-center text-muted-foreground">
                                                        <ImageIcon className="size-7 opacity-40" />
                                                    </div>
                                                )}
                                            </button>

                                            <div className="space-y-2 p-3">
                                                <div className="flex items-start justify-between gap-2">
                                                    <div className="min-w-0">
                                                        <p className="text-sm font-semibold text-foreground">#{item.numero}</p>
                                                        <p className="text-xs text-muted-foreground">Orden {item.orden}</p>
                                                    </div>
                                                    <span
                                                        className={`${cajasBadgeClass} ${
                                                            item.tipo === 'V'
                                                                ? 'bg-cajas-border/10 text-cajas-border ring-cajas-border/20'
                                                                : 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                        }`}
                                                    >
                                                        {item.tipo_label}
                                                    </span>
                                                </div>
                                                {item.nota && (
                                                    <p className="line-clamp-2 text-xs text-muted-foreground">{item.nota}</p>
                                                )}
                                                <div className="flex flex-wrap gap-1">
                                                    <button
                                                        type="button"
                                                        className={cajasActionLinkClass}
                                                        disabled={busy}
                                                        onClick={() => move(item.numero, 'arriba')}
                                                        title="Subir"
                                                    >
                                                        <ArrowUp className="size-3.5" />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className={cajasActionLinkClass}
                                                        disabled={busy}
                                                        onClick={() => move(item.numero, 'abajo')}
                                                        title="Bajar"
                                                    >
                                                        <ArrowDown className="size-3.5" />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className={cajasActionDangerClass}
                                                        disabled={busy}
                                                        onClick={() => handleDelete(item)}
                                                    >
                                                        <Trash2 className="size-3.5" />
                                                    </button>
                                                </div>
                                            </div>
                                        </article>
                                    );
                                })}
                            </div>
                        )}
                    </div>
                </section>

                <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                    <DialogContent className="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Agregar contenido</DialogTitle>
                            <DialogDescription>Sube una foto (JPG/PNG) o video (MP4) para la galería.</DialogDescription>
                        </DialogHeader>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                            {archivoPreview && (
                                <div>
                                    <label className={cajasFormLabelClass}>Vista previa</label>
                                    {form.tipo === 'V' ? (
                                        <video
                                            src={archivoPreview}
                                            controls
                                            className="mt-1 max-h-40 w-full rounded-md border border-border object-contain"
                                        />
                                    ) : (
                                        <img
                                            src={archivoPreview}
                                            alt="Vista previa"
                                            className="mt-1 max-h-40 w-full rounded-md border border-border object-contain"
                                        />
                                    )}
                                </div>
                            )}

                            <div>
                                <label htmlFor="archivo" className={cajasFormLabelClass}>
                                    Archivo *
                                </label>
                                <input
                                    id="archivo"
                                    name="archivo"
                                    type="file"
                                    required
                                    accept={form.tipo === 'V' ? 'video/mp4' : 'image/jpeg,image/png,image/jpg'}
                                    className={`${cajasFormInputClass} ${inputError('archivo')}`}
                                    onChange={handleFileChange}
                                />
                                <p className="mt-1 text-xs text-muted-foreground">
                                    {form.tipo === 'V' ? 'MP4 · máx. 20 MB' : 'JPG/PNG · máx. 5 MB'}
                                </p>
                                {formErrors.archivo && <p className={cajasFormErrorClass}>{formErrors.archivo}</p>}
                            </div>

                            <div>
                                <label htmlFor="tipo" className={cajasFormLabelClass}>
                                    Tipo *
                                </label>
                                <select
                                    id="tipo"
                                    name="tipo"
                                    required
                                    className={`${cajasFormSelectClass} ${inputError('tipo')}`}
                                    value={form.tipo}
                                    onChange={(e) => setForm((prev) => ({ ...prev, tipo: e.target.value }))}
                                >
                                    <option value="F">Foto</option>
                                    <option value="V">Video</option>
                                </select>
                                {formErrors.tipo && <p className={cajasFormErrorClass}>{formErrors.tipo}</p>}
                            </div>

                            <div>
                                <label htmlFor="nota" className={cajasFormLabelClass}>
                                    Nota
                                </label>
                                <textarea
                                    id="nota"
                                    name="nota"
                                    rows={3}
                                    maxLength={255}
                                    className={`${cajasFormTextareaClass} ${inputError('nota')}`}
                                    value={form.nota}
                                    onChange={(e) => setForm((prev) => ({ ...prev, nota: e.target.value }))}
                                    placeholder="Texto opcional"
                                />
                                {formErrors.nota && <p className={cajasFormErrorClass}>{formErrors.nota}</p>}
                            </div>

                            <DialogFooter>
                                <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                    Cancelar
                                </button>
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing || !archivo}>
                                    {processing ? 'Guardando...' : 'Guardar'}
                                </button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <Dialog open={previewOpen} onOpenChange={setPreviewOpen}>
                    <DialogContent className="sm:max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Vista previa #{previewItem?.numero}</DialogTitle>
                        </DialogHeader>
                        {previewItem?.archivo && (
                            previewItem.tipo === 'V' ? (
                                <video src={previewItem.archivo} controls className="max-h-[70vh] w-full rounded-md object-contain" />
                            ) : (
                                <img src={previewItem.archivo} alt={previewItem.nota || 'Galería'} className="max-h-[70vh] w-full rounded-md object-contain" />
                            )
                        )}
                        {previewItem?.nota && (
                            <p className="text-sm text-muted-foreground">{previewItem.nota}</p>
                        )}
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
