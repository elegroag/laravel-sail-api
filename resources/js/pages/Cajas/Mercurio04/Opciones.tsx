import AppLayout from '@/layouts/AppLayout';
import {
    cajasActionDangerClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { useEffect, useState, type FormEvent } from 'react';

type Option = {
    value: string | number;
    label: string;
};

type Asignacion = {
    codofi: string;
    tipopc: string;
    tipopc_detalle: string;
    usuario: number;
    usuario_nombre: string;
    orden: number;
};

type Props = {
    oficina: {
        codofi: string;
        detalle: string;
        principal: string;
        estado: string;
    };
    tipopcOptions: Option[];
    usuarioOptions: Option[];
    asignaciones: Asignacion[];
};

export default function Opciones({ oficina, tipopcOptions, usuarioOptions, asignaciones }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [tipopc, setTipopc] = useState('');
    const [usuario, setUsuario] = useState('');
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
            setTipopc('');
            setUsuario('');
        }
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (!tipopc || !usuario) return;

        setProcessing(true);
        router.post(
            `/cajas/mercurio04/${encodeURIComponent(oficina.codofi)}/opciones`,
            {
                tipopc,
                usuario: Number(usuario),
            },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    const handleDelete = (row: Asignacion) => {
        if (!confirm(`¿Quitar a ${row.usuario_nombre || row.usuario} del tipo ${row.tipopc}?`)) return;

        router.delete(`/cajas/mercurio04/${encodeURIComponent(oficina.codofi)}/opciones`, {
            data: {
                tipopc: row.tipopc,
                usuario: row.usuario,
            },
            preserveScroll: true,
        });
    };

    return (
        <AppLayout title={`Opciones · ${oficina.detalle || oficina.codofi}`}>
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
                                Capturar opciones · {oficina.codofi}
                            </h1>
                            <p className="text-xs text-muted-foreground">
                                {oficina.detalle} · Asigna funcionarios a tipos de trámite en esta oficina
                            </p>
                        </div>
                        <Link href="/cajas/mercurio04/index" className={cajasFormBtnSecondary}>
                            <ArrowLeft className="size-4" />
                            Volver
                        </Link>
                    </div>

                    <div className={`${cajasCardBodyClass} space-y-6`}>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-3 md:grid-cols-[1fr_1fr_auto] md:items-end">
                            <div>
                                <label htmlFor="tipopc" className={cajasFormLabelClass}>
                                    Tipo de opción *
                                </label>
                                <select
                                    id="tipopc"
                                    className={cajasFormSelectClass}
                                    value={tipopc}
                                    onChange={(e) => setTipopc(e.target.value)}
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {tipopcOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label} ({opt.value})
                                        </option>
                                    ))}
                                </select>
                                {formErrors.tipopc && <p className={cajasFormErrorClass}>{formErrors.tipopc}</p>}
                            </div>

                            <div>
                                <label htmlFor="usuario" className={cajasFormLabelClass}>
                                    Usuario *
                                </label>
                                <select
                                    id="usuario"
                                    className={cajasFormSelectClass}
                                    value={usuario}
                                    onChange={(e) => setUsuario(e.target.value)}
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    {usuarioOptions.map((opt) => (
                                        <option key={String(opt.value)} value={String(opt.value)}>
                                            {opt.label}
                                        </option>
                                    ))}
                                </select>
                                {formErrors.usuario && <p className={cajasFormErrorClass}>{formErrors.usuario}</p>}
                            </div>

                            <button type="submit" className={`${cajasFormBtnPrimary} justify-center`} disabled={processing}>
                                <Plus className="size-4" />
                                {processing ? 'Guardando...' : 'Adicionar'}
                            </button>
                        </form>

                        {asignaciones.length === 0 ? (
                            <p className="py-8 text-center text-sm text-muted-foreground">
                                No hay asignaciones para esta oficina.
                            </p>
                        ) : (
                            <div className="overflow-x-auto rounded-lg border border-border">
                                <table className="min-w-full divide-y divide-border text-sm">
                                    <thead className="bg-muted/40">
                                        <tr>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Tipo</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Usuario</th>
                                            <th className="px-3 py-2 text-left font-medium text-muted-foreground">Orden</th>
                                            <th className="px-3 py-2 text-right font-medium text-muted-foreground">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {asignaciones.map((row) => (
                                            <tr key={`${row.tipopc}-${row.usuario}`} className="hover:bg-muted/20">
                                                <td className="px-3 py-2.5 text-foreground">
                                                    <div className="font-medium">{row.tipopc_detalle || row.tipopc}</div>
                                                    <div className="text-xs text-muted-foreground">{row.tipopc}</div>
                                                </td>
                                                <td className="px-3 py-2.5 text-foreground">
                                                    <div className="font-medium">{row.usuario_nombre || row.usuario}</div>
                                                    <div className="text-xs text-muted-foreground">{row.usuario}</div>
                                                </td>
                                                <td className="px-3 py-2.5 text-muted-foreground">{row.orden}</td>
                                                <td className="px-3 py-2.5">
                                                    <div className="flex justify-end">
                                                        <button type="button" onClick={() => handleDelete(row)} className={cajasActionDangerClass}>
                                                            <Trash2 className="size-3.5" />
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </td>
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
