import AppLayout from '@/layouts/AppLayout';
import AprobarSolicitudPanel from '@/pages/Cajas/Solicitudes/AprobarSolicitudPanel';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import FlashMessages from '@/pages/Cajas/Solicitudes/FlashMessages';
import MotivoNotaPanel from '@/pages/Cajas/Solicitudes/MotivoNotaPanel';
import NotaPanel from '@/pages/Cajas/Solicitudes/NotaPanel';
import { AdjuntosList, CambiosTable, CamposTable, SeccionesCard, SeguimientoTable } from '@/pages/Cajas/Solicitudes/SolicitudInfo';
import SolicitudTabs, { type TabDef } from '@/pages/Cajas/Solicitudes/SolicitudTabs';
import type {
    Acciones,
    AdjuntoItem,
    AprobarSchema,
    Cambio,
    CampoCorregible,
    ModuloInfo,
    Motivo,
    Seccion,
    SeguimientoItem,
    SisuInfo,
    SolicitudResumen,
} from '@/pages/Cajas/Solicitudes/types';
import { cajasCardBodyClass, cajasCardClass, cajasCardHeaderClass, cajasFormBtnSecondary, cajasPageClass } from '@/pages/Cajas/styles/cajas-classes';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Banknote, Pencil, Undo2 } from 'lucide-react';
import { useState } from 'react';

type Props = {
    modulo: ModuloInfo;
    solicitud: SolicitudResumen;
    secciones: Seccion[];
    cambios: Cambio[];
    adjuntos: AdjuntoItem[];
    seguimiento: SeguimientoItem[];
    acciones: Acciones;
    sisu: SisuInfo;
    motivos: Motivo[];
    camposCorregibles: CampoCorregible[];
    aprobar: AprobarSchema | null;
};

