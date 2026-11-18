import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
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
    cajasInputErrorClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { ArrowLeft, KeyRound, Loader2, Save, Shuffle } from 'lucide-react';
import { useEffect, useState, type FormEvent } from 'react';

type Usuario = {
    documento: string;
    tipo: string;
    coddoc: string;
    nombre: string;
    email: string;
    estado: string;
    estado_detalle: string;
    tipo_detalle: string;
    coddoc_detalle: string;
    whatsapp: string;
    codciu: string;
    codciu_detalle: string;
    autoriza: string;
    fecreg: string | null;
    feccla: string | null;
    fecha_syncron: string | null;
};

type Options = {
    coddoc: Record<string, string>;
    tipo: Record<string, string>;
    estado: Record<string, string>;
    codciu: Record<string, string>;
};

type Props = {
    usuario: Usuario;
    options: Options;
};

type FormState = {
    nombre: string;
    email: string;
    whatsapp: string;
    codciu: string;
    coddoc: string;
    estado: string;
    autoriza: string;
    cambiar_clave: boolean;
    newclave: string;
    newclave_confirmation: string;
};

function estadoBadgeClass(estado: string): string {
    if (estado === 'A') return `${cajasBadgeClass} bg-emerald-100 text-emerald-800`;
    if (estado === 'I') return `${cajasBadgeClass} bg-amber-100 text-amber-800`;
    if (estado === 'B') return `${cajasBadgeClass} bg-red-100 text-red-800`;
    return `${cajasBadgeClass} bg-muted text-muted-foreground`;
}

function formFromUsuario(usuario: Usuario): FormState {
    return {
        nombre: usuario.nombre ?? '',
        email: usuario.email ?? '',
        whatsapp: usuario.whatsapp ?? '',
        codciu: usuario.codciu ?? '',
        coddoc: usuario.coddoc ?? '',
        estado: usuario.estado ?? '',
        autoriza: usuario.autoriza === 'N' ? 'N' : 'S',
        cambiar_clave: false,
        newclave: '',
        newclave_confirmation: '',
    };
}

function generarClave(length = 10): string {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    let out = '';
    const array = new Uint32Array(length);
    crypto.getRandomValues(array);
    for (let i = 0; i < length; i++) {
        out += chars[array[i] % chars.length];
    }
    return out;
}

