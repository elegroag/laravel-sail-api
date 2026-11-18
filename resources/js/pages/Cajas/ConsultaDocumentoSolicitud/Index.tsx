import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router } from '@inertiajs/react';
import { Eraser, FileSearch, Loader2, Search } from 'lucide-react';
import { useEffect, useState, type FormEvent } from 'react';

type Option = { value: string; label: string };

type Solicitud = {
    id: number | string;
    tipopc: number;
    tipo: string;
    tabla: string;
    campo: string;
    ruuid: string | null;
    nombre: string;
    documento: string;
    fecsol: string | null;
    fecsis: string | null;
    fecest: string | null;
    estado: string;
    estado_detalle: string;
};

type Grupo = {
    tipopc: number;
    label: string;
    tabla: string;
    campo: string;
    total: number;
    solicitudes: Solicitud[];
};

type Props = {
    filters: {
        documento: string;
    };
    tipopcOptions: Option[];
    resultado: {
        documento: string;
        total: number;
        grupos: Grupo[];
    };
    queried: boolean;
    error: string | null;
};

const badgeTone: Record<string, string> = {
    T: 'bg-cajas-border/15 text-cajas-border ring-cajas-border/25',
    D: 'bg-cajas-info/15 text-cajas-info ring-cajas-info/25',
    A: 'bg-cajas-success/15 text-cajas-success ring-cajas-success/25',
    P: 'bg-cajas-warning/15 text-cajas-warning ring-cajas-warning/25',
    X: 'bg-muted text-muted-foreground ring-border',
    C: 'bg-cajas-danger/15 text-cajas-danger ring-cajas-danger/25',
    I: 'bg-cajas-danger/15 text-cajas-danger ring-cajas-danger/25',
};

export default function Index({ filters, resultado, queried, error }: Props) {
    const [documento, setDocumento] = useState(filters.documento);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);

    useEffect(() => {
        setDocumento(filters.documento);
        setProcessing(false);
    }, [filters.documento, queried, resultado.total]);

    const visit = (params: Record<string, string | number | boolean>) => {
        setProcessing(true);
        setLocalError(null);
        router.get('/cajas/consulta-documento-solicitud/index', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => setProcessing(false),
            onError: () => setProcessing(false),
        });
    };

    const handleConsultar = (event?: FormEvent) => {
        event?.preventDefault();
        const value = documento.trim();
        if (!value) {
            setLocalError('Indique el documento de identificación.');
            return;
        }
        visit({
            queried: 1,
            documento: value,
        });
    };

    const handleLimpiar = () => {
        setDocumento('');
        setLocalError(null);
        visit({
            queried: 0,
            documento: '',
        });
    };

    const displayError = localError || error;
    const grupos = resultado.grupos || [];

    return (
        <AppLayout title="Consulta de solicitudes por documento">
            <LoadingOverlay show={processing} message="Consultando solicitudes…" />

            <div className={cajasPageClass}>
                <div>
                    <h1 className="flex items-center gap-2 text-base font-semibold text-foreground">
                        <FileSearch className="size-5 text-cajas-border" />
                        Consulta de solicitudes por documento
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Consulte las solicitudes de afiliación asociadas a un documento de identificación
                    </p>
                </div>

                <div className="rounded-lg border border-cajas-border/25 bg-cajas-border/5 px-4 py-3 text-sm text-foreground">
                    Use el <strong>documento de identificación</strong> de la persona. La consulta revisa empresa (
                    <code>cedrep</code>), trabajador (<code>cedtra</code>), cónyuge (<code>cedcon</code>), beneficiario (
                    <code>numdoc</code>), facultativo, pensionado e independiente. Solo se muestran los tipos con
                    coincidencias.
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Identificación de la persona</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleConsultar} className="flex flex-col gap-4 sm:flex-row sm:items-end" autoComplete="off">
                            <div className="w-full max-w-sm">
                                <label htmlFor="documento" className={cajasFormLabelClass}>
                                    Documento de identificación
                                </label>
                                <input
                                    id="documento"
                                    type="text"
                                    className={cajasFormInputClass}
                                    maxLength={20}
                                    placeholder="Ej. 1048123456"
                                    value={documento}
                                    disabled={processing}
                                    onChange={(e) => setDocumento(e.target.value)}
                                />
                                <p className="mt-1 text-xs text-muted-foreground">Número sin puntos ni espacios.</p>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                    Consultar
                                </button>
                                <button type="button" className={cajasFormBtnSecondary} disabled={processing} onClick={handleLimpiar}>
                                    <Eraser className="mr-1.5 h-4 w-4" />
                                    Limpiar
                                </button>
                            </div>
                        </form>
                        {displayError && <p className={`${cajasFormErrorClass} mt-3`}>{displayError}</p>}
                    </div>
                </div>

                {queried && (
                    <div className="space-y-4">
                        <div className="rounded-xl border border-border bg-card px-4 py-3 text-sm shadow-sm">
                            {resultado.total === 0 ? (
                                <>
                                    Documento <strong>{resultado.documento}</strong>: sin coincidencias.
                                </>
                            ) : (
                                <>
                                    Documento <strong>{resultado.documento}</strong>:{' '}
                                    <strong>{resultado.total}</strong> solicitud(es) en <strong>{grupos.length}</strong>{' '}
                                    tipo(s).
                                </>
                            )}
                        </div>

                        {resultado.total === 0 ? (
                            <div className="rounded-xl border border-dashed border-border bg-muted/30 px-4 py-10 text-center text-sm text-muted-foreground">
                                No se encontraron solicitudes para el documento indicado.
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                                {grupos.map((grupo) => (
                                    <section key={`${grupo.tipopc}-${grupo.tabla}`} className={cajasCardClass}>
                                        <div className={`${cajasCardHeaderClass} flex items-start justify-between gap-3`}>
                                            <div>
                                                <h3 className="text-sm font-semibold text-foreground">{grupo.label}</h3>
                                                <p className="text-xs text-muted-foreground">
                                                    {grupo.tabla}.{grupo.campo}
                                                </p>
                                            </div>
                                            <span className={`${cajasBadgeClass} bg-cajas-border/10 text-cajas-border ring-cajas-border/25`}>
                                                {grupo.total} solicitud(es)
                                            </span>
                                        </div>
                                        <div className={`${cajasCardBodyClass} space-y-3`}>
                                            {grupo.solicitudes.map((item) => {
                                                const estadoCode = String(item.estado || 'X').toUpperCase();
                                                return (
                                                    <article
                                                        key={`${grupo.tipopc}-${item.id}`}
                                                        className="rounded-lg border border-border px-3 py-3"
                                                    >
                                                        <div className="mb-2 flex items-start justify-between gap-2">
                                                            <div>
                                                                <p className="text-sm font-semibold text-foreground">
                                                                    {item.nombre || 'Sin nombre'}
                                                                </p>
                                                                <p className="text-xs text-muted-foreground">
                                                                    Radicado: {item.ruuid || '—'} · ID {item.id}
                                                                </p>
                                                            </div>
                                                            <span
                                                                className={`${cajasBadgeClass} ${badgeTone[estadoCode] ?? 'bg-muted text-muted-foreground ring-border'}`}
                                                            >
                                                                {item.estado_detalle || item.estado || 'Sin estado'}
                                                            </span>
                                                        </div>
                                                        <div className="grid gap-1 text-xs text-muted-foreground">
                                                            <div>Solicitud: {item.fecsol || '—'}</div>
                                                            <div>Sistema: {item.fecsis || '—'}</div>
                                                        </div>
                                                    </article>
                                                );
                                            })}
                                        </div>
                                    </section>
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
