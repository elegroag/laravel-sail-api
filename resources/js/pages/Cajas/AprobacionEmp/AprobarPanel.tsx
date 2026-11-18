import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import ConfirmDialog from '@/pages/Cajas/Solicitudes/ConfirmDialog';
import type { FlashProps, SelectOption } from '@/pages/Cajas/Solicitudes/types';
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

type Controles = { camara_comercio: boolean; paz_y_salvo: boolean; aportes_empresa: boolean };

type FormState = {
    tipdur: string;
    codind: string;
    todmes: string;
    forpre: string;
    pymes: string;
    contratista: string;
    tipemp: string;
    tipapo: string;
    tipsoc: string;
    ofiafi: string;
    colegio: string;
    codsuc: string;
    actapr: string;
    diahab: string;
    fecafi: string;
    fecapr: string;
    nota_aprobar: string;
    validaciones_control: Controles;
};

export type AprobarFormulario = {
    options: Record<string, SelectOption[]>;
    defaults: FormState;
    catalogos_disponibles: boolean;
};

const SELECTS: { name: keyof FormState & string; label: string; search?: boolean }[] = [
    { name: 'tipdur', label: 'Duración' },
    { name: 'codind', label: 'Índice', search: true },
    { name: 'todmes', label: 'Paga mes' },
    { name: 'forpre', label: 'Forma de presentación' },
    { name: 'pymes', label: 'Pyme' },
    { name: 'contratista', label: 'Contratista' },
    { name: 'tipemp', label: 'Tipo de empresa' },
    { name: 'tipapo', label: 'Tipo de aportante', search: true },
    { name: 'tipsoc', label: 'Tipo de sociedad', search: true },
    { name: 'ofiafi', label: 'Oficina' },
    { name: 'colegio', label: 'Colegio' },
];

const CONTROLES: { key: keyof Controles; label: string }[] = [
    { key: 'camara_comercio', label: 'Validación de Cámara de Comercio' },
    { key: 'paz_y_salvo', label: 'Se valida paz y salvo' },
    { key: 'aportes_empresa', label: 'Validación de aportes de la empresa' },
];

const NOTA_MAX = 2000;

