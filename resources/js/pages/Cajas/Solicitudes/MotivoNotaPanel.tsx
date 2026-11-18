import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
import type { CampoCorregible, FlashProps, Motivo } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

const NOTA_MAX = 800;

type Props = {
    /** URL POST de la acción. */
    action: string;
    title: string;
    description: string;
    submitLabel: string;
    confirmTitle: string;
    confirmDescription: string;
    motivos: Motivo[];
    /** Si se envía, se muestra la selección de campos a corregir (devolución). */
    camposCorregibles?: CampoCorregible[];
};

/** Panel compartido para devolver/rechazar: motivo + nota (+ campos a corregir) con confirmación. */
export default function MotivoNotaPanel({
    action,
    title,
    description,
    submitLabel,
    confirmTitle,
    confirmDescription,
    motivos,
    camposCorregibles,
}: Props) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const [codest, setCodest] = useState('');
    const [nota, setNota] = useState('');
    const [campos, setCampos] = useState<string[]>([]);
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        if (!codest || nota.trim() === '') {
            setLocalError('Seleccione el motivo y escriba la nota para continuar.');
            return;
        }
        setLocalError(null);
        setConfirming(true);
    };

    const handleConfirm = () => {
        setProcessing(true);
        router.post(
            action,
            { codest, nota: nota.trim(), ...(camposCorregibles ? { campos_corregir: campos } : {}) },
            {
                preserveScroll: true,
                onFinish: () => {
                    setProcessing(false);
                    setConfirming(false);
                },
            },
        );
    };

    const toggleCampo = (coddoc: string) => setCampos((prev) => (prev.includes(coddoc) ? prev.filter((c) => c !== coddoc) : [...prev, coddoc]));

    return (
        <form onSubmit={handleSubmit} className="space-y-4" noValidate>
            <div>
                <h3 className="text-foreground text-sm font-semibold">{title}</h3>
                <p className="text-muted-foreground text-sm">{description}</p>
            </div>

            {localError && <p className={cajasFormErrorClass}>{localError}</p>}

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label htmlFor={`${action}-codest`} className={cajasFormLabelClass}>
                        Motivo
                    </label>
                    <select
                        id={`${action}-codest`}
                        className={`${cajasFormSelectClass} ${errors.codest ? cajasInputErrorClass : ''}`}
                        value={codest}
                        onChange={(e) => setCodest(e.target.value)}
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

                <div className="md:col-span-2">
                    <label htmlFor={`${action}-nota`} className={cajasFormLabelClass}>
                        Nota
                    </label>
                    <textarea
                        id={`${action}-nota`}
                        rows={4}
                        maxLength={NOTA_MAX}
                        className={`${cajasFormTextareaClass} ${errors.nota ? cajasInputErrorClass : ''}`}
                        value={nota}
                        onChange={(e) => setNota(e.target.value)}
                    />
                    <div className="flex justify-between">
                        {errors.nota ? <p className={cajasFormErrorClass}>{errors.nota}</p> : <span />}
                        <span className="text-muted-foreground text-xs tabular-nums">
                            {nota.length}/{NOTA_MAX}
                        </span>
                    </div>
                </div>
            </div>

            {camposCorregibles && camposCorregibles.length > 0 && (
                <fieldset>
                    <legend className={cajasFormLabelClass}>Campos y documentos a corregir</legend>
                    <div className="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        {camposCorregibles.map((c) => (
                            <label key={c.coddoc} className="text-foreground inline-flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    className="border-border rounded"
                                    checked={campos.includes(c.coddoc)}
                                    onChange={() => toggleCampo(c.coddoc)}
                                />
                                {c.detalle}
                            </label>
                        ))}
                    </div>
                </fieldset>
            )}

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
