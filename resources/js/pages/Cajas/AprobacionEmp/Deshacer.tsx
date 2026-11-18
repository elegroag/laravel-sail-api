import AppLayout from '@/layouts/AppLayout';
import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { FlashProps, Motivo, SolicitudResumen } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Loader2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

type Props = {
    solicitud: SolicitudResumen;
    motivos: Motivo[];
};

const ACCIONES = [
    { value: 'D', label: 'Devolver' },
    { value: 'R', label: 'Rechazar' },
    { value: 'I', label: 'Inactivar' },
];

export default function Deshacer({ solicitud, motivos }: Props) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const [action, setAction] = useState('');
    const [codest, setCodest] = useState('');
    const [nota, setNota] = useState('');
    const [sendEmail, setSendEmail] = useState(false);
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    const requiereMotivo = action === 'D' || action === 'R';

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (!action || nota.trim() === '' || (requiereMotivo && !codest)) {
            setLocalError('Complete la acción, el motivo (si aplica) y la nota para continuar.');
            return;
        }
        setLocalError(null);
        setConfirming(true);
    };

    const handleConfirm = () => {
        setProcessing(true);
        router.post(
            `${solicitud.urls.show}/deshacer`,
            { action, codest: requiereMotivo ? codest : null, nota: nota.trim(), send_email: requiereMotivo && sendEmail },
            {
                preserveScroll: true,
                onFinish: () => {
                    setProcessing(false);
                    setConfirming(false);
                },
            },
        );
    };

    return (
        <AppLayout title={`Deshacer aprobación ${solicitud.nit}`}>
            <div className={cajasPageClass}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">Deshacer aprobación</h1>
                        <p className="text-muted-foreground mt-1 flex flex-wrap items-center gap-2 text-sm">
                            <span>
                                {solicitud.razsoc} · NIT {solicitud.nit}
                            </span>
                            <EstadoBadge estado={solicitud.estado} label={solicitud.estado_detalle} />
                        </p>
                    </div>
                    <Link href={solicitud.urls.show} className={cajasFormBtnSecondary}>
                        <ArrowLeft className="mr-1.5 size-4" />
                        Volver al detalle
                    </Link>
                </div>

                <section className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-foreground text-sm font-semibold">Revertir la afiliación en SISU</h2>
                    </div>
                    <form onSubmit={handleSubmit} className={`${cajasCardBodyClass} space-y-4`} noValidate>
                        <p className="text-muted-foreground text-sm">
                            Elimina la afiliación de la empresa en SISU y actualiza el estado de la solicitud en Comfaca en línea.
                        </p>
                        {localError && <p className={cajasFormErrorClass}>{localError}</p>}

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label htmlFor="dh-action" className={cajasFormLabelClass}>
                                    Acción en Comfaca en línea
                                </label>
                                <select
                                    id="dh-action"
                                    className={`${cajasFormSelectClass} ${errors.action ? cajasInputErrorClass : ''}`}
                                    value={action}
                                    onChange={(e) => setAction(e.target.value)}
                                >
                                    <option value="">Seleccione una acción</option>
                                    {ACCIONES.map((a) => (
                                        <option key={a.value} value={a.value}>
                                            {a.label}
                                        </option>
                                    ))}
                                </select>
                                {errors.action && <p className={cajasFormErrorClass}>{errors.action}</p>}
                            </div>
                            <div>
                                <label htmlFor="dh-codest" className={cajasFormLabelClass}>
                                    Motivo de la acción
                                </label>
                                <select
                                    id="dh-codest"
                                    className={`${cajasFormSelectClass} ${errors.codest ? cajasInputErrorClass : ''}`}
                                    value={codest}
                                    onChange={(e) => setCodest(e.target.value)}
                                    disabled={!requiereMotivo}
                                >
                                    <option value="">Seleccione un motivo</option>
                                    {motivos.map((m) => (
                                        <option key={m.codest} value={m.codest}>
                                            {m.detalle}
                                        </option>
                                    ))}
                                </select>
                                {errors.codest && <p className={cajasFormErrorClass}>{errors.codest}</p>}
                            </div>
                        </div>

                        <label className="text-foreground inline-flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                className="border-border rounded"
                                checked={sendEmail}
                                disabled={!requiereMotivo}
                                onChange={(e) => setSendEmail(e.target.checked)}
                            />
                            Enviar correo al solicitante
                        </label>

                        <div>
                            <label htmlFor="dh-nota" className={cajasFormLabelClass}>
                                Nota de seguimiento y trazabilidad
                            </label>
                            <textarea
                                id="dh-nota"
                                rows={4}
                                maxLength={800}
                                className={`${cajasFormTextareaClass} ${errors.nota ? cajasInputErrorClass : ''}`}
                                value={nota}
                                onChange={(e) => setNota(e.target.value)}
                            />
                            {errors.nota && <p className={cajasFormErrorClass}>{errors.nota}</p>}
                        </div>

                        <div className="flex justify-end">
                            <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                {processing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                                Procesar
                            </button>
                        </div>
                    </form>
                </section>

                <ConfirmDialog
                    open={confirming}
                    title="Deshacer aprobación"
                    description="Se eliminará la afiliación de la empresa en SISU. Verifique la acción y la nota antes de continuar."
                    confirmLabel="Deshacer"
                    processing={processing}
                    onConfirm={handleConfirm}
                    onCancel={() => setConfirming(false)}
                />
            </div>
        </AppLayout>
    );
}