export default function Show({
    modulo,
    solicitud,
    secciones,
    cambios,
    adjuntos,
    seguimiento,
    acciones,
    sisu,
    motivos,
    camposCorregibles,
    aprobar,
}: Props) {
    const base = solicitud.urls.show;
    const [tab, setTab] = useState('solicitud');

    const tabs: TabDef[] = [
        { id: 'solicitud', label: 'Solicitud' },
        { id: 'archivos', label: `Archivos (${adjuntos.length})` },
        { id: 'seguimiento', label: `Seguimiento (${seguimiento.length})` },
        ...(acciones.aprobar ? [{ id: 'aprobar', label: 'Aprobar' }] : []),
        ...(acciones.devolver ? [{ id: 'devolver', label: 'Devolver' }] : []),
        ...(acciones.rechazar ? [{ id: 'rechazar', label: 'Rechazar' }] : []),
        ...(acciones.reaprobar ? [{ id: 'reaprobar', label: 'Re-aprobar' }] : []),
    ];

    return (
        <AppLayout title={`${modulo.titulo} ${solicitud.nit}`}>
            <div className={cajasPageClass}>
                <FlashMessages />

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-foreground text-base font-semibold">{solicitud.razsoc || `Solicitud de ${modulo.singular}`}</h1>
                        <p className="text-muted-foreground mt-1 flex flex-wrap items-center gap-2 text-sm">
                            <span>
                                {modulo.identificacion} {solicitud.nit}
                            </span>
                            <EstadoBadge estado={solicitud.estado} label={solicitud.estado_detalle} />
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                        {acciones.editar && (
                            <Link href={`${base}/edit`} className={cajasFormBtnSecondary}>
                                <Pencil className="mr-1.5 size-4" />
                                Editar información
                            </Link>
                        )}
                        {acciones.aportes && (
                            <Link href={`${base}/aportes`} className={cajasFormBtnSecondary}>
                                <Banknote className="mr-1.5 size-4" />
                                Aportes
                            </Link>
                        )}
                        {acciones.deshacer && (
                            <Link href={`${base}/deshacer`} className={cajasFormBtnSecondary}>
                                <Undo2 className="mr-1.5 size-4" />
                                Deshacer afiliación
                            </Link>
                        )}
                        <Link href={`${solicitud.urls.index}?estado=${solicitud.estado}`} className={cajasFormBtnSecondary}>
                            <ArrowLeft className="mr-1.5 size-4" />
                            Volver a la lista
                        </Link>
                    </div>
                </div>

                {solicitud.estado === 'A' && (
                    <p className="rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-900">
                        La solicitud ya se encuentra aprobada.
                    </p>
                )}
                {solicitud.estado === 'T' && (
                    <p className="rounded-md border border-pink-300 bg-pink-50 px-3 py-2 text-sm text-pink-900">
                        La solicitud se encuentra en estado temporal; no dispone de usuario asignado para validación.
                    </p>
                )}
                {(solicitud.estado === 'D' || solicitud.estado === 'X') && solicitud.motivo && (
                    <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                        Motivo registrado: {solicitud.motivo}
                    </p>
                )}

                <SolicitudTabs tabs={tabs} active={tab} onChange={setTab} />

                <div role="tabpanel" id={`panel-${tab}`} aria-labelledby={`tab-${tab}`} className="space-y-4">
                    {tab === 'solicitud' && cambios.length > 0 && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardHeaderClass}>
                                <h2 className="text-foreground text-sm font-semibold">Cambios solicitados ({cambios.length})</h2>
                            </div>
                            <div className={cajasCardBodyClass}>
                                <CambiosTable rows={cambios} />
                            </div>
                        </section>
                    )}

                    {tab === 'solicitud' && (
                        <div className={sisu.consultado && sisu.existe ? 'grid items-start gap-4 lg:grid-cols-2' : 'space-y-4'}>
                            <SeccionesCard secciones={secciones} />
                            <section className={cajasCardClass}>
                                <div className={cajasCardHeaderClass}>
                                    <h2 className="text-foreground text-sm font-semibold">Empresa en SISU</h2>
                                </div>
                                <div className={cajasCardBodyClass}>
                                    {!sisu.consultado ? (
                                        <p className="text-muted-foreground text-sm">SISU no respondió; no fue posible verificar el registro.</p>
                                    ) : !sisu.existe ? (
                                        <p className="text-muted-foreground text-sm">No se encontró un registro en SISU.</p>
                                    ) : (
                                        <CamposTable rows={sisu.datos} />
                                    )}
                                </div>
                            </section>
                        </div>
                    )}

                    {tab === 'archivos' && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <AdjuntosList rows={adjuntos} />
                            </div>
                        </section>
                    )}

                    {tab === 'seguimiento' && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <SeguimientoTable rows={seguimiento} />
                            </div>
                        </section>
                    )}

                    {tab === 'aprobar' && aprobar && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <AprobarSolicitudPanel action={`${base}/aprobar`} schema={aprobar} singular={modulo.singular} />
                            </div>
                        </section>
                    )}

                    {tab === 'devolver' && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <MotivoNotaPanel
                                    action={`${base}/devolver`}
                                    title="Devolver"
                                    description="Devuelve la solicitud al afiliado para que corrija la información indicada."
                                    submitLabel="Devolver"
                                    confirmTitle="Devolver solicitud"
                                    confirmDescription="Se enviará un correo al solicitante con el motivo de la devolución."
                                    motivos={motivos}
                                    camposCorregibles={camposCorregibles}
                                />
                            </div>
                        </section>
                    )}

                    {tab === 'rechazar' && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <MotivoNotaPanel
                                    action={`${base}/rechazar`}
                                    title="Rechazar"
                                    description="Rechaza la solicitud e informa la causal al solicitante."
                                    submitLabel="Rechazar"
                                    confirmTitle="Rechazar solicitud"
                                    confirmDescription="Se enviará un correo al solicitante con el motivo del rechazo."
                                    motivos={motivos}
                                />
                            </div>
                        </section>
                    )}

                    {tab === 'reaprobar' && (
                        <section className={cajasCardClass}>
                            <div className={cajasCardBodyClass}>
                                <NotaPanel
                                    action={`${base}/reaprobar`}
                                    title="Re-aprobar"
                                    description="Marca la solicitud como aprobada cuando ya existe en SISU y deja registro en el seguimiento."
                                    submitLabel="Re-aprobar"
                                    confirmTitle="Re-aprobar solicitud"
                                    confirmDescription="La solicitud quedará en estado Aprobado con la nota indicada."
                                />
                            </div>
                        </section>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
