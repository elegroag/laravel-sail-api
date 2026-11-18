import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import EstadoBadge from '@/pages/Cajas/Solicitudes/EstadoBadge';
import type { AdjuntoItem, Campo, Seccion, SeguimientoItem } from '@/pages/Cajas/Solicitudes/types';
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

export function AdjuntosList({ rows }: { rows: AdjuntoItem[] }) {
    const [preview, setPreview] = useState<AdjuntoItem | null>(null);

    if (rows.length === 0) {
        return <p className="text-muted-foreground text-sm">La solicitud no tiene archivos adjuntos.</p>;
    }

    return (
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
                                    <button type="button" className={cajasActionLinkClass} onClick={() => setPreview(row)}>
                                        <Eye className="size-3.5" />
                                        Ver archivo
                                    </button>
                                    <a href={`${row.url}?download=1`} className={cajasActionLinkClass}>
                                        <Download className="size-3.5" />
                                        Descargar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            <Dialog open={preview !== null} onOpenChange={(open) => !open && setPreview(null)}>
                <DialogContent className="flex h-[90vh] w-full flex-col sm:max-w-5xl">
                    <DialogHeader>
                        <DialogTitle>{preview?.detalle}</DialogTitle>
                        <DialogDescription className="break-all">{preview?.archivo}</DialogDescription>
                    </DialogHeader>
                    {preview && (
                        <iframe
                            key={preview.coddoc}
                            src={preview.url}
                            title={preview.detalle}
                            className="border-border min-h-0 flex-1 rounded border"
                        />
                    )}
                    <DialogFooter>
                        {preview && (
                            <>
                                <a href={`${preview.url}?download=1`} className={cajasFormBtnSecondary}>
                                    <Download className="mr-1.5 size-4" />
                                    Descargar archivo
                                </a>
                                <a href={preview.url} target="_blank" rel="noopener noreferrer" className={cajasFormBtnSecondary}>
                                    <ExternalLink className="mr-1.5 size-4" />
                                    Abrir en pestaña nueva
                                </a>
                            </>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
