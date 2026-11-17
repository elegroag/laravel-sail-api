import AppLayout from '@/layouts/AppLayout';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasCheckboxClass,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useEffect, useState } from 'react';

type Option = {
    value: string | number;
    label: string;
};

type DocumentoRow = {
    coddoc: number;
    detalle: string;
    adjunto: boolean;
    obliga: string;
    auto_generado: number;
    nota: string | null;
};

type Props = {
    tipo: {
        tipopc: string;
        detalle: string;
        dias: number | null;
        allows_empresa: boolean;
    };
    tab: 'trabajador' | 'empresa';
    tipsoc: string;
    tipsocOptions: Option[];
    documentos: DocumentoRow[];
};

export default function Documentos({ tipo, tab, tipsoc, tipsocOptions, documentos }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};
    const [toast, setToast] = useState<string | null>(null);
    const [busyKey, setBusyKey] = useState<string | null>(null);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) setToast(success);
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const visitTab = (nextTab: 'trabajador' | 'empresa', nextTipsoc = tipsoc) => {
        router.get(
            `/cajas/mercurio09/${encodeURIComponent(tipo.tipopc)}/documentos`,
            {
                tab: nextTab,
                ...(nextTab === 'empresa' ? { tipsoc: nextTipsoc } : {}),
            },
            { preserveScroll: true, preserveState: false },
        );
    };

    const syncTrabajador = (row: DocumentoRow, payload: { adjunto: boolean; obliga?: string }) => {
        const key = `t-${row.coddoc}`;
        setBusyKey(key);
        router.post(
            `/cajas/mercurio09/${encodeURIComponent(tipo.tipopc)}/documentos/trabajador`,
            {
                coddoc: row.coddoc,
                adjunto: payload.adjunto,
                ...(payload.obliga ? { obliga: payload.obliga } : {}),
            },
            {
                preserveScroll: true,
                onFinish: () => setBusyKey(null),
            },
        );
    };

    const syncEmpresa = (row: DocumentoRow, payload: { adjunto: boolean; obliga?: string }) => {
        if (!tipsoc) return;
        const key = `e-${row.coddoc}`;
        setBusyKey(key);
        router.post(
            `/cajas/mercurio09/${encodeURIComponent(tipo.tipopc)}/documentos/empresa`,
            {
                tipsoc,
                coddoc: row.coddoc,
                adjunto: payload.adjunto,
                ...(payload.obliga ? { obliga: payload.obliga } : {}),
            },
            {
                preserveScroll: true,
                onFinish: () => setBusyKey(null),
            },
        );
    };

    const tabButtonClass = (active: boolean) =>
        `inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
            active
                ? 'bg-cajas-border text-cajas-text-active'
                : 'border border-border text-foreground hover:bg-cajas-border/10'
        }`;

    return (
        <AppLayout title={`Documentos · ${tipo.detalle || tipo.tipopc}`}>
            <div className={cajasPageClass}>
                {toast && (
                    <div className="rounded-md bg-cajas-success px-3 py-2 text-sm text-white shadow-sm">{toast}</div>
                )}
                {formErrors.form && (
                    <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                        {formErrors.form}
                    </div>
                )}

                <section className={cajasCardClass}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between`}>
                        <div>
                            <h1 className="text-sm font-semibold text-foreground">
                                Documentos requeridos · {tipo.tipopc}
                            </h1>
                            <p className="text-xs text-muted-foreground">
                                {tipo.detalle}
                                {tipo.dias !== null ? ` · ${tipo.dias} días` : ''}
                            </p>
                        </div>
                        <Link href="/cajas/mercurio09/index" className={cajasFormBtnSecondary}>
                            <ArrowLeft className="size-4" />
                            Volver
                        </Link>
                    </div>

                    <div className={`${cajasCardBodyClass} space-y-4`}>
                        <div className="flex flex-wrap items-center gap-2">
                            <button type="button" className={tabButtonClass(tab === 'trabajador')} onClick={() => visitTab('trabajador')}>
                                Trabajador
                            </button>
                            {tipo.allows_empresa && (
                                <button type="button" className={tabButtonClass(tab === 'empresa')} onClick={() => visitTab('empresa', tipsoc)}>
                                    Empresa
                                </button>
                            )}
                        </div>

                        {tab === 'empresa' && (
                            <div className="max-w-sm">
                                <label htmlFor="tipsoc" className={cajasFormLabelClass}>
                                    Tipo de sociedad
                                </label>
                                <select
                                    id="tipsoc"
                                    className={cajasFormSelectClass}
                                    value={tipsoc}
                                    onChange={(e) => visitTab('empresa', e.target.value)}
                                >
                                    {tipsocOptions.length === 0 && <option value="">Sin opciones</option>}
                                    {tipsocOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label} ({opt.value})
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}

                        {tab === 'empresa' && !tipsoc ? (
                            <p className={`${cajasFormErrorClass} !mt-0`}>Selecciona un tipo de sociedad para configurar documentos.</p>
                        ) : documentos.length === 0 ? (
                            <p className="py-8 text-center text-sm text-muted-foreground">No hay documentos en el catálogo (mercurio12).</p>
                        ) : (
                            <div className="overflow-hidden rounded-lg border border-border">
                                <table className="min-w-full divide-y divide-border text-sm">
                                    <thead className="bg-muted/40">
                                        <tr>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Documento</th>
                                            <th className="px-3 py-2 text-center font-medium text-muted-foreground">Adjuntar</th>
                                            <th className="px-3 py-2 text-center font-medium text-muted-foreground">Obliga</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {documentos.map((row) => {
                                            const key = `${tab === 'empresa' ? 'e' : 't'}-${row.coddoc}`;
                                            const disabled = busyKey === key;

                                            return (
                                                <tr key={row.coddoc} className="hover:bg-muted/20">
                                                    <td className="px-3 py-3">
                                                        <div className="font-medium text-foreground">{row.detalle}</div>
                                                        <div className="text-xs text-muted-foreground">Código {row.coddoc}</div>
                                                    </td>
                                                    <td className="px-3 py-3 text-center">
                                                        <input
                                                            type="checkbox"
                                                            className={cajasCheckboxClass}
                                                            checked={row.adjunto}
                                                            disabled={disabled}
                                                            onChange={(e) => {
                                                                const adjunto = e.target.checked;
                                                                if (tab === 'empresa') {
                                                                    syncEmpresa(row, { adjunto });
                                                                } else {
                                                                    syncTrabajador(row, { adjunto });
                                                                }
                                                            }}
                                                        />
                                                    </td>
                                                    <td className="px-3 py-3 text-center">
                                                        {row.adjunto ? (
                                                            <input
                                                                type="checkbox"
                                                                className={cajasCheckboxClass}
                                                                checked={row.obliga === 'S'}
                                                                disabled={disabled}
                                                                onChange={(e) => {
                                                                    const obliga = e.target.checked ? 'S' : 'N';
                                                                    if (tab === 'empresa') {
                                                                        syncEmpresa(row, { adjunto: true, obliga });
                                                                    } else {
                                                                        syncTrabajador(row, { adjunto: true, obliga });
                                                                    }
                                                                }}
                                                            />
                                                        ) : (
                                                            <span className="text-xs text-muted-foreground">—</span>
                                                        )}
                                                    </td>
                                                </tr>
                                            );
                                        })}
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
