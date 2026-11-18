import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
import CajasSearchSelect from '@/pages/Cajas/components/CajasSearchSelect';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { FileText, Loader2 } from 'lucide-react';
import { useEffect, useMemo, useRef, useState, type FormEvent } from 'react';

type Option = { value: string; label: string };

type Props = {
    title: string;
    tipos: Option[];
};

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

export default function CertificadoTrabajador({ title, tipos }: Props) {
    const [nit, setNit] = useState('');
    const [cedtra, setCedtra] = useState('');
    const [tipo, setTipo] = useState(tipos[0]?.value ?? 'A');
    const [trabajadores, setTrabajadores] = useState<Option[]>([]);
    const [trabajadorPlaceholder, setTrabajadorPlaceholder] = useState('Ingrese NIT primero');
    const [loadingTrabajadores, setLoadingTrabajadores] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [dialogOpen, setDialogOpen] = useState(false);
    const [pdfUrl, setPdfUrl] = useState<string | null>(null);
    const lastNitLoaded = useRef('');

    const tipoOptions = useMemo(() => tipos, [tipos]);

    useEffect(() => {
        return () => {
            if (pdfUrl) {
                URL.revokeObjectURL(pdfUrl);
            }
        };
    }, [pdfUrl]);

    const revokePdf = () => {
        setPdfUrl((current) => {
            if (current) {
                URL.revokeObjectURL(current);
            }
            return null;
        });
    };

    const handleDialogOpenChange = (open: boolean) => {
        setDialogOpen(open);
        if (!open) {
            revokePdf();
        }
    };

    const cargarTrabajadores = async (nitValue: string) => {
        const trimmed = nitValue.trim();
        setLocalError(null);
        setCedtra('');

        if (!trimmed) {
            lastNitLoaded.current = '';
            setTrabajadores([]);
            setTrabajadorPlaceholder('Ingrese NIT primero');
            return;
        }

        if (trimmed === lastNitLoaded.current && trabajadores.length > 0) {
            return;
        }

        setLoadingTrabajadores(true);
        setTrabajadorPlaceholder('Cargando…');

        try {
            const response = await fetch('/cajas/consulta/trabajadores_por_nit', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ nit: trimmed }),
            });

            const payload = (await response.json().catch(() => null)) as {
                success?: boolean;
                msj?: string;
                trabajadores?: Record<string, string>;
            } | null;

            if (!response.ok || !payload?.success) {
                throw new Error(payload?.msj || 'No se pudo cargar trabajadores.');
            }

            const options = Object.entries(payload.trabajadores || {}).map(([value, label]) => ({
                value,
                label: `${value} — ${label}`,
            }));

            lastNitLoaded.current = trimmed;
            setTrabajadores(options);
            setTrabajadorPlaceholder(options.length ? 'Seleccione' : 'Sin trabajadores');
        } catch (error) {
            lastNitLoaded.current = '';
            setTrabajadores([]);
            setTrabajadorPlaceholder('Sin trabajadores');
            setLocalError(error instanceof Error ? error.message : 'Error al cargar trabajadores.');
        } finally {
            setLoadingTrabajadores(false);
        }
    };

    const handleGenerar = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setLocalError(null);

        const nitValue = nit.trim();
        if (!nitValue || !cedtra || !tipo) {
            setLocalError('Complete NIT, trabajador y tipo de certificado.');
            return;
        }

        setProcessing(true);

        try {
            const body = new FormData();
            body.append('_token', csrfToken());
            body.append('nit', nitValue);
            body.append('cedtra', cedtra);
            body.append('tipo', tipo);

            const response = await fetch('/cajas/consulta/certificado_trabajador', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/pdf',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body,
            });

            if (!response.ok) {
                let msj = `No se pudo generar el certificado (${response.status})`;
                try {
                    const payload = (await response.json()) as { msj?: string };
                    if (payload?.msj) {
                        msj = payload.msj;
                    }
                } catch {
                    // respuesta no JSON
                }
                throw new Error(msj);
            }

            const contentType = response.headers.get('Content-Type') || '';
            if (!contentType.includes('application/pdf')) {
                throw new Error('La respuesta del servidor no es un PDF válido.');
            }

            const blob = await response.blob();
            revokePdf();
            const url = URL.createObjectURL(blob);
            setPdfUrl(url);
            setDialogOpen(true);
        } catch (error) {
            setLocalError(error instanceof Error ? error.message : 'Error al generar el certificado.');
        } finally {
            setProcessing(false);
        }
    };

    const busy = processing || loadingTrabajadores;

    return (
        <AppLayout>
            <LoadingOverlay
                show={busy}
                message={loadingTrabajadores ? 'Cargando trabajadores…' : 'Generando certificado…'}
            />

            <div className={cajasPageClass}>
                <div>
                    <h1 className="text-base font-semibold text-foreground">{title}</h1>
                    <p className="text-sm text-muted-foreground">
                        Ingrese el NIT de la empresa, seleccione el trabajador y el tipo de certificado.
                    </p>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Generar certificado</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <form onSubmit={handleGenerar} className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4" autoComplete="off">
                            <div>
                                <label htmlFor="nit" className={cajasFormLabelClass}>
                                    NIT empresa
                                </label>
                                <input
                                    id="nit"
                                    type="text"
                                    className={cajasFormInputClass}
                                    value={nit}
                                    disabled={busy}
                                    onChange={(e) => setNit(e.target.value)}
                                    onBlur={() => void cargarTrabajadores(nit)}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            e.preventDefault();
                                            void cargarTrabajadores(nit);
                                        }
                                    }}
                                />
                            </div>

                            <div>
                                <label htmlFor="cedtra" className={cajasFormLabelClass}>
                                    Trabajador
                                </label>
                                <CajasSearchSelect
                                    id="cedtra"
                                    value={cedtra}
                                    options={trabajadores}
                                    disabled={busy || trabajadores.length === 0}
                                    placeholder={trabajadorPlaceholder}
                                    onChange={setCedtra}
                                />
                            </div>

                            <div>
                                <label htmlFor="tipo" className={cajasFormLabelClass}>
                                    Tipo
                                </label>
                                <select
                                    id="tipo"
                                    className={cajasFormSelectClass}
                                    value={tipo}
                                    disabled={busy}
                                    onChange={(e) => setTipo(e.target.value)}
                                >
                                    {tipoOptions.map((opt) => (
                                        <option key={opt.value} value={opt.value}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="flex items-end">
                                <button type="submit" className={cajasFormBtnPrimary} disabled={busy}>
                                    {processing ? (
                                        <Loader2 className="mr-1.5 h-4 w-4 animate-spin" />
                                    ) : (
                                        <FileText className="mr-1.5 h-4 w-4" />
                                    )}
                                    Generar certificado
                                </button>
                            </div>
                        </form>

                        {localError && <p className={`${cajasFormErrorClass} mt-3`}>{localError}</p>}
                    </div>
                </div>
            </div>

            <Dialog open={dialogOpen} onOpenChange={handleDialogOpenChange}>
                <DialogContent className="flex max-h-[92vh] flex-col overflow-hidden sm:max-w-5xl">
                    <DialogHeader>
                        <DialogTitle>Certificado / Oficio</DialogTitle>
                        <DialogDescription>Vista previa del PDF generado</DialogDescription>
                    </DialogHeader>
                    {pdfUrl ? (
                        <iframe title="Certificado trabajador" src={pdfUrl} className="h-[75vh] w-full rounded-md border border-border bg-card" />
                    ) : (
                        <p className="text-sm text-muted-foreground">No hay documento para mostrar.</p>
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
