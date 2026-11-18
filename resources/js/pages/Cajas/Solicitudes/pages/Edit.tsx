import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { FlashProps, ModuloInfo, SolicitudResumen } from '@/pages/Cajas/Solicitudes/types';
import {
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
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Loader2, Save } from 'lucide-react';
import { useMemo, useState, type FormEvent } from 'react';

type Campo = {
    name: string;
    label: string;
    type: string;
    form_type: 'input' | 'select' | 'date';
    data_source: Record<string, string>;
    grupo: string;
    order: number;
};

type Props = {
    modulo: ModuloInfo;
    solicitud: SolicitudResumen;
    data: Record<string, string>;
    campos: Campo[];
};

export default function Edit({ modulo, solicitud, data, campos }: Props) {
    const errors = usePage<FlashProps>().props.errors ?? {};
    const [form, setForm] = useState<Record<string, string>>(data);
    const [processing, setProcessing] = useState(false);

    const grupos = useMemo(() => {
        const map = new Map<string, Campo[]>();
        campos.forEach((campo) => map.set(campo.grupo, [...(map.get(campo.grupo) ?? []), campo]));
        return Array.from(map.entries());
    }, [campos]);

    const set = (name: string, value: string) => setForm((prev) => ({ ...prev, [name]: value }));

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setProcessing(true);
        router.put(`${solicitud.urls.show}`, form, {
            preserveScroll: true,
            onFinish: () => setProcessing(false),
        });
    };

    const renderCampo = (campo: Campo) => {
        const id = `edit-${campo.name}`;
        const value = form[campo.name] ?? '';
        const hasError = Boolean(errors[campo.name]);

        if (campo.form_type === 'select') {
            return (
                <CajasSearchSelect
                    id={id}
                    name={campo.name}
                    value={value}
                    options={Object.entries(campo.data_source).map(([v, label]) => ({ value: v, label }))}
                    onChange={(v) => set(campo.name, v)}
                />
            );
        }

        if (campo.form_type === 'date') {
            return <CajasDateField id={id} name={campo.name} value={value} onChange={(v) => set(campo.name, v)} />;
        }

        return (
            <input
                id={id}
                name={campo.name}
                type={campo.type}
                className={`${cajasFormInputClass} ${hasError ? cajasInputErrorClass : ''}`}
                value={value}
                onChange={(e) => set(campo.name, e.target.value)}
            />
        );
    };

    return (
        <AppLayout title={`Editar ${modulo.singular} ${solicitud.nit}`}>
            <LoadingOverlay show={processing} message="Guardando cambios…" />
            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">Editar información registrada</h1>
                        <p className="text-muted-foreground mt-1 flex flex-wrap items-center gap-2 text-sm">
                            <span>
                                {solicitud.razsoc} · {modulo.identificacion} {solicitud.nit}
                            </span>
                            <EstadoBadge estado={solicitud.estado} label={solicitud.estado_detalle} />
                        </p>
                    </div>
                    <Link href={solicitud.urls.show} className={cajasFormBtnSecondary}>
                        <ArrowLeft className="mr-1.5 size-4" />
                        Volver al detalle
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                    {grupos.map(([titulo, items]) => (
                        <section key={titulo} className={cajasCardClass}>
                            <div className={cajasCardHeaderClass}>
                                <h2 className="text-foreground text-sm font-semibold">{titulo}</h2>
                            </div>
                            <div className={`${cajasCardBodyClass} grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3`}>
                                {items.map((campo) => (
                                    <div key={campo.name}>
                                        <label htmlFor={`edit-${campo.name}`} className={cajasFormLabelClass}>
                                            {campo.label}
                                        </label>
                                        {renderCampo(campo)}
                                        {errors[campo.name] && <p className={cajasFormErrorClass}>{errors[campo.name]}</p>}
                                    </div>
                                ))}
                            </div>
                        </section>
                    ))}

                    <div className="flex flex-wrap items-center justify-end gap-2">
                        <Link href={solicitud.urls.show} className={cajasFormBtnSecondary}>
                            Cancelar
                        </Link>
                        <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                            {processing ? <Loader2 className="mr-1.5 size-4 animate-spin" /> : <Save className="mr-1.5 size-4" />}
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
