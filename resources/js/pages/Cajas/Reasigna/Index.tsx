import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout';
import LoadingOverlay from '@/pages/Cajas/components/LoadingOverlay';
import {
    cajasActionLinkClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormErrorClass,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { router, usePage } from '@inertiajs/react';
import {
    ChevronFirst,
    ChevronLast,
    ChevronLeft,
    ChevronRight,
    Loader2,
    Search,
    UserRoundPen,
} from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

const paginationButtonClass =
    'inline-flex h-9 items-center rounded-md border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-cajas-border/10 hover:border-cajas-border/40 focus:outline-none focus:ring-2 focus:ring-cajas-border disabled:cursor-not-allowed disabled:opacity-50';

const paginationIconButtonClass = `${paginationButtonClass} justify-center px-2.5`;

type Option = { value: string; label: string };

type Solicitud = {
    id: number | string;
    documento: string;
    nombre: string;
};

type Props = {
    tipopcOptions: Option[];
    tipopcMasivoOptions: Option[];
    usuariosOptions: Option[];
    usuariosByTipopc: Record<string, Option[]>;
    filters: {
        accion: 'C' | 'P';
        tipopc: string;
        usuario: string;
    };
    solicitudes: Solicitud[];
    queried: boolean;
};

export default function Index({
    tipopcOptions,
    tipopcMasivoOptions,
    usuariosOptions,
    usuariosByTipopc,
    filters,
    solicitudes,
    queried,
}: Props) {
    const page = usePage<{ flash?: { success?: string }; errors?: Record<string, string> }>();
    const formErrors = page.props.errors ?? {};

    const [accion, setAccion] = useState<'C' | 'P'>(filters.accion || 'C');
    const [tipopc, setTipopc] = useState(filters.tipopc);
    const [usuario, setUsuario] = useState(filters.usuario);
    const [q, setQ] = useState('');
    const [processing, setProcessing] = useState(false);
    const [localError, setLocalError] = useState<string | null>(null);
    const [toast, setToast] = useState<string | null>(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(25);

    const [proceso, setProceso] = useState({
        tipopc: '',
        usuori: '',
        usudes: '',
        fecini: '',
        fecfin: '',
    });

    const [dialogOpen, setDialogOpen] = useState(false);
    const [selected, setSelected] = useState<Solicitud | null>(null);
    const [usuarioRea, setUsuarioRea] = useState('');

    useEffect(() => {
        setAccion(filters.accion || 'C');
        setTipopc(filters.tipopc);
        setUsuario(filters.usuario);
    }, [filters.accion, filters.tipopc, filters.usuario]);

    useEffect(() => {
        const success = page.props.flash?.success;
        if (success) setToast(success);
    }, [page.props.flash?.success]);

    useEffect(() => {
        if (!toast) return;
        const timer = window.setTimeout(() => setToast(null), 3500);
        return () => window.clearTimeout(timer);
    }, [toast]);

    useEffect(() => {
        setCurrentPage(1);
    }, [q, solicitudes, filters.tipopc, filters.usuario]);

    const filteredRows = useMemo(() => {
        const term = q.trim().toLowerCase();
        if (!term) return solicitudes;
        return solicitudes.filter((row) =>
            [row.id, row.documento, row.nombre].some((value) => String(value).toLowerCase().includes(term)),
        );
    }, [q, solicitudes]);

    const totalPages = Math.max(1, Math.ceil(filteredRows.length / perPage) || 1);
    const safePage = Math.min(Math.max(currentPage, 1), totalPages);
    const pagedRows = useMemo(() => {
        const start = (safePage - 1) * perPage;
        return filteredRows.slice(start, start + perPage);
    }, [filteredRows, safePage, perPage]);
    const from = filteredRows.length === 0 ? 0 : (safePage - 1) * perPage + 1;
    const to = Math.min(safePage * perPage, filteredRows.length);

    const pageNumbers = useMemo(() => {
        const windowSize = 5;
        let start = Math.max(1, safePage - Math.floor(windowSize / 2));
        let end = Math.min(totalPages, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }, [safePage, totalPages]);

    const modalUsuarios = useMemo(() => {
        const key = tipopc || filters.tipopc;
        return usuariosByTipopc[key] ?? usuariosOptions;
    }, [usuariosByTipopc, tipopc, filters.tipopc, usuariosOptions]);

    const visitConsulta = (params: { tipopc: string; usuario: string }) => {
        router.get(
            '/cajas/reasigna/index',
            { accion: 'C', ...params },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onCancel: () => setProcessing(false),
                onError: () => setProcessing(false),
            },
        );
    };

    const handleAccionChange = (next: 'C' | 'P') => {
        setAccion(next);
        setLocalError(null);
        router.get(
            '/cajas/reasigna/index',
            { accion: next },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    const handleConsulta = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);
        if (!tipopc || !usuario) {
            setLocalError('Seleccione tipo de opción y usuario.');
            return;
        }
        visitConsulta({ tipopc, usuario });
    };

    const handleProceso = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);

        if (!proceso.tipopc || !proceso.usuori || !proceso.usudes || !proceso.fecini || !proceso.fecfin) {
            setLocalError('Complete todos los campos del proceso.');
            return;
        }

        router.post('/cajas/reasigna/proceso_reasignar_masivo', proceso, {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onCancel: () => setProcessing(false),
            onError: () => setProcessing(false),
            onSuccess: () => {
                setProceso((prev) => ({ ...prev, fecini: '', fecfin: '' }));
            },
        });
    };

    const openDialog = (row: Solicitud) => {
        setSelected(row);
        setUsuarioRea('');
        setDialogOpen(true);
    };

    const submitCambioUsuario = (event: FormEvent) => {
        event.preventDefault();
        if (!selected || !usuarioRea) {
            setLocalError('Seleccione el nuevo responsable.');
            return;
        }

        router.post(
            '/cajas/reasigna/cambiar_usuario',
            {
                tipopc: tipopc || filters.tipopc,
                id: selected.id,
                usuario: usuarioRea,
            },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onCancel: () => setProcessing(false),
                onError: () => setProcessing(false),
                onSuccess: () => {
                    setDialogOpen(false);
                    setSelected(null);
                    visitConsulta({ tipopc: tipopc || filters.tipopc, usuario: usuario || filters.usuario });
                },
            },
        );
    };

    return (
        <AppLayout title="Reasignación">
            <LoadingOverlay show={processing} message={accion === 'P' ? 'Procesando reasignación…' : 'Consultando solicitudes…'} />

            {toast && (
                <div className="fixed right-4 bottom-4 z-50 max-w-[360px] min-w-[260px] rounded-lg bg-cajas-success px-4 py-3 text-sm text-white shadow-lg">
                    {toast}
                </div>
            )}

            <div className={`${cajasPageClass} ${processing ? 'pointer-events-none select-none' : ''}`}>
                <div>
                    <h1 className="text-base font-semibold text-foreground">Reasignación</h1>
                    <p className="text-sm text-muted-foreground">Consulta y proceso de cambio de responsable</p>
                </div>

                <div className={cajasCardClass}>
                    <div className={cajasCardHeaderClass}>
                        <h2 className="text-sm font-semibold text-foreground">Acción</h2>
                    </div>
                    <div className={cajasCardBodyClass}>
                        <div className="max-w-xs">
                            <label htmlFor="accion" className={cajasFormLabelClass}>
                                Modo
                            </label>
                            <select
                                id="accion"
                                className={cajasFormSelectClass}
                                value={accion}
                                disabled={processing}
                                onChange={(e) => handleAccionChange(e.target.value as 'C' | 'P')}
                            >
                                <option value="C">Consulta</option>
                                <option value="P">Proceso</option>
                            </select>
                        </div>
                    </div>
                </div>

                {accion === 'C' ? (
                    <>
                        <div className={cajasCardClass}>
                            <div className={cajasCardHeaderClass}>
                                <h2 className="text-sm font-semibold text-foreground">Consulta</h2>
                            </div>
                            <div className={cajasCardBodyClass}>
                                <form onSubmit={handleConsulta} className="grid grid-cols-1 gap-4 md:grid-cols-3" autoComplete="off">
                                    <div>
                                        <label htmlFor="tipopc" className={cajasFormLabelClass}>
                                            Tipo opción
                                        </label>
                                        <select
                                            id="tipopc"
                                            className={cajasFormSelectClass}
                                            value={tipopc}
                                            disabled={processing}
                                            onChange={(e) => setTipopc(e.target.value)}
                                        >
                                            <option value="">Seleccione</option>
                                            {tipopcOptions.map((opt) => (
                                                <option key={opt.value} value={opt.value}>
                                                    {opt.label}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label htmlFor="usuario" className={cajasFormLabelClass}>
                                            Usuario
                                        </label>
                                        <select
                                            id="usuario"
                                            className={cajasFormSelectClass}
                                            value={usuario}
                                            disabled={processing}
                                            onChange={(e) => setUsuario(e.target.value)}
                                        >
                                            <option value="">Seleccione</option>
                                            {(usuariosByTipopc[tipopc] ?? usuariosOptions).map((opt) => (
                                                <option key={opt.value} value={opt.value}>
                                                    {opt.label}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="flex items-end">
                                        <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                            {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : <Search className="mr-1.5 h-4 w-4" />}
                                            Consultar
                                        </button>
                                    </div>
                                </form>
                                {(localError || formErrors.form) && (
                                    <p className={`${cajasFormErrorClass} mt-3`}>{localError || formErrors.form}</p>
                                )}
                            </div>
                        </div>

                        <div className={cajasCardClass}>
                            <div className={`${cajasCardHeaderClass} flex flex-wrap items-center justify-between gap-3`}>
                                <div>
                                    <h2 className="text-sm font-semibold text-foreground">Solicitudes</h2>
                                    <p className="text-xs text-muted-foreground">
                                        {queried
                                            ? `${filteredRows.length} registro${filteredRows.length === 1 ? '' : 's'}`
                                            : 'Ejecute una consulta para ver resultados'}
                                    </p>
                                </div>
                                {queried && (
                                    <div className="flex flex-wrap items-center gap-3">
                                        <div className="flex items-center gap-2">
                                            <label htmlFor="per_page" className="text-xs text-muted-foreground">
                                                Por página
                                            </label>
                                            <select
                                                id="per_page"
                                                className={`${cajasFormSelectClass} w-auto min-w-[4.5rem]`}
                                                value={perPage}
                                                onChange={(e) => {
                                                    setPerPage(Number(e.target.value) || 25);
                                                    setCurrentPage(1);
                                                }}
                                            >
                                                {[10, 25, 50, 100].map((n) => (
                                                    <option key={n} value={n}>
                                                        {n}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="relative min-w-[220px] flex-1 sm:max-w-xs">
                                            <Search className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <input
                                                type="search"
                                                className={`${cajasFormInputClass} pl-9`}
                                                placeholder="Buscar…"
                                                value={q}
                                                onChange={(e) => setQ(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>
                            <div className={`${cajasCardBodyClass} space-y-4`}>
                                <div className="overflow-x-auto -mx-4 sm:-mx-5">
                                    {!queried ? (
                                        <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">Seleccione filtros y pulse Consultar.</p>
                                    ) : filteredRows.length === 0 ? (
                                        <p className="px-4 py-6 text-sm text-muted-foreground sm:px-5">No hay solicitudes.</p>
                                    ) : (
                                        <table className="min-w-full text-left text-sm">
                                            <thead className="border-b border-border bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                                                <tr>
                                                    <th className="px-4 py-3 font-medium sm:px-5">Id</th>
                                                    <th className="px-4 py-3 font-medium sm:px-5">Documento</th>
                                                    <th className="px-4 py-3 font-medium sm:px-5">Nombre</th>
                                                    <th className="px-4 py-3 font-medium sm:px-5">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody key={`reasigna-page-${safePage}-${perPage}`}>
                                                {pagedRows.map((row) => (
                                                    <tr key={`${safePage}-${row.id}`} className="border-b border-border/70">
                                                        <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.id}</td>
                                                        <td className="px-4 py-3 whitespace-nowrap sm:px-5">{row.documento}</td>
                                                        <td className="px-4 py-3 sm:px-5">{row.nombre}</td>
                                                        <td className="px-4 py-3 whitespace-nowrap sm:px-5">
                                                            <button type="button" className={cajasActionLinkClass} onClick={() => openDialog(row)}>
                                                                <UserRoundPen className="h-3.5 w-3.5" />
                                                                Cambiar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    )}
                                </div>

                                {queried && filteredRows.length > 0 && (
                                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <p className="text-xs text-muted-foreground">
                                            Mostrando {from}–{to} de {filteredRows.length}
                                        </p>
                                        {totalPages > 1 && (
                                            <div className="flex flex-wrap items-center gap-1.5">
                                                <button type="button" className={paginationIconButtonClass} disabled={safePage <= 1} onClick={() => setCurrentPage(1)}>
                                                    <ChevronFirst className="size-4" />
                                                </button>
                                                <button
                                                    type="button"
                                                    className={paginationIconButtonClass}
                                                    disabled={safePage <= 1}
                                                    onClick={() => setCurrentPage(safePage - 1)}
                                                >
                                                    <ChevronLeft className="size-4" />
                                                </button>
                                                {pageNumbers.map((n) => (
                                                    <button
                                                        key={n}
                                                        type="button"
                                                        className={`${paginationButtonClass} ${n === safePage ? 'border-cajas-border bg-cajas-border/10' : ''}`}
                                                        onClick={() => setCurrentPage(n)}
                                                    >
                                                        {n}
                                                    </button>
                                                ))}
                                                <button
                                                    type="button"
                                                    className={paginationIconButtonClass}
                                                    disabled={safePage >= totalPages}
                                                    onClick={() => setCurrentPage(safePage + 1)}
                                                >
                                                    <ChevronRight className="size-4" />
                                                </button>
                                                <button
                                                    type="button"
                                                    className={paginationIconButtonClass}
                                                    disabled={safePage >= totalPages}
                                                    onClick={() => setCurrentPage(totalPages)}
                                                >
                                                    <ChevronLast className="size-4" />
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </>
                ) : (
                    <div className={cajasCardClass}>
                        <div className={cajasCardHeaderClass}>
                            <h2 className="text-sm font-semibold text-foreground">Proceso masivo</h2>
                        </div>
                        <div className={cajasCardBodyClass}>
                            <form onSubmit={handleProceso} className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" autoComplete="off">
                                <div>
                                    <label htmlFor="tipopc_proceso" className={cajasFormLabelClass}>
                                        Tipo opción
                                    </label>
                                    <select
                                        id="tipopc_proceso"
                                        className={cajasFormSelectClass}
                                        value={proceso.tipopc}
                                        disabled={processing}
                                        onChange={(e) => setProceso((prev) => ({ ...prev, tipopc: e.target.value }))}
                                    >
                                        <option value="">Seleccione</option>
                                        {tipopcMasivoOptions.map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.tipopc && <p className={cajasFormErrorClass}>{formErrors.tipopc}</p>}
                                </div>
                                <div>
                                    <label htmlFor="usuori" className={cajasFormLabelClass}>
                                        Usuario origen
                                    </label>
                                    <select
                                        id="usuori"
                                        className={cajasFormSelectClass}
                                        value={proceso.usuori}
                                        disabled={processing}
                                        onChange={(e) => setProceso((prev) => ({ ...prev, usuori: e.target.value }))}
                                    >
                                        <option value="">Seleccione</option>
                                        {(usuariosByTipopc[proceso.tipopc] ?? usuariosOptions).map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.usuori && <p className={cajasFormErrorClass}>{formErrors.usuori}</p>}
                                </div>
                                <div>
                                    <label htmlFor="usudes" className={cajasFormLabelClass}>
                                        Usuario destino
                                    </label>
                                    <select
                                        id="usudes"
                                        className={cajasFormSelectClass}
                                        value={proceso.usudes}
                                        disabled={processing}
                                        onChange={(e) => setProceso((prev) => ({ ...prev, usudes: e.target.value }))}
                                    >
                                        <option value="">Seleccione</option>
                                        {(usuariosByTipopc[proceso.tipopc] ?? usuariosOptions).map((opt) => (
                                            <option key={opt.value} value={opt.value}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.usudes && <p className={cajasFormErrorClass}>{formErrors.usudes}</p>}
                                </div>
                                <div>
                                    <label htmlFor="fecini" className={cajasFormLabelClass}>
                                        Fecha inicial
                                    </label>
                                    <input
                                        id="fecini"
                                        type="date"
                                        className={cajasFormInputClass}
                                        value={proceso.fecini}
                                        disabled={processing}
                                        onChange={(e) => setProceso((prev) => ({ ...prev, fecini: e.target.value }))}
                                    />
                                    {formErrors.fecini && <p className={cajasFormErrorClass}>{formErrors.fecini}</p>}
                                </div>
                                <div>
                                    <label htmlFor="fecfin" className={cajasFormLabelClass}>
                                        Fecha final
                                    </label>
                                    <input
                                        id="fecfin"
                                        type="date"
                                        className={cajasFormInputClass}
                                        value={proceso.fecfin}
                                        disabled={processing}
                                        onChange={(e) => setProceso((prev) => ({ ...prev, fecfin: e.target.value }))}
                                    />
                                    {formErrors.fecfin && <p className={cajasFormErrorClass}>{formErrors.fecfin}</p>}
                                </div>
                                <div className="flex items-end">
                                    <button type="submit" className={cajasFormBtnPrimary} disabled={processing}>
                                        {processing ? <Loader2 className="mr-1.5 h-4 w-4 animate-spin" /> : null}
                                        Reasignar
                                    </button>
                                </div>
                            </form>
                            {(localError || formErrors.form) && (
                                <p className={`${cajasFormErrorClass} mt-3`}>{localError || formErrors.form}</p>
                            )}
                        </div>
                    </div>
                )}
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Cambio de responsable</DialogTitle>
                        <DialogDescription>
                            Solicitud #{selected?.id} · {selected?.documento} · {selected?.nombre}
                        </DialogDescription>
                    </DialogHeader>
                    <form onSubmit={submitCambioUsuario} className="space-y-4">
                        <div>
                            <label htmlFor="usuario_rea" className={cajasFormLabelClass}>
                                Lista de usuarios disponibles
                            </label>
                            <select
                                id="usuario_rea"
                                className={cajasFormSelectClass}
                                value={usuarioRea}
                                onChange={(e) => setUsuarioRea(e.target.value)}
                            >
                                <option value="">Seleccione</option>
                                {modalUsuarios.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <DialogFooter>
                            <button type="button" className={cajasFormBtnSecondary} onClick={() => setDialogOpen(false)} disabled={processing}>
                                Cancelar
                            </button>
                            <button type="submit" className={cajasFormBtnPrimary} disabled={processing || !usuarioRea}>
                                {processing ? 'Guardando…' : 'Cambiar usuario'}
                            </button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
