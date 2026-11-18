import AppLayout from '@/layouts/AppLayout';
import CajasDateField from '@/pages/Cajas/components/CajasDateField';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import { Download, Loader2, Scale, Search } from 'lucide-react';
import { useEffect, useState, type FormEvent } from 'react';

type Option = { value: string; label: string };

type Resumen = {
    total: number;
    en_termino: number;
    vencido: number;
    en_tramite: number;
};

type Props = {
    filters: {
        fecini: string;
        fecfin: string;
        tipafis: string;
    };
    tipafiOptions: Option[];
    umbralDias: number;
    resumen: Resumen;
    queried: boolean;
    error: string | null;
    exportarUrl: string;
};

const resumenCards = [
    { key: 'total' as const, label: 'Total radicados', tone: 'border-border text-foreground' },
    { key: 'en_termino' as const, label: 'En término', tone: 'border-cajas-success/40 text-cajas-success' },
    { key: 'vencido' as const, label: 'Vencidos', tone: 'border-cajas-danger/40 text-cajas-danger' },
    { key: 'en_tramite' as const, label: 'En trámite', tone: 'border-cajas-warning/40 text-cajas-warning' },
];

export default function Index({
    filters,
    tipafiOptions,
    umbralDias,
    resumen,
    queried,
    error,
    exportarUrl,
}: Props) {
    const [fecini, setFecini] = useState(filters.fecini);
    const [fecfin, setFecfin] = useState(filters.fecfin);
    const [tipafis, setTipafis] = useState(filters.tipafis);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [feedback, setFeedback] = useState<string | null>(null);

    useEffect(() => {
        setFecini(filters.fecini);
        setFecfin(filters.fecfin);
        setTipafis(filters.tipafis);
        setProcessing(false);
    }, [filters.fecini, filters.fecfin, filters.tipafis, queried, resumen.total]);

    const visit = (params: Record<string, string | number | boolean>) => {
        setProcessing(true);
        setLocalError(null);
        setFeedback(null);
        router.get('/cajas/reporte-oportunidad/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const handleConsultar = (event?: FormEvent) => {
        event?.preventDefault();
        if (!fecini || !fecfin) {
            setLocalError('Complete las fechas requeridas.');
            return;
        }
        visit({
            queried: 1,
            fecini,
            fecfin,
            tipafis,
        });
    };

    const handleExportar = () => {
        setLocalError(null);
        if (!fecini || !fecfin) {
            setLocalError('Complete las fechas requeridas para generar el reporte.');
            return;
        }

        const qs = new URLSearchParams({
            fecini,
            fecfin,
        });
        if (tipafis) {
            qs.append('tipafis[]', tipafis);
        }

        setFeedback('Se está generando el Excel. Si no inicia la descarga, revise el bloqueador de ventanas emergentes.');
        window.open(`${exportarUrl}?${qs.toString()}`, '_blank', 'noopener,noreferrer');
    };

    const displayError = localError || error;

    return (
        <AppLayout title="Reporte Oportunidad Afiliaciones">
            <LoadingOverlay show={processing} message="Calculando oportunidad…" />

            <div className={cajasPageClass}>
                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <Scale className="size-5 text-cajas-border" />
                        Reporte Oportunidad Afiliaciones
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Control de días hábiles entre el envío a caja y el cierre del radicado
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    Se calculan los <strong>días hábiles</strong> entre el <strong>envío a caja</strong> (evento P) y el{' '}
                    <strong>cierre</strong> del mismo radicado. Umbral configurado:{' '}
                    <strong>{umbralDias} días hábiles</strong>. Se omiten solicitudes inactivas y eventos sin solicitud.
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Filtros</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form
                            onSubmit={handleConsultar}
                            className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4"
                            autoComplete="off"
                        >
                            <div>
                                <label htmlFor="fecini" className={cajasFormLabelClass}>
                                    Fecha envío inicial
                                </label>
                                <CajasDateField id="fecini" value={fecini} disabled={processing} onChange={setFecini} />
                            </div>
                            <div>
                                <label htmlFor="fecfin" className={cajasFormLabelClass}>
                                    Fecha envío final
                                </label>
                                <CajasDateField id="fecfin" value={fecfin} disabled={processing} onChange={setFecfin} />
                            </div>
                            <div>
                                <label htmlFor="tipafis" className={cajasFormLabelClass}>
                                    Tipo de afiliación
                                </label>
                                <select
                                    id="tipafis"
                                    className={cajasFormSelectClass}
                                    value={tipafis}
                                    disabled={processing}
                                    onChange={(e) => setTipafis(e.target.value)}
                                >
                                    <option value="">Todos</option>
                                    {tipafiOptions.map((opt) => (
                                        <option key={opt.value} value={opt.value}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="flex items-end gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                    Consultar
                                </button>
                                <button
                                    type="button"
                                    className={cajasFormBtnSecondary}
                                    disabled={processing || !fecini || !fecfin}
                                    onClick={handleExportar}
                                >
                                    <Download className="mr-1.5 h-4 w-4" />
                                    Excel
                                </button>
                            </div>
                        </form>
                        {displayError && <p className={`${cajasFormErrorClass} mt-3`}>{displayError}</p>}
                        {feedback && (
                            <div className="mt-3 rounded-md border border-cajas-success/30 bg-cajas-success/10 px-3 py-2 text-sm text-cajas-success">
                                {feedback}
                            </div>
                        )}
                    </div>
                </div>

                {queried && (
                    <div className="grid grid-cols-2 gap-3 md:grid-cols-4">
                        {resumenCards.map((card) => (
                            <div key={card.key} className={`rounded-xl border bg-card px-4 py-3 shadow-sm ${card.tone}`}>
                                <p className="text-xs text-muted-foreground">{card.label}</p>
                                <p className="text-xl font-semibold">{resumen[card.key] ?? 0}</p>
                            </div>
                        ))}
                    </div>
                )}

                {!queried && (
                    <div className={cajasCardClass}>
                        <div className={cajasCardBodyClass}>
                            <p className="text-sm text-muted-foreground">
                                Seleccione el rango de fechas y pulse Consultar para ver el resumen de oportunidad, o
                                Excel para descargar el detalle.
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
