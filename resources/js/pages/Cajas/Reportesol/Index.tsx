import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Download, FileSpreadsheet } from 'lucide-react';
import { useState, type FormEvent } from 'react';

type Option = { value: string; label: string };

type Props = {
    title: string;
    tipoOptions: Option[];
    estadoOptions: Option[];
    procesarUrl: string;
};

export default function Index({ title, tipoOptions, estadoOptions, procesarUrl }: Props) {
    const [tipo, setTipo] = useState('');
    const [estado, setEstado] = useState('');
    const [fechaSolicitud, setFechaSolicitud] = useState('');
    const [fechaAprueba, setFechaAprueba] = useState('');
    const [localError, setLocalError] = useState<string | null>(null);
    const [feedback, setFeedback] = useState<string | null>(null);

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);
        setFeedback(null);

        if (!tipo) {
            setLocalError('Seleccione el tipo de solicitud.');
            return;
        }

        const qs = new URLSearchParams({ tipo });
        if (estado) qs.set('estado', estado);
        if (fechaSolicitud) qs.set('fecha_solicitud', fechaSolicitud);
        if (fechaAprueba) qs.set('fecha_aprueba', fechaAprueba);

        setFeedback('Se está generando el Excel. Si no inicia la descarga, revise el bloqueador de ventanas emergentes.');
        window.open(`${procesarUrl}?${qs.toString()}`, '_blank', 'noopener,noreferrer');
    };

    return (
        <AppLayout title={title}>
            <div className={cajasPageClass}>
                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <FileSpreadsheet className="size-5 text-cajas-border" />
                        {title}
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Exporte solicitudes por tipo y estado, con filtros opcionales de fechas
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    El tipo de solicitud es <strong>obligatorio</strong>. Estado y fechas son opcionales: si no los
                    indica, se incluyen todos los estados del tipo seleccionado. El archivo Excel se abrirá en una nueva
                    pestaña.
                </div>

                <section className={`${cajasCardClass} max-w-3xl`}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros del reporte</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label htmlFor="tipo" className={cajasFormLabelClass}>
                                        Tipo de solicitud
                                    </label>
                                    <select
                                        id="tipo"
                                        className={cajasFormSelectClass}
                                        value={tipo}
                                        onChange={(e) => {
                                            setTipo(e.target.value);
                                            setLocalError(null);
                                            setFeedback(null);
                                        }}
                                    >
                                        <option value="">Seleccione</option>
                                        {tipoOptions.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label htmlFor="estado" className={cajasFormLabelClass}>
                                        Estado de solicitud
                                    </label>
                                    <select
                                        id="estado"
                                        className={cajasFormSelectClass}
                                        value={estado}
                                        onChange={(e) => setEstado(e.target.value)}
                                    >
                                        <option value="">Todos</option>
                                        {estadoOptions.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label htmlFor="fecha_solicitud" className={cajasFormLabelClass}>
                                        Fecha de envío
                                    </label>
                                    <CajasDateField id="fecha_solicitud" value={fechaSolicitud} onChange={setFechaSolicitud} />
                                    <p className="mt-1 text-xs text-muted-foreground">Opcional. Filtra por fecha de radicación/envío.</p>
                                </div>
                                <div>
                                    <label htmlFor="fecha_aprueba" className={cajasFormLabelClass}>
                                        Fecha de aprobación
                                    </label>
                                    <CajasDateField id="fecha_aprueba" value={fechaAprueba} onChange={setFechaAprueba} />
                                    <p className="mt-1 text-xs text-muted-foreground">Opcional. Filtra por fecha de aprobación.</p>
                                </div>
                            </div>

                            {localError && <p className={cajasFormErrorClass}>{localError}</p>}
                            {feedback && (
                                <div className="rounded-md border border-cajas-success/30 bg-cajas-success/10 px-3 py-2 text-sm text-cajas-success">
                                    {feedback}
                                </div>
                            )}

                            <div className="flex justify-center pt-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={!tipo}>
                                    <Download className="mr-1.5 size-4" />
                                    Generar Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
