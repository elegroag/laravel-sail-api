import AppLayout from '@/layouts/AppLayout';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { SolicitudResumen } from '@/pages/Cajas/Solicitudes/types';
import { cajasCardBodyClass, cajasCardClass, cajasCardHeaderClass, cajasFormBtnSecondary, cajasPageClass } from '@/pages/Cajas/styles/cajas-classes';
import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

type Aporte = {
    perapo: string;
    fecrec: string;
    fecsis: string;
    cedtra: string;
    codsuc: string;
    numero: string | number;
    valapo: string | number;
    valnom: string | number;
};

type Props = {
    solicitud: SolicitudResumen;
    resultado: { success: boolean; msj: string; aportes: Aporte[] };
};

const COLUMNS: { key: keyof Aporte; label: string; numeric?: boolean }[] = [
    { key: 'perapo', label: 'Periodo aportes' },
    { key: 'fecrec', label: 'Fecha recibo' },
    { key: 'fecsis', label: 'Fecha sistema' },
    { key: 'cedtra', label: 'Cédula trabajador' },
    { key: 'codsuc', label: 'Sucursal' },
    { key: 'numero', label: 'Número' },
    { key: 'valapo', label: 'Valor aportes', numeric: true },
    { key: 'valnom', label: 'Valor nómina', numeric: true },
];

const formatNumber = (value: string | number) => {
    const n = Number(value);
    return Number.isFinite(n) && value !== '' ? n.toLocaleString('es-CO') : String(value ?? '');
};

export default function Aportes({ solicitud, resultado }: Props) {
    return (
        <AppLayout title={`Aportes ${solicitud.nit}`}>
            <div className={cajasPageClass}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">Aportes de la empresa</h1>
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
                        <h2 className="text-foreground text-sm font-semibold">Aportes registrados en SISU ({resultado.aportes.length})</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {!resultado.success && resultado.msj && (
                            <p className="mb-3 rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">{resultado.msj}</p>
                        )}
                        {resultado.aportes.length === 0 ? (
                            <p className="text-muted-foreground text-sm">No hay aportes aplicados por parte de la empresa.</p>
                        ) : (
                            <div className="border-border overflow-x-auto rounded-lg border">
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                                        <tr>
                                            {COLUMNS.map((c) => (
                                                <th key={c.key} className={`px-4 py-3 font-medium ${c.numeric ? 'text-right' : ''}`}>
                                                    {c.label}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {resultado.aportes.map((row, index) => (
                                            <tr key={index} className="border-border/70 border-b last:border-b-0">
                                                {COLUMNS.map((c) => (
                                                    <td
                                                        key={c.key}
                                                        className={`whitespace-nowrap px-4 py-2.5 ${c.numeric ? 'text-right tabular-nums' : ''}`}
                                                    >
                                                        {c.numeric ? formatNumber(row[c.key]) : String(row[c.key] ?? '')}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
