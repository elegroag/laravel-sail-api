import AppLayout from '@/layouts/AppLayout';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import type { ModuloInfo, SolicitudResumen } from '@/pages/Cajas/Solicitudes/types';
import { cajasCardBodyClass, cajasCardClass, cajasCardHeaderClass, cajasFormBtnSecondary, cajasPageClass } from '@/pages/Cajas/styles/cajas-classes';
import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

type Trayecto = { fecafi: string; nit: string; codsuc: string; fecret: string };
type Salario = { fecha: string; salario: string };

type Props = {
    modulo: ModuloInfo;
    solicitud: SolicitudResumen;
    resultado: { consultado: boolean; trayectorias: Trayecto[]; salarios: Salario[] };
};

const formatNumber = (value: string) => {
    const n = Number(value);
    return Number.isFinite(n) && value !== '' ? n.toLocaleString('es-CO') : value;
};

const thClass = 'px-4 py-3 font-medium';
const tdClass = 'px-4 py-2.5';

export default function Trayectoria({ modulo, solicitud, resultado }: Props) {
    return (
        <AppLayout title={`Trayectoria ${solicitud.nit}`}>
            <div className={cajasPageClass}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">Trayectoria del {modulo.singular}</h1>
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

                {!resultado.consultado && (
                    <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                        SISU no respondió; no fue posible consultar la trayectoria.
                    </p>
                )}

                <section className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-foreground text-sm font-semibold">Trayectoria laboral ({resultado.trayectorias.length})</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {resultado.trayectorias.length === 0 ? (
                            <p className="text-muted-foreground text-sm">No hay trayectoria registrada en SISU.</p>
                        ) : (
                            <div className="border-border overflow-x-auto rounded-lg border">
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                                        <tr>
                                            <th className={thClass}>Fecha afiliación</th>
                                            <th className={thClass}>NIT empresa</th>
                                            <th className={thClass}>Sucursal</th>
                                            <th className={thClass}>Fecha retiro</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-border divide-y">
                                        {resultado.trayectorias.map((t, i) => (
                                            <tr key={`${t.nit}-${t.fecafi}-${i}`}>
                                                <td className={tdClass}>{t.fecafi}</td>
                                                <td className={tdClass}>{t.nit}</td>
                                                <td className={tdClass}>{t.codsuc}</td>
                                                <td className={tdClass}>{t.fecret}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                </section>

                <section className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-foreground text-sm font-semibold">Salarios ({resultado.salarios.length})</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        {resultado.salarios.length === 0 ? (
                            <p className="text-muted-foreground text-sm">No hay salarios registrados en SISU.</p>
                        ) : (
                            <div className="border-border overflow-x-auto rounded-lg border">
                                <table className="min-w-full text-left text-sm">
                                    <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                                        <tr>
                                            <th className={thClass}>Fecha</th>
                                            <th className={`${thClass} text-right`}>Salario</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-border divide-y">
                                        {resultado.salarios.map((s, i) => (
                                            <tr key={`${s.fecha}-${i}`}>
                                                <td className={tdClass}>{s.fecha}</td>
                                                <td className={`${tdClass} text-right`}>{formatNumber(s.salario)}</td>
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
