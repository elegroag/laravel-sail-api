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
    cajasInputErrorClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { ArrowDown, ArrowUp, ImageIcon, Pencil, Plus, Trash2 } from 'lucide-react';
import { useEffect, useState, type ChangeEvent, type FormEvent } from 'react';

type GaleriaItem = {
    numero: number;
    archivo: string;
    archivo_nombre: string | null;
    url: string;
    orden: number;
};

type Props = {
    items: GaleriaItem[];
};

export default function Index({ items }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [dialogOpen, setDialogOpen] = useState(false);
    const [previewOpen, setPreviewOpen] = useState(false);
    const [previewItem, setPreviewItem] = useState<GaleriaItem | null>(null);
    const [editingNumero, setEditingNumero] = useState<number | null>(null);
    const [archivo, setArchivo] = useState<File | null>(null);
    const [archivoPreview, setArchivoPreview] = useState<string | null>(null);
    const [currentImage, setCurrentImage] = useState<string | null>(null);
    const [processing, setProcessing] = useState(false);
    const [busyNumero, setBusyNumero] = useState<number | null>(null);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setDialogOpen(false);
            setEditingNumero(null);
            setArchivo(null);
            setCurrentImage(null);
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
        setEditingNumero(null);
        setArchivo(null);
        if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        setArchivoPreview(null);
        setCurrentImage(null);
        setDialogOpen(true);
    };

    const openEdit = (item: GaleriaItem) => {
        setEditingNumero(item.numero);
        setArchivo(null);
        if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        setArchivoPreview(null);
        setCurrentImage(item.archivo || null);
        setDialogOpen(true);
    };

    const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;
        if (archivoPreview) URL.revokeObjectURL(archivoPreview);
        setArchivo(file);
        setArchivoPreview(file ? URL.createObjectURL(file) : null);
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (!editingNumero && !archivo) return;

        setProcessing(true);

        const payload: Record<string, File> = {};
        if (archivo) {
            payload.archivo = archivo;
        }

        const options = {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        };

        if (editingNumero) {
            router.post(`/cajas/mercurio53/${editingNumero}`, payload, options);
        } else {
            router.post('/cajas/mercurio53', payload, options);
        }
    };

    const move = (numero: number, direction: 'arriba' | 'abajo') => {
        setBusyNumero(numero);
        router.post(`/cajas/mercurio53/${numero}/${direction}`, {}, {
            preserveScroll: true,
            onFinish: () => setBusyNumero(null),
        });
    };

    const handleDelete = (item: GaleriaItem) => {
        if (!confirm(`¿Eliminar la imagen destacada #${item.numero}?`)) return;

        setBusyNumero(item.numero);
        router.delete(`/cajas/mercurio53/${item.numero}`, {
            preserveScroll: true,
            onFinish: () => setBusyNumero(null),
        });
    };

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');
    const previewSrc = archivoPreview || currentImage;

    return (
        <AppLayout title="Destacadas">
            <div className={cajasPageClass}>
                {toast && <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}
                {formErrors.form && !dialogOpen && (
                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                        {formErrors.form}
                    </div>
                )}

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between`}>
                        <div>
                            <h1 className="text-sm font-semibold text-foreground">Imágenes destacadas</h1>
                            <p className="text-xs text-muted-foreground">
                                {items.length} ítem{items.length === 1 ? '' : 's'} · destacadas del canal móvil
                            </p>
                        </div>
                        <button type="button" onClick={openCreate} className={cajasFormBtnPrimary}>
                            <Plus className="size-4" />
                            Agregar imagen
                        </button>
                    </div>

                    <div className={cajasCardBodyClass}>
                        {items.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 text-center">
                                <ImageIcon className="mb-3 size-10 text-muted-foreground/50" />
                                <p className="text-sm text-muted-foreground">No hay imágenes destacadas.</p>
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
                                                {item.archivo ? (
                                                    <img
                                                        src={item.archivo}
                                                        alt={`Destacada ${item.numero}`}
                                                        className="h-28 w-full object-cover"
                                                    />
                                                ) : (
                                                    <div className="flex h-28 items-center justify-center text-muted-foreground">
                                                        <ImageIcon className="size-7 opacity-40" />
                                                    </div>
                                                )}
                                            </button>

                                            <div className="space-y-2 p-3">
                                                <div>
                                                    <p className="text-sm font-semibold text-foreground">#{item.numero}</p>
                                                    <p className="text-xs text-muted-foreground">Orden {item.orden}</p>
                                                </div>
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
                                                        className={cajasActionLinkClass}
                                                        disabled={busy}
                                                        onClick={() => openEdit(item)}
                                                        title="Editar"
                                                    >
                                                        <Pencil className="size-3.5" />
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
                            <DialogTitle>{editingNumero ? 'Editar imagen destacada' : 'Agregar imagen destacada'}</DialogTitle>
                            <DialogDescription>Imagen JPG/PNG para destacadas del móvil.</DialogDescription>
                        </DialogHeader>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {formErrors.form && <p className={cajasFormErrorClass}>{formErrors.form}</p>}

                            {previewSrc && (
                                <div>
                                    <label className={cajasFormLabelClass}>Vista previa</label>
                                    <img
                                        src={previewSrc}
                                        alt="Vista previa"
                                        className="mt-1 max-h-40 w-full rounded-md border border-border object-contain"
                                    />
                                </div>
                            )}

                            <div>
                                <label htmlFor="archivo" className={cajasFormLabelClass}>
                                    Imagen {editingNumero ? '' : '*'}
                                </label>
                                <input
                                    id="archivo"
                                    name="archivo"
                                    type="file"
                                    required={!editingNumero}
                                    accept="image/jpeg,image/png,image/jpg"
                                    className={`${cajasFormInputClass} ${inputError('archivo')}`}
                                    onChange={handleFileChange}
                                />
                                <p className="mt-1 text-xs text-muted-foreground">JPG/PNG · máx. 5 MB</p>
                                {formErrors.archivo && <p className={cajasFormErrorClass}>{formErrors.archivo}</p>}
                            </div>

                            <DialogFooter>
                                <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    className={cajasFormBtnPrimary}
                                    disabled={processing || (!editingNumero && !archivo)}
                                >
                                    {processing ? 'Guardando…' : 'Guardar'}
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
                            <img
                                src={previewItem.archivo}
                                alt={`Destacada ${previewItem.numero}`}
                                className="max-h-[70vh] w-full rounded-md object-contain"
                            />
                        )}
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
