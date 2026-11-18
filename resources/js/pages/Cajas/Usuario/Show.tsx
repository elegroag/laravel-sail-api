import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Loader2, Pencil } from 'lucide-react';
import { useEffect, useState } from 'react';

type Usuario = {
    documento: string;
    tipo: string;
    coddoc: string;
    nombre: string;
    email: string;
    estado: string;
    estado_detalle: string;
    tipo_detalle: string;
    coddoc_detalle: string;
    whatsapp: string;
    codciu: string;
    codciu_detalle: string;
    autoriza: string;
    fecreg: string | null;
    feccla: string | null;
    fecha_syncron: string | null;
    edit_url: string;
};

type Props = {
    usuario: Usuario;
};

type Field = { label: string; value: string };

function FieldTable({ rows }: { rows: Field[] }) {
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

function estadoBadgeClass(estado: string): string {
    if (estado === 'A') return `${cajasBadgeClass} bg-emerald-100 text-emerald-800`;
    if (estado === 'I') return `${cajasBadgeClass} bg-amber-100 text-amber-800`;
    if (estado === 'B') return `${cajasBadgeClass} bg-red-100 text-red-800`;
    return `${cajasBadgeClass} bg-muted text-muted-foreground`;
}

export default function Show({ usuario }: Props) {
    const page = usePage<{ flash?: { success?: string } }>();
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) setToast(success);
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 4000);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const handleBack = () => {
        router.get(
            '/cajas/usuario/index',
            {},
            {
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onCancel: () => setProcessing(false),
                onError: () => setProcessing(false),
            },
        );
    };

    const identityRows: Field[] = [
        { label: 'Documento', value: usuario.documento },
        { label: 'Tipo documento', value: usuario.coddoc_detalle || usuario.coddoc },
        { label: 'Tipo usuario', value: usuario.tipo_detalle || usuario.tipo },
        { label: 'Nombre', value: usuario.nombre },
        { label: 'Email', value: usuario.email },
        { label: 'WhatsApp', value: usuario.whatsapp },
        { label: 'Ciudad', value: usuario.codciu_detalle || usuario.codciu },
    ];

    const statusRows: Field[] = [
        { label: 'Estado', value: usuario.estado_detalle || usuario.estado },
        { label: 'Autoriza', value: usuario.autoriza === 'S' ? 'Sí' : usuario.autoriza === 'N' ? 'No' : usuario.autoriza },
        { label: 'Fecha registro', value: usuario.fecreg ?? '' },
        { label: 'Fecha clave', value: usuario.feccla ?? '' },
        { label: 'Última sincronización', value: usuario.fecha_syncron ?? '' },
    ];

    return (
        <AppLayout title={`Usuario ${usuario.documento}`}>
            <LoadingOverlay show={processing} message="Volviendo al listado…" />
            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                {toast && <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}

                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-base font-semibold text-foreground">{usuario.nombre || 'Usuario externo'}</h1>
                        <p className="mt-1 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                            <span>
                                {usuario.tipo_detalle || usuario.tipo} · {usuario.documento}
                            </span>
                            <span className={estadoBadgeClass(usuario.estado)}>{usuario.estado_detalle || usuario.estado}</span>
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                        <button type="button" className={cajasFormBtnSecondary} onClick={handleBack} disabled={processing}>
                            {processing ? (
                                <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />
                            ) : (
                                <ArrowLeft className="mr-1.5 h-4 w-4" />
                            )}
                            {processing ? 'Cargando…' : 'Volver'}
                        </button>
                        <Link href={usuario.edit_url} className={cajasFormBtnPrimary} prefetch>
                            <Pencil className="mr-1.5 size-4" />
                            Editar
                        </Link>
                    </div>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Identificación</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <FieldTable rows={identityRows} />
                    </div>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Estado y fechas</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <FieldTable rows={statusRows} />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
