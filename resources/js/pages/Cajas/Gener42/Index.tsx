import AppLayout from '@/layouts/AppLayout';
import {
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasCheckboxClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Search, Shield } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';

type UsuarioOption = {
    value: number | string;
    label: string;
};

type Permiso = {
    codigo: string;
    detalle: string;
    orden: number | null;
};

type Props = {
    usuarios: UsuarioOption[];
    usuario: number | string | null;
    asignados: Permiso[];
    disponibles: Permiso[];
};

function PermissionList({
    title,
    items,
    selected,
    onToggle,
    onToggleAll,
    search,
    onSearch,
    emptyLabel,
}: {
    title: string;
    items: Permiso[];
    selected: Set<string>;
    onToggle: (codigo: string) => void;
    onToggleAll: (checked: boolean) => void;
    search: string;
    onSearch: (value: string) => void;
    emptyLabel: string;
}) {
    const allChecked = items.length > 0 && items.every((item) => selected.has(item.codigo));
    const someChecked = items.some((item) => selected.has(item.codigo));

    return (
        <section className={`${cajasCardClass} flex min-h-[28rem] flex-col`}>
            <div className={cajasCardHeaderClass}>
                <h2 className="text-sm font-semibold text-foreground">{title}</h2>
                <p className="text-xs text-muted-foreground">{items.length} permisos</p>
            </div>

            <div className="space-y-3 border-b border-border px-4 py-3 sm:px-5">
                <label className="inline-flex items-center gap-2 text-sm text-foreground">
                    <input
                        type="checkbox"
                        className={cajasCheckboxClass}
                        checked={allChecked}
                        ref={(el) => {
                            if (el) el.indeterminate = !allChecked && someChecked;
                        }}
                        onChange={(e) => onToggleAll(e.target.checked)}
                        disabled={items.length === 0}
                    />
                    Seleccionar todo
                </label>
                <div className="relative">
                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="search"
                        className={`${cajasFormInputClass} pl-9`}
                        placeholder="Buscar permiso..."
                        value={search}
                        onChange={(e) => onSearch(e.target.value)}
                    />
                </div>
            </div>

            <div className={`${cajasCardBodyClass} flex-1 overflow-y-auto !pt-0`}>
                {items.length === 0 ? (
                    <div className="flex h-full min-h-48 flex-col items-center justify-center text-center">
                        <Shield className="mb-2 size-8 text-muted-foreground/50" />
                        <p className="text-sm text-muted-foreground">{emptyLabel}</p>
                    </div>
                ) : (
                    <ul className="divide-y divide-border">
                        {items.map((item) => (
                            <li key={item.codigo}>
                                <label className="flex cursor-pointer items-start gap-3 py-3 transition-colors hover:bg-muted/20">
                                    <input
                                        type="checkbox"
                                        className={`${cajasCheckboxClass} mt-0.5`}
                                        checked={selected.has(item.codigo)}
                                        onChange={() => onToggle(item.codigo)}
                                    />
                                    <span className="min-w-0">
                                        <span className="block text-sm font-medium text-foreground">{item.detalle}</span>
                                        <span className="mt-0.5 block text-xs text-muted-foreground">{item.codigo}</span>
                                    </span>
                                </label>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </section>
    );
}

export default function Index({ usuarios, usuario, asignados, disponibles }: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [selectedUsuario, setSelectedUsuario] = useState(usuario !== null && usuario !== undefined ? String(usuario) : '');
    const [selectedDisponibles, setSelectedDisponibles] = useState<Set<string>>(new Set());
    const [selectedAsignados, setSelectedAsignados] = useState<Set<string>>(new Set());
    const [searchDisponibles, setSearchDisponibles] = useState('');
    const [searchAsignados, setSearchAsignados] = useState('');
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        setSelectedUsuario(usuario !== null && usuario !== undefined ? String(usuario) : '');
        setSelectedDisponibles(new Set());
        setSelectedAsignados(new Set());
        setSearchDisponibles('');
        setSearchAsignados('');
    }, [usuario, asignados, disponibles]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) {
            setToast(success);
        }
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    const filteredDisponibles = useMemo(() => {
        const q = searchDisponibles.trim().toLowerCase();
        if (!q) return disponibles;
        return disponibles.filter(
            (item) => item.detalle.toLowerCase().includes(q) || item.codigo.toLowerCase().includes(q),
        );
    }, [disponibles, searchDisponibles]);

    const filteredAsignados = useMemo(() => {
        const q = searchAsignados.trim().toLowerCase();
        if (!q) return asignados;
        return asignados.filter(
            (item) => item.detalle.toLowerCase().includes(q) || item.codigo.toLowerCase().includes(q),
        );
    }, [asignados, searchAsignados]);

    const toggleInSet = (set: Set<string>, codigo: string): Set<string> => {
        const next = new Set(set);
        if (next.has(codigo)) {
            next.delete(codigo);
        } else {
            next.add(codigo);
        }
        return next;
    };

    const handleUsuarioChange = (value: string) => {
        setSelectedUsuario(value);
        if (!value) {
            router.get('/cajas/gener42/index', {}, { preserveState: false, preserveScroll: true });
            return;
        }

        router.get(
            '/cajas/gener42/index',
            { usuario: value },
            { preserveState: false, preserveScroll: true },
        );
    };

    const assignSelected = () => {
        if (!selectedUsuario || selectedDisponibles.size === 0) return;

        setProcessing(true);
        router.post(
            '/cajas/gener42/assign',
            {
                usuario: Number(selectedUsuario),
                permisos: Array.from(selectedDisponibles),
            },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    const revokeSelected = () => {
        if (!selectedUsuario || selectedAsignados.size === 0) return;

        setProcessing(true);
        router.post(
            '/cajas/gener42/revoke',
            {
                usuario: Number(selectedUsuario),
                permisos: Array.from(selectedAsignados),
            },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <AppLayout title="Permisos de usuario">
            <div className={cajasPageClass}>
                <section className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h1 className="text-sm font-semibold text-foreground">Asignación de permisos</h1>
                        <p className="text-xs text-muted-foreground">Selecciona un usuario y mueve permisos entre las listas</p>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <div className="mx-auto max-w-xl">
                            <label htmlFor="usuario" className={cajasFormLabelClass}>
                                Usuario
                            </label>
                            <select
                                id="usuario"
                                className={cajasFormSelectClass}
                                value={selectedUsuario}
                                onChange={(e) => handleUsuarioChange(e.target.value)}
                            >
                                <option value="">Seleccione un usuario</option>
                                {usuarios.map((opt) => (
                                    <option key={String(opt.value)} value={String(opt.value)}>
                                        {opt.label}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {(formErrors.form || formErrors.usuario || formErrors.permisos) && (
                            <p className={`${cajasFormErrorClass} mt-3 text-center`}>
                                {formErrors.form || formErrors.usuario || formErrors.permisos}
                            </p>
                        )}
                    </div>
                </section>

                {!selectedUsuario ? (
                    <section className={cajasCardClass}>
                        <div className={`${cajasCardBodyClass} flex flex-col items-center justify-center py-16 text-center`}>
                            <Shield className="mb-3 size-10 text-muted-foreground/50" />
                            <p className="text-sm text-muted-foreground">Selecciona un usuario para gestionar sus permisos.</p>
                        </div>
                    </section>
                ) : (
                    <div className="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-[1fr_auto_1fr]">
                        <PermissionList
                            title="No permite"
                            items={filteredDisponibles}
                            selected={selectedDisponibles}
                            onToggle={(codigo) => setSelectedDisponibles((prev) => toggleInSet(prev, codigo))}
                            onToggleAll={(checked) =>
                                setSelectedDisponibles(
                                    checked
                                        ? new Set(filteredDisponibles.map((item) => item.codigo))
                                        : new Set(),
                                )
                            }
                            search={searchDisponibles}
                            onSearch={setSearchDisponibles}
                            emptyLabel="No hay permisos disponibles"
                        />

                        <div className="flex flex-row items-center justify-center gap-2 lg:flex-col">
                            <button
                                type="button"
                                className={`${cajasFormBtnPrimary} w-full justify-center lg:w-36`}
                                onClick={assignSelected}
                                disabled={processing || selectedDisponibles.size === 0}
                            >
                                Agregar
                                <ArrowRight className="size-4" />
                            </button>
                            <button
                                type="button"
                                className={`${cajasFormBtnSecondary} w-full justify-center lg:w-36`}
                                onClick={revokeSelected}
                                disabled={processing || selectedAsignados.size === 0}
                            >
                                <ArrowLeft className="size-4" />
                                Quitar
                            </button>
                        </div>

                        <PermissionList
                            title="Permite"
                            items={filteredAsignados}
                            selected={selectedAsignados}
                            onToggle={(codigo) => setSelectedAsignados((prev) => toggleInSet(prev, codigo))}
                            onToggleAll={(checked) =>
                                setSelectedAsignados(
                                    checked ? new Set(filteredAsignados.map((item) => item.codigo)) : new Set(),
                                )
                            }
                            search={searchAsignados}
                            onSearch={setSearchAsignados}
                            emptyLabel="Este usuario no tiene permisos asignados"
                        />
                    </div>
                )}

                {toast && (
                    <div className="fixed right-4 bottom-4 z-50 max-w-[360px] min-w-[260px] rounded-lg bg-cajas-success px-4 py-3 text-sm text-white shadow-lg">
                        {toast}
                        <button type="button" className="ml-3 text-white/90 underline hover:text-white" onClick={() => setToast(null)}>
                            Cerrar
                        </button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
