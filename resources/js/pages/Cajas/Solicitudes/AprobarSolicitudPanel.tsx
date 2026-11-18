import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
import type { AprobarCampo, AprobarSchema, FlashProps } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasCheckboxClass,
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

type Props = {
    action: string;
    schema: AprobarSchema;
    singular: string;
};

type Valores = Record<string, string>;

const isVisible = (campo: AprobarCampo, valores: Valores) => !campo.show_if || campo.show_if.in.includes(valores[campo.show_if.field] ?? '');

export default function AprobarSolicitudPanel({ action, schema, singular }: Props) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const { validaciones_control: controlesInicial, nota_aprobar: notaInicial, ...camposInicial } = schema.defaults;

    const [valores, setValores] = useState<Valores>(camposInicial as Valores);
    const [controles, setControles] = useState<Record<string, boolean>>(controlesInicial as Record<string, boolean>);
    const [nota, setNota] = useState(String(notaInicial ?? ''));
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);

    const inputError = (name: string) => (errors[name] ? cajasInputErrorClass : '');
    const fieldError = (name: string) => errors[name] && <p className={cajasFormErrorClass}>{errors[name]}</p>;

    const set = (name: string, value: string) =>
        setValores((prev) => {
            const next: Valores = { ...prev, [name]: value, ...(schema.dependencias[name]?.[value] ?? {}) };
            schema.campos.forEach((campo) => {
                if (!isVisible(campo, next)) next[campo.name] = '';
            });
            return next;
        });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setConfirming(true);
    };

    const handleConfirm = () => {
        setProcessing(true);
        const payload: Record<string, string | Record<string, boolean>> = { validaciones_control: controles, nota_aprobar: nota.trim() };
        schema.campos.forEach((campo) => {
            payload[campo.name] = isVisible(campo, valores) ? (valores[campo.name] ?? '').trim() : '';
        });

        router.post(action, payload, {
            preserveScroll: true,
            onFinish: () => {
                setProcessing(false);
                setConfirming(false);
            },
        });
    };

    const renderCampo = (campo: AprobarCampo) => {
        const id = `ap-${campo.name}`;
        const value = valores[campo.name] ?? '';

        if (campo.type === 'select') {
            return campo.search ? (
                <CajasSearchSelect id={id} name={campo.name} value={value} options={campo.options ?? []} onChange={(v) => set(campo.name, v)} />
            ) : (
                <select
                    id={id}
                    name={campo.name}
                    className={`${cajasFormSelectClass} ${inputError(campo.name)}`}
                    value={value}
                    onChange={(e) => set(campo.name, e.target.value)}
                >
                    <option value="">Seleccione…</option>
                    {(campo.options ?? []).map((o) => (
                        <option key={o.value} value={o.value}>
                            {o.label}
                        </option>
                    ))}
                </select>
            );
        }

        if (campo.type === 'date') {
            return <CajasDateField id={id} name={campo.name} value={value} onChange={(v) => set(campo.name, v)} options={{ maxDate: 'today' }} />;
        }

        return (
            <input
                id={id}
                name={campo.name}
                type={campo.type === 'number' ? 'number' : 'text'}
                min={campo.type === 'number' ? (campo.min ?? 0) : undefined}
                max={campo.type === 'number' ? (campo.max ?? undefined) : undefined}
                maxLength={campo.type === 'text' ? (campo.max ?? undefined) : undefined}
                className={`${cajasFormInputClass} ${inputError(campo.name)}`}
                value={value}
                onChange={(e) => set(campo.name, e.target.value)}
            />
        );
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-5" noValidate autoComplete="off">
            <div>
                <h3 className="text-foreground text-sm font-semibold">Aprobar</h3>
                <p className="text-muted-foreground text-sm">Aprueba el {singular} y envía los datos a Subsidio (SISU).</p>
            </div>

            {!schema.catalogos_disponibles && (
                <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                    No se pudieron cargar los parámetros de SISU; las listas pueden estar vacías. Recargue la página antes de aprobar.
                </p>
            )}

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {schema.campos
                    .filter((campo) => isVisible(campo, valores))
                    .map((campo) => (
                        <div key={campo.name}>
                            <label htmlFor={`ap-${campo.name}`} className={cajasFormLabelClass}>
                                {campo.label}
                            </label>
                            {renderCampo(campo)}
                            {fieldError(campo.name)}
                        </div>
                    ))}
            </div>

            <fieldset className="border-border rounded-lg border p-4">
                <legend className="text-foreground px-1 text-sm font-semibold">Validaciones de control (auditoría)</legend>
                <p className="text-muted-foreground mb-3 text-sm">
                    Deje registro de las consultas realizadas, soporte requerido por entes de control. Todas son obligatorias.
                </p>
                <div className="space-y-2">
                    {schema.controles.map(({ key, label }) => (
                        <label key={key} className="text-foreground flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                className={cajasCheckboxClass}
                                checked={Boolean(controles[key])}
                                onChange={(e) => setControles((prev) => ({ ...prev, [key]: e.target.checked }))}
                            />
                            {label}
                        </label>
                    ))}
                </div>
                {fieldError('validaciones_control')}
            </fieldset>

            <div>
                <label htmlFor="ap-nota" className={cajasFormLabelClass}>
                    Nota
                </label>
                <textarea
                    id="ap-nota"
                    rows={3}
                    maxLength={schema.nota_max}
                    className={`${cajasFormTextareaClass} ${inputError('nota_aprobar')}`}
                    value={nota}
                    onChange={(e) => setNota(e.target.value)}
                />
                {fieldError('nota_aprobar')}
            </div>

            <div className="flex justify-end">
                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                    {processing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                    Aprobar solicitud
                </button>
            </div>

            <ConfirmDialog
                open={confirming}
                title="Aprobar solicitud"
                description={`Se registrará el ${singular} en SISU y se notificará al solicitante.`}
                confirmLabel="Aprobar"
                processing={processing}
                onConfirm={handleConfirm}
                onCancel={() => setConfirming(false)}
            />
        </form>
    );
}
