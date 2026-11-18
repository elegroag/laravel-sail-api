import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
import type { FlashProps } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

const NOTA_MAX = 800;

type Props = {
    action: string;
    title: string;
    description: string;
    submitLabel: string;
    confirmTitle: string;
    confirmDescription: string;
};

/** Panel compartido con solo nota de trazabilidad (p. ej. re-aprobar). */
export default function NotaPanel({ action, title, description, submitLabel, confirmTitle, confirmDescription }: Props) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const [nota, setNota] = useState('');
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (nota.trim() === '') {
            setLocalError('Escriba la nota de seguimiento para continuar.');
            return;
        }
        setLocalError(null);
        setConfirming(true);
    };

    const handleConfirm = () => {
        setProcessing(true);
        router.post(
            action,
            { nota: nota.trim() },
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
        <form onSubmit={handleSubmit} className="space-y-4" noValidate>
            <div>
                <h3 className="text-foreground text-sm font-semibold">{title}</h3>
                <p className="text-muted-foreground text-sm">{description}</p>
            </div>
            {localError && <p className={cajasFormErrorClass}>{localError}</p>}
            <div>
                <label htmlFor={`${action}-nota`} className={cajasFormLabelClass}>
                    Nota de seguimiento
                </label>
                <textarea
                    id={`${action}-nota`}
                    rows={4}
                    maxLength={NOTA_MAX}
                    className={`${cajasFormTextareaClass} ${errors.nota ? cajasInputErrorClass : ''}`}
                    value={nota}
                    onChange={(e) => setNota(e.target.value)}
                />
                {errors.nota && <p className={cajasFormErrorClass}>{errors.nota}</p>}
            </div>
            <div className="flex justify-end">
                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                    {processing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                    {submitLabel}
                </button>
            </div>
            <ConfirmDialog
                open={confirming}
                title={confirmTitle}
                description={confirmDescription}
                confirmLabel={submitLabel}
                processing={processing}
                onConfirm={handleConfirm}
                onCancel={() => setConfirming(false)}
            />
        </form>
    );
}
