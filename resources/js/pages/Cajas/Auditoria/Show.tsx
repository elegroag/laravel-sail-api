import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnSecondary,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import { ArrowLeft, Loader2 } from 'lucide-react';
import { useState } from 'react';

type Field = { label: string; value: string };

type Seguimiento = {
    nota: string;
    estado: string;
    fecha: string;
};

type Props = {
    detalle: {
        tipopc: string;
        tipopc_label: string;
        id: number;
        fields: Field[];
        extraFields: Field[];
        seguimiento: Seguimiento[];
    };
    backFilters: {
        fecini?: string;
        fecfin?: string;
    };
};

function FieldTable({ rows }: { rows: Field[] }) {
    if (rows.length === 0) {
        return <p className="text-sm text-muted-foreground">Sin datos.</p>;
    }

    return (
        <div className="overflow-hidden rounded-lg border border-border">
            <table className="min-w-full text-sm">
                <tbody>
                    {rows.map((row) => (
                        <tr key={row.label} className="border-b border-border/70 last:border-b-0">
                            <th className="w-[35%] bg-muted/30 px-4 py-2.5 text-left font-medium text-muted-foreground">{row.label}</th>
                            <td className="px-4 py-2.5 text-foreground whitespace-pre-wrap">{row.value || '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default function Show({ detalle, backFilters }: Props) {
    const [processing, setProcessing] = useState(false);

    const handleBack = () => {
        router.get(
            '/cajas/auditoria/index',
            {
                tipopc: detalle.tipopc,
                ...(backFilters.fecini ? { fecini: backFilters.fecini } : {}),
                ...(backFilters.fecfin ? { fecfin: backFilters.fecfin } : {}),
            },
            {
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onCancel: () => setProcessing(false),
                onError: () => setProcessing(false),
            },
        );
    };

    return (
        <AppLayout title={`Detalle #${detalle.id}`}>
            <LoadingOverlay show={processing} message="Volviendo a la consulta…" />
            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-base font-semibold text-foreground">Detalle de la solicitud</h1>
                        <p className="text-sm text-muted-foreground">
                            {detalle.tipopc_label} · ID {detalle.id}
                        </p>
                    </div>
                    <button type="button" className={cajasFormBtnSecondary} onClick={handleBack} disabled={processing}>
                        {processing ? (
                            <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />
                        ) : (
                            <ArrowLeft className="mr-1.5 h-4 w-4" />
                        )}
                        {processing ? 'Cargando…' : 'Volver'}
                    </button>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Resumen de la solicitud</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <FieldTable rows={detalle.fields} />
                    </div>
                </div>

                {detalle.extraFields.length > 0 && (
                    <div className={cajasCardClass}>
                        <div className={cajasCardHeaderClass}>
                            <h2 className="text-sm font-semibold text-foreground">Datos de la solicitud</h2>
                        </div>
                        <div className={cajasCardBodyClass}>
                            <FieldTable rows={detalle.extraFields} />
                        </div>
                    </div>
                )}

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Seguimiento</h2>
                    </div>
                    <div className={`${cajasCardBodyClass} overflow-x-auto p-0`}>
                        {detalle.seguimiento.length === 0 ? (
                            <p className="p-6 text-sm text-muted-foreground">No hay datos de seguimiento.</p>
                        ) : (
                            <table className="min-w-full text-left text-sm">
                                <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Observaciones</th>
                                        <th className="px-4 py-3 font-medium">Estado</th>
                                        <th className="px-4 py-3 font-medium">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {detalle.seguimiento.map((row, index) => (
                                        <tr key={`${row.fecha}-${index}`} className="border-b border-border/70">
                                            <td className="px-4 py-3 whitespace-pre-wrap">{row.nota}</td>
                                            <td className="px-4 py-3 whitespace-nowrap">{row.estado}</td>
                                            <td className="px-4 py-3 whitespace-nowrap">{row.fecha}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