export default function Edit({ usuario, options }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [form, setForm] = useState<FormState>(() => formFromUsuario(usuario));
    const [processing, setProcessing] = useState(false);
    const [navigating, setNavigating] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        setForm(formFromUsuario(usuario));
    }, [usuario]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setForm((prev) => ({
                ...prev,
                cambiar_clave: false,
                newclave: '',
                newclave_confirmation: '',
            }));
        }
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 4000);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const inputError = (field: string) => (formErrors[field] ? cajasInputErrorClass : '');

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        if (type === 'checkbox' && e.target instanceof HTMLInputElement) {
            setForm((prev) => ({
                ...prev,
                [name]: e.target.checked,
                ...(name === 'cambiar_clave' && !e.target.checked
                    ? { newclave: '', newclave_confirmation: '' }
                    : {}),
            }));
            return;
        }
        setForm((prev) => ({ ...prev, [name]: value }));
    };

    const handleGenerarClave = () => {
        const clave = generarClave();
        setForm((prev) => ({
            ...prev,
            cambiar_clave: true,
            newclave: clave,
            newclave_confirmation: clave,
        }));
    };

    const showUrl = `/cajas/usuario/${encodeURIComponent(usuario.documento)}/${encodeURIComponent(usuario.tipo)}/${encodeURIComponent(usuario.coddoc)}`;

    const handleBack = () => {
        router.get(
            showUrl,
            {},
            {
                onStart: () => setNavigating(true),
                onFinish: () => setNavigating(false),
                onCancel: () => setNavigating(false),
                onError: () => setNavigating(false),
            },
        );
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        router.put(
            `/cajas/usuario/${encodeURIComponent(usuario.documento)}/${encodeURIComponent(usuario.tipo)}/${encodeURIComponent(usuario.coddoc)}`,
            {
                nombre: form.nombre.trim(),
                email: form.email.trim(),
                whatsapp: form.whatsapp.trim(),
                codciu: form.codciu,
                coddoc: form.coddoc,
                estado: form.estado,
                autoriza: form.autoriza,
                cambiar_clave: form.cambiar_clave,
                newclave: form.cambiar_clave ? form.newclave : '',
                newclave_confirmation: form.cambiar_clave ? form.newclave_confirmation : '',
            },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    const busy = processing || navigating;

    return (
        <AppLayout title={`Editar ${usuario.documento}`}>
            <LoadingOverlay show={busy} message={processing ? 'Guardando cambios…' : 'Cargando…'} />
            <div className={`${cajasPageClass} ${busy ? 'pointer-events-none select-none' : ''}`}>
                {toast && <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}
                {formErrors.form && (
                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                        {formErrors.form}
                    </div>
                )}

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-base font-semibold text-foreground">Editar usuario</h1>
                        <p className="mt-1 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                            <span>
                                {usuario.tipo_detalle || usuario.tipo} · {usuario.documento}
                            </span>
                            <span className={estadoBadgeClass(usuario.estado)}>{usuario.estado_detalle || usuario.estado}</span>
                        </p>
                    </div>
                    <button type="button" className={cajasFormBtnSecondary} onClick={handleBack} disabled={busy}>
                        {navigating ? (
                            <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />
                        ) : (
                            <ArrowLeft className="mr-1.5 h-4 w-4" />
                        )}
                        {navigating ? 'Cargando…' : 'Volver al detalle'}
                    </button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                    <div className={cajasCardClass}>
                        <div className={cajasCardHeaderClass}>
                            <h2 className="text-sm font-semibold text-foreground">Datos del usuario</h2>
                        </div>
                        <div className={`${cajasCardBodyClass} grid grid-cols-1 gap-4 md:grid-cols-2`}>
                            <div>
                                <label className={cajasFormLabelClass}>Documento</label>
                                <input className={cajasFormInputClass} value={usuario.documento} disabled readOnly />
                            </div>
                            <div>
                                <label className={cajasFormLabelClass}>Tipo usuario</label>
                                <input
                                    className={cajasFormInputClass}
                                    value={usuario.tipo_detalle || options.tipo[usuario.tipo] || usuario.tipo}
                                    disabled
                                    readOnly
                                />
                            </div>
                            <div>
                                <label htmlFor="coddoc" className={cajasFormLabelClass}>
                                    Tipo documento
                                </label>
                                <select
                                    id="coddoc"
                                    name="coddoc"
                                    className={`${cajasFormSelectClass} ${inputError('coddoc')}`}
                                    value={form.coddoc}
                                    onChange={handleChange}
                                    disabled={busy}
                                >
                                    {Object.entries(options.coddoc).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.coddoc && <p className={cajasFormErrorClass}>{formErrors.coddoc}</p>}
                            </div>
                            <div>
                                <label htmlFor="nombre" className={cajasFormLabelClass}>
                                    Nombre
                                </label>
                                <input
                                    id="nombre"
                                    name="nombre"
                                    className={`${cajasFormInputClass} ${inputError('nombre')}`}
                                    value={form.nombre}
                                    onChange={handleChange}
                                    disabled={busy}
                                    required
                                />
                                {formErrors.nombre && <p className={cajasFormErrorClass}>{formErrors.nombre}</p>}
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
                                    value={form.email}
                                    onChange={handleChange}
                                    disabled={busy}
                                    required
                                />
                                {formErrors.email && <p className={cajasFormErrorClass}>{formErrors.email}</p>}
                            </div>
                            <div>
                                <label htmlFor="whatsapp" className={cajasFormLabelClass}>
                                    WhatsApp
                                </label>
                                <input
                                    id="whatsapp"
                                    name="whatsapp"
                                    className={`${cajasFormInputClass} ${inputError('whatsapp')}`}
                                    value={form.whatsapp}
                                    onChange={handleChange}
                                    disabled={busy}
                                />
                                {formErrors.whatsapp && <p className={cajasFormErrorClass}>{formErrors.whatsapp}</p>}
                            </div>
                            <div>
                                <label htmlFor="codciu" className={cajasFormLabelClass}>
                                    Ciudad
                                </label>
                                <select
                                    id="codciu"
                                    name="codciu"
                                    className={`${cajasFormSelectClass} ${inputError('codciu')}`}
                                    value={form.codciu}
                                    onChange={handleChange}
                                    disabled={busy}
                                    required
                                >
                                    <option value="">Seleccione…</option>
                                    {Object.entries(options.codciu).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.codciu && <p className={cajasFormErrorClass}>{formErrors.codciu}</p>}
                            </div>
                            <div>
                                <label htmlFor="estado" className={cajasFormLabelClass}>
                                    Estado
                                </label>
                                <select
                                    id="estado"
                                    name="estado"
                                    className={`${cajasFormSelectClass} ${inputError('estado')}`}
                                    value={form.estado}
                                    onChange={handleChange}
                                    disabled={busy}
                                    required
                                >
                                    {Object.entries(options.estado).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.estado && <p className={cajasFormErrorClass}>{formErrors.estado}</p>}
                            </div>
                            <div>
                                <label htmlFor="autoriza" className={cajasFormLabelClass}>
                                    Autoriza
                                </label>
                                <select
                                    id="autoriza"
                                    name="autoriza"
                                    className={`${cajasFormSelectClass} ${inputError('autoriza')}`}
                                    value={form.autoriza}
                                    onChange={handleChange}
                                    disabled={busy}
                                >
                                    <option value="S">Sí</option>
                                    <option value="N">No</option>
                                </select>
                                {formErrors.autoriza && <p className={cajasFormErrorClass}>{formErrors.autoriza}</p>}
                            </div>
                        </div>
                    </div>

                    <div className={cajasCardClass}>
                        <div className={`${cajasCardHeaderClass} flex flex-wrap items-center justify-between gap-2`}>
                            <div className="flex items-center gap-2">
                                <KeyRound className="size-4 text-muted-foreground" />
                                <h2 className="text-sm font-semibold text-foreground">Cambiar clave</h2>
                            </div>
                            <label className="inline-flex items-center gap-2 text-sm text-foreground">
                                <input
                                    type="checkbox"
                                    name="cambiar_clave"
                                    checked={form.cambiar_clave}
                                    onChange={handleChange}
                                    disabled={busy}
                                    className="rounded border-border"
                                />
                                Activar cambio de clave
                            </label>
                        </div>
                        <div className={`${cajasCardBodyClass} space-y-4`}>
                            <p className="text-sm text-muted-foreground">
                                Si cambia la clave, se actualizará el acceso y se enviará un correo al usuario con las nuevas
                                credenciales.
                            </p>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label htmlFor="newclave" className={cajasFormLabelClass}>
                                        Nueva clave
                                    </label>
                                    <input
                                        id="newclave"
                                        name="newclave"
                                        type="text"
                                        className={`${cajasFormInputClass} ${inputError('newclave')}`}
                                        value={form.newclave}
                                        onChange={handleChange}
                                        disabled={busy || !form.cambiar_clave}
                                        autoComplete="new-password"
                                    />
                                    {formErrors.newclave && <p className={cajasFormErrorClass}>{formErrors.newclave}</p>}
                                </div>
                                <div>
                                    <label htmlFor="newclave_confirmation" className={cajasFormLabelClass}>
                                        Confirmar clave
                                    </label>
                                    <input
                                        id="newclave_confirmation"
                                        name="newclave_confirmation"
                                        type="text"
                                        className={`${cajasFormInputClass} ${inputError('newclave_confirmation')}`}
                                        value={form.newclave_confirmation}
                                        onChange={handleChange}
                                        disabled={busy || !form.cambiar_clave}
                                        autoComplete="new-password"
                                    />
                                </div>
                            </div>
                            <button
                                type="button"
                                className={cajasFormBtnSecondary}
                                onClick={handleGenerarClave}
                                disabled={busy}
                            >
                                <Shuffle className="mr-1.5 size-4" />
                                Generar clave aleatoria
                            </button>
                            <div className="grid grid-cols-1 gap-2 text-xs text-muted-foreground sm:grid-cols-3">
                                <span>Registro: {usuario.fecreg || '—'}</span>
                                <span>Fecha clave: {usuario.feccla || '—'}</span>
                                <span>Sincronización: {usuario.fecha_syncron || '—'}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center justify-end gap-2">
                        <button type="button" className={cajasFormBtnSecondary} onClick={handleBack} disabled={busy}>
                            Cancelar
                        </button>
                        <button type="submit" className={cajasFormBtnPrimary} disabled={busy}>
                            {processing ? <Loader2 className="mr-1.5 size-4 animate-spin" /> : <Save className="mr-1.5 size-4" />}
                            {processing ? 'Guardando…' : 'Guardar cambios'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
