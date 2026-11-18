import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import type { AdjuntoItem, Cambio, Campo, Seccion, SeguimientoItem } from '@/pages/Cajas/Solicitudes/types';
import {
    cajasActionLinkClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnSecondary,
} from '@/pages/Cajas/styles/cajas-classes';
import { Download, ExternalLink, Eye } from 'lucide-react';
import { useState } from 'react';

export function CamposTable({ rows }: { rows: Campo[] }) {
    if (rows.length === 0) {
        return <p className="text-muted-foreground text-sm">Sin datos.</p>;
    }

    return (
        <div className="border-border overflow-hidden rounded-lg border">
            <table className="min-w-full text-sm">
                <tbody>
                    {rows.map((row, index) => (
                        <tr key={`${row.label}-${index}`} className="border-border/70 border-b last:border-b-0">
                            <th className="bg-muted/30 text-muted-foreground w-[35%] px-4 py-2.5 text-left font-medium">{row.label}</th>
                            <td className="text-foreground whitespace-pre-wrap px-4 py-2.5">{row.value || '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export function CambiosTable({ rows }: { rows: Cambio[] }) {
    return (
        <div className="border-border overflow-x-auto rounded-lg border">
            <table className="min-w-full text-left text-sm">
                <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                    <tr>
                        <th className="px-4 py-3 font-medium">Campo</th>
                        <th className="px-4 py-3 font-medium">Valor anterior</th>
                        <th className="px-4 py-3 font-medium">Valor nuevo</th>
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row) => (
                        <tr key={row.campo} className="border-border/70 border-b last:border-b-0">
                            <td className="px-4 py-2.5 font-medium">{row.label}</td>
                            <td className="text-muted-foreground whitespace-pre-wrap break-all px-4 py-2.5">{row.anterior || '—'}</td>
                            <td className="text-foreground whitespace-pre-wrap break-all px-4 py-2.5">{row.nuevo || '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export function SeccionesCard({ secciones }: { secciones: Seccion[] }) {
    return (
        <div className="space-y-4">
            {secciones.map((seccion) => (
                <section key={seccion.titulo} className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-foreground text-sm font-semibold">{seccion.titulo}</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <CamposTable rows={seccion.campos} />
                    </div>
                </section>
            ))}
        </div>
    );
}

export function SeguimientoTable({ rows }: { rows: SeguimientoItem[] }) {
    if (rows.length === 0) {
        return <p className="text-muted-foreground text-sm">No hay datos de seguimiento.</p>;
    }

    return (
        <div className="border-border overflow-x-auto rounded-lg border">
            <table className="min-w-full text-left text-sm">
                <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                    <tr>
                        <th className="px-4 py-3 font-medium">Observaciones</th>
                        <th className="px-4 py-3 font-medium">Estado</th>
                        <th className="px-4 py-3 font-medium">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row) => (
                        <tr key={row.item} className="border-border/70 border-b last:border-b-0">
                            <td className="whitespace-pre-wrap px-4 py-3">{row.nota || '—'}</td>
                            <td className="whitespace-nowrap px-4 py-3">
                                <EstadoBadge estado={row.estado} label={row.estado_detalle} />
                            </td>
                            <td className="text-muted-foreground whitespace-nowrap px-4 py-3">{row.fecha || '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

type ArchivoBlob = { item: AdjuntoItem; blobUrl: string };

async function fetchArchivo(url: string, download = false): Promise<Blob> {
    const response = await fetch(download ? `${url}?download=1` : url, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    const type = response.headers.get('content-type') ?? '';
    if (!response.ok || type.includes('text/html') || type.includes('application/json')) {
        throw new Error('El archivo no está disponible.');
    }
    return response.blob();
}

export function AdjuntosList({ rows }: { rows: AdjuntoItem[] }) {
    const [preview, setPreview] = useState<ArchivoBlob | null>(null);
    const [loading, setLoading] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);

    const closePreview = () => {
        if (preview) URL.revokeObjectURL(preview.blobUrl);
        setPreview(null);
    };

    const ver = async (item: AdjuntoItem) => {
        setError(null);
        setLoading(item.coddoc);
        try {
            const blob = await fetchArchivo(item.url);
            setPreview({ item, blobUrl: URL.createObjectURL(blob) });
        } catch (e) {
            setError(e instanceof Error ? e.message : 'No se pudo cargar el documento.');
        } finally {
            setLoading(null);
        }
    };

    const descargar = async (item: AdjuntoItem) => {
        setError(null);
        setLoading(item.coddoc);
        try {
            const blobUrl = URL.createObjectURL(await fetchArchivo(item.url, true));
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = item.archivo.split('/').pop() || item.detalle;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'No se pudo descargar el documento.');
        } finally {
            setLoading(null);
        }
    };

    if (rows.length === 0) {
        return <p className="text-muted-foreground text-sm">La solicitud no tiene archivos adjuntos.</p>;
    }

    return (
        <div className="space-y-3">
            {error && (
                <p role="alert" className="rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-900">
                    {error}
                </p>
            )}
            <div className="border-border overflow-x-auto rounded-lg border">
                <table className="min-w-full text-left text-sm">
                    <thead className="border-border bg-muted/40 text-muted-foreground border-b text-xs uppercase tracking-wide">
                        <tr>
                            <th className="px-4 py-3 font-medium">Documento</th>
                            <th className="px-4 py-3 font-medium">Archivo</th>
                            <th className="px-4 py-3 font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {rows.map((row) => (
                            <tr key={row.coddoc} className="border-border/70 border-b last:border-b-0">
                                <td className="px-4 py-3">{row.detalle}</td>
                                <td className="text-muted-foreground break-all px-4 py-3">{row.archivo}</td>
                                <td className="whitespace-nowrap px-4 py-3">
                                    <div className="flex flex-wrap items-center gap-1.5">
                                        <button type="button" disabled={loading !== null} className={cajasActionLinkClass} onClick={() => ver(row)}>
                                            <Eye className="size-3.5" />
                                            {loading === row.coddoc ? 'Cargando…' : 'Ver archivo'}
                                        </button>
                                        <button
                                            type="button"
                                            disabled={loading !== null}
                                            className={cajasActionLinkClass}
                                            onClick={() => descargar(row)}
                                        >
                                            <Download className="size-3.5" />
                                            Descargar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <Dialog open={preview !== null} onOpenChange={(open) => !open && closePreview()}>
                <DialogContent className="flex h-[90vh] w-full flex-col sm:max-w-5xl">
                    <DialogHeader>
                        <DialogTitle>{preview?.item.detalle}</DialogTitle>
                        <DialogDescription className="break-all">{preview?.item.archivo}</DialogDescription>
                    </DialogHeader>
                    {preview && (
                        <iframe
                            key={preview.blobUrl}
                            src={preview.blobUrl}
                            title={preview.item.detalle}
                            className="border-border min-h-0 flex-1 rounded border"
                        />
                    )}
                    <DialogFooter>
                        {preview && (
                            <>
                                <button type="button" className={cajasFormBtnSecondary} onClick={() => descargar(preview.item)}>
                                    <Download className="mr-1.5 size-4" />
                                    Descargar archivo
                                </button>
                                <button
                                    type="button"
                                    className={cajasFormBtnSecondary}
                                    onClick={() =>
                                        window.open(
                                            preview.blobUrl,
                                            preview.item.coddoc,
                                            'width=900,height=750,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes',
                                        )
                                    }
                                >
                                    <ExternalLink className="mr-1.5 size-4" />
                                    Abrir en ventana
                                </button>
                            </>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