export default function AprobarPanel({ action, formulario }: { action: string; formulario: AprobarFormulario }) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const [form, setForm] = useState<FormState>(formulario.defaults);
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);

    const set = <K extends keyof FormState>(name: K, value: FormState[K]) => setForm((prev) => ({ ...prev, [name]: value }));
    const inputError = (name: string) => (errors[name] ? cajasInputErrorClass : '');
    const fieldError = (name: string) => errors[name] && <p className={cajasFormErrorClass}>{errors[name]}</p>;

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setConfirming(true);
    };

    const handleConfirm = () => {
        setProcessing(true);
        router.post(
            action,
            { ...form, codsuc: form.codsuc.trim(), actapr: form.actapr.trim(), nota_aprobar: form.nota_aprobar.trim() },
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
        <form onSubmit={handleSubmit} className="space-y-5" noValidate autoComplete="off">
            <div>
                <h3 className="text-foreground text-sm font-semibold">Aprobar</h3>
                <p className="text-muted-foreground text-sm">Aprueba la empresa y envía los datos a Subsidio (SISU).</p>
            </div>

            {!formulario.catalogos_disponibles && (
                <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                    No se pudieron cargar los parámetros de SISU; las listas pueden estar vacías. Recargue la página antes de aprobar.
                </p>
            )}

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {SELECTS.map(({ name, label, search }) => (
                    <div key={name}>
                        <label htmlFor={`ap-${name}`} className={cajasFormLabelClass}>
                            {label}
                        </label>
                        {search ? (
                            <CajasSearchSelect
                                id={`ap-${name}`}
                                name={name}
                                value={String(form[name])}
                                options={formulario.options[name] ?? []}
                                onChange={(v) => set(name, v as FormState[typeof name])}
                            />
                        ) : (
                            <select
                                id={`ap-${name}`}
                                name={name}
                                className={`${cajasFormSelectClass} ${inputError(name)}`}
                                value={String(form[name])}
                                onChange={(e) => set(name, e.target.value as FormState[typeof name])}
                            >
                                <option value="">Seleccione…</option>
                                {(formulario.options[name] ?? []).map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        )}
                        {fieldError(name)}
                    </div>
                ))}

                <div>
                    <label htmlFor="ap-codsuc" className={cajasFormLabelClass}>
                        Sucursal planilla
                    </label>
                    <input
                        id="ap-codsuc"
                        className={`${cajasFormInputClass} ${inputError('codsuc')}`}
                        value={form.codsuc}
                        maxLength={10}
                        onChange={(e) => set('codsuc', e.target.value)}
                    />
                    {fieldError('codsuc')}
                </div>
                <div>
                    <label htmlFor="ap-actapr" className={cajasFormLabelClass}>
                        Acta de aprobación
                    </label>
                    <input
                        id="ap-actapr"
                        className={`${cajasFormInputClass} ${inputError('actapr')}`}
                        value={form.actapr}
                        maxLength={50}
                        onChange={(e) => set('actapr', e.target.value)}
                    />
                    {fieldError('actapr')}
                </div>
                <div>
                    <label htmlFor="ap-diahab" className={cajasFormLabelClass}>
                        Día hábil de pago
                    </label>
                    <input
                        id="ap-diahab"
                        type="number"
                        min={0}
                        max={31}
                        className={`${cajasFormInputClass} ${inputError('diahab')}`}
                        value={form.diahab}
                        onChange={(e) => set('diahab', e.target.value)}
                    />
                    {fieldError('diahab')}
                </div>
                <div>
                    <label htmlFor="ap-fecafi" className={cajasFormLabelClass}>
                        Fecha de afiliación
                    </label>
                    <CajasDateField
                        id="ap-fecafi"
                        name="fecafi"
                        value={form.fecafi}
                        onChange={(v) => set('fecafi', v)}
                        options={{ maxDate: 'today' }}
                    />
                    {fieldError('fecafi')}
                </div>
                <div>
                    <label htmlFor="ap-fecapr" className={cajasFormLabelClass}>
                        Fecha aprobación resolución
                    </label>
                    <CajasDateField
                        id="ap-fecapr"
                        name="fecapr"
                        value={form.fecapr}
                        onChange={(v) => set('fecapr', v)}
                        options={{ maxDate: 'today' }}
                    />
                    {fieldError('fecapr')}
                </div>
            </div>

            <fieldset className="border-border rounded-lg border p-4">
                <legend className="text-foreground px-1 text-sm font-semibold">Validaciones de control (auditoría)</legend>
                <p className="text-muted-foreground mb-3 text-sm">
                    Deje registro de las consultas realizadas sobre Cámara de Comercio, paz y salvo y aportes de la empresa. Todas son obligatorias.
                </p>
                <div className="space-y-2">
                    {CONTROLES.map(({ key, label }) => (
                        <label key={key} className="text-foreground flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                className={cajasCheckboxClass}
                                checked={form.validaciones_control[key]}
                                onChange={(e) => set('validaciones_control', { ...form.validaciones_control, [key]: e.target.checked })}
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
                    maxLength={NOTA_MAX}
                    className={`${cajasFormTextareaClass} ${inputError('nota_aprobar')}`}
                    value={form.nota_aprobar}
                    onChange={(e) => set('nota_aprobar', e.target.value)}
                />
                {fieldError('nota_aprobar')}
            </div>

            <div className="flex justify-end">
                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                    {processing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                    Aprobar empresa
                </button>
            </div>

            <ConfirmDialog
                open={confirming}
                title="Aprobar empresa"
                description="Se registrará la empresa en SISU y se notificará al solicitante."
                confirmLabel="Aprobar"
                processing={processing}
                onConfirm={handleConfirm}
                onCancel={() => setConfirming(false)}
            />
        </form>
    );
}
