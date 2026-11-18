import AppLayout from '@/layouts/AppLayout';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { usePage } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import { useEffect, useState, type FormEvent } from 'react';

type Option = { value: string; label: string };

type Props = {
    title: string;
    tipopcOptions: Option[];
    pdfUrl: string;
    flashError?: string | null;
};

function resolveFlashError(value: unknown): string | null {
    if (!value) return null;
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null && 'msj' in value) {
        const msj = (value as { msj?: unknown }).msj;
        return typeof msj === 'string' && msj !== '' ? msj : null;
    }
    return null;
}

export default function Index({ title, tipopcOptions, pdfUrl, flashError }: Props) {
    const page = usePage<{ flash?: { error?: unknown } }>();
    const [ruuid, setRuuid] = useState('');
    const [tipopc, setTipopc] = useState('');
    const [localError, setLocalError] = useState<string | null>(null);
    const [toast, setToast] = useState<string | null>(
        flashError || resolveFlashError(page.props.flash?.error),
    );

    useEffect(() => {
        const msg = flashError || resolveFlashError(page.props.flash?.error);
        if (msg) {
            setToast(msg);
        }
    }, [flashError, page.props.flash?.error]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 5000);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);

        const ruuidValue = ruuid.trim();
        if (!ruuidValue) {
            setLocalError('El RUUID de la solicitud es obligatorio.');
            return;
        }
        if (ruuidValue.length < 8) {
            setLocalError('El RUUID debe tener al menos 8 caracteres.');
            return;
        }
        if (!tipopc) {
            setLocalError('El tipo de afiliación es obligatorio.');
            return;
        }

        const qs = new URLSearchParams({
            ruuid: ruuidValue,
            tipopc,
        });
        window.open(`${pdfUrl}?${qs.toString()}`, '_blank', 'noopener,noreferrer');
    };

    const displayError = localError;

    return (
        <AppLayout title={title}>
            <div className={cajasPageClass}>
                {toast && (
                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                        {toast}
                    </div>
                )}

                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <FileText className="size-5 text-cajas-border" />
                        {title}
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Genere el PDF con los datos de la solicitud y su trazabilidad de eventos
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    Ingrese el <strong>RUUID</strong> de la solicitud y el <strong>tipo de afiliación</strong>. El informe
                    incluye los datos registrados y el historial desde el envío hasta la aprobación.
                </div>

                <section className={`${cajasCardClass} max-w-3xl`}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Parámetros del informe</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-5">
                                <div className="md:col-span-3">
                                    <label htmlFor="ruuid" className={cajasFormLabelClass}>
                                        RUUID
                                    </label>
                                    <input
                                        id="ruuid"
                                        type="text"
                                        className={cajasFormInputClass}
                                        maxLength={40}
                                        placeholder="Ej. radicado de la solicitud"
                                        value={ruuid}
                                        onChange={(e) => setRuuid(e.target.value)}
                                    />
                                </div>
                                <div className="md:col-span-2">
                                    <label htmlFor="tipopc" className={cajasFormLabelClass}>
                                        Tipo de afiliación
                                    </label>
                                    <select
                                        id="tipopc"
                                        className={cajasFormSelectClass}
                                        value={tipopc}
                                        onChange={(e) => setTipopc(e.target.value)}
                                    >
                                        <option value="">Seleccione</option>
                                        {tipopcOptions.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            {displayError && <p className={cajasFormErrorClass}>{displayError}</p>}

                            <div className="flex justify-center pt-2">
                                <button type="submit" className={cajasFormBtnPrimary}>
                                    <FileText className="mr-1.5 size-4" />
                                    Descargar PDF
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
