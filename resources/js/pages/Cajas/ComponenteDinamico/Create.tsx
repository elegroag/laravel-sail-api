import { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentForm, ActionButtons } from '@/components/atomic';
import type { Formulario as FormularioType } from '@/types/cajas';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';
import type { ComponentData } from '@/components/atomic/organisms/ComponentForm';

interface Props {
    formulario?: Pick<FormularioType, 'id' | 'name' | 'title'>;
    formularios?: Array<Pick<FormularioType, 'id' | 'name' | 'title'>>;
}

export default function Create({ formulario, formularios = [] }: Props) {
    const { props } = usePage<{ flash?: { success?: string; error?: string } }>();

    const [loading, setLoading] = useState(false);
    const [successOpen, setSuccessOpen] = useState(false);
    const [successMsg, setSuccessMsg] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [formPickerOpen, setFormPickerOpen] = useState(false);
    const [formPickerQuery, setFormPickerQuery] = useState('');
    const [formPickerModule, setFormPickerModule] = useState('');
    const [selectedFormulario, setSelectedFormulario] = useState<Pick<FormularioType, 'id' | 'name' | 'title'> | null>(formulario ?? null);
    const [rows, setRows] = useState<Array<Pick<FormularioType, 'id' | 'name' | 'title'>>>(formularios);
    const [loadingPicker, setLoadingPicker] = useState(false);
    const [page, setPage] = useState(1);
    const [pager, setPager] = useState<{ current_page: number; last_page: number; per_page: number; total: number } | null>(null);

    useEffect(() => {
        if (!formPickerOpen) return;
        const controller = new AbortController();
        const load = async () => {
            setLoadingPicker(true);
            try {
                const params = new URLSearchParams();
                if (formPickerQuery.trim() !== '') params.set('q', formPickerQuery.trim());
                if (formPickerModule.trim() !== '') params.set('module', formPickerModule.trim());
                params.set('per_page', '10');
                params.set('page', String(page));
                const res = await fetch(`/cajas/formulario-dinamico/options?${params.toString()}`, {
                    signal: controller.signal,
                    headers: { Accept: 'application/json' },
                });
                const json = (await res.json()) as { data: Array<Pick<FormularioType, 'id' | 'name' | 'title'>>; meta?: { current_page: number; last_page: number; per_page: number; total: number } };
                setRows(json.data || []);
                if (json.meta) setPager(json.meta);
            } catch {
                // noop
            } finally {
                setLoadingPicker(false);
            }
        };
        load();
        return () => controller.abort();
    }, [formPickerOpen, formPickerQuery, formPickerModule, page]);

     useEffect(() => {
        const msg = props?.flash?.success as string | undefined;
        if (msg && typeof msg === 'string') {
            setSuccessMsg(msg);
            setSuccessOpen(true);
        }
    }, [props]);

    const handleSubmit = async (data: ComponentData) => {
        setLoading(true);
        setErrors({});

        try {
            await router.post('/cajas/componente-dinamico', {
                ...data,
                formulario_id: selectedFormulario ? selectedFormulario.id : null,
            });
        } catch (error: unknown) {
            if (error && typeof error === 'object' && 'response' in error) {
                const axiosError = error as { response?: { data?: { errors?: Record<string, string> } } };
                if (axiosError.response?.data?.errors) {
                    setErrors(axiosError.response.data.errors);
                }
            }
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = () => {
        router.visit('/cajas/componente-dinamico');
    };

    return (
        <AppLayout title="Crear Componente Dinámico">
            <Dialog open={successOpen} onOpenChange={setSuccessOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Actualización exitosa</DialogTitle>
                        <DialogDescription>{successMsg || 'Cambios guardados correctamente.'}</DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <DialogClose asChild>
                            <button
                                type="button"
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Cerrar
                            </button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="bg-white shadow overflow-hidden sm:rounded-md">
                    <div className="px-4 py-5 sm:px-6">
                        <div className="flex justify-between items-center">
                            <div>
                                <h3 className="text-lg leading-6 font-medium text-gray-900">
                                    Crear Nuevo Componente
                                </h3>
                                <p className="mt-1 max-w-2xl text-sm text-gray-500">
                                    Define las propiedades y configuración del componente dinámico
                                </p>
                                {selectedFormulario ? (
                                    <p className="mt-2 text-sm text-blue-600">
                                        Para el formulario: <strong>{selectedFormulario.title}</strong>
                                    </p>
                                ) : (
                                    <p className="mt-2 text-sm text-gray-500">Sin formulario asignado</p>
                                )}
                            </div>
                            <ActionButtons
                                actions={[
                                    {
                                        label: 'Cancelar',
                                        onClick: handleCancel,
                                        variant: 'secondary'
                                    },
                                    {
                                        label: selectedFormulario ? 'Cambiar Formulario' : 'Elegir Formulario',
                                        onClick: () => setFormPickerOpen(true),
                                        variant: 'secondary'
                                    },
                                ]}
                            />
                        </div>
                        {pager && pager.last_page > 1 && (
                            <div className="flex items-center justify-between pt-2">
                                <button
                                    type="button"
                                    disabled={page <= 1 || loadingPicker}
                                    onClick={() => setPage(p => Math.max(1, p - 1))}
                                    className="px-3 py-1 text-sm rounded border bg-white disabled:opacity-50"
                                >
                                    Anterior
                                </button>
                                <span className="text-xs text-gray-500">
                                    Página {pager.current_page} de {pager.last_page}
                                </span>
                                <button
                                    type="button"
                                    disabled={page >= pager.last_page || loadingPicker}
                                    onClick={() => setPage(p => p + 1)}
                                    className="px-3 py-1 text-sm rounded border bg-white disabled:opacity-50"
                                >
                                    Siguiente
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="px-4 py-5 sm:px-6">
                        <ComponentForm
                            initialData={{}}
                            onSubmit={handleSubmit}
                            onCancel={handleCancel}
                            loading={loading}
                            errors={errors}
                        />
                    </div>
                </div>
            </div>
            {/* Picker de formulario */}
            <Dialog open={formPickerOpen} onOpenChange={setFormPickerOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Seleccionar formulario</DialogTitle>
                        <DialogDescription>
                            Busca y selecciona un formulario al que pertenezca este componente.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-3">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input
                                type="text"
                                value={formPickerQuery}
                                onChange={(e) => { setPage(1); setFormPickerQuery(e.target.value); }}
                                placeholder="Buscar por nombre o título..."
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            />
                            <input
                                type="text"
                                value={formPickerModule}
                                onChange={(e) => { setPage(1); setFormPickerModule(e.target.value); }}
                                placeholder="Filtrar por módulo (opcional)"
                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                            />
                        </div>
                        <div className="max-h-64 overflow-auto divide-y divide-gray-200 rounded border">
                            {loadingPicker && (
                                <div className="p-3 text-sm text-gray-500">Cargando...</div>
                            )}
                            {!loadingPicker && rows.map((f) => (
                                <div key={f.id} className="flex items-center justify-between px-3 py-2 hover:bg-gray-50">
                                    <div>
                                        <div className="text-sm font-medium text-gray-900">{f.title}</div>
                                        <div className="text-xs text-gray-500">{f.name}</div>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setSelectedFormulario(f);
                                            setFormPickerOpen(false);
                                        }}
                                        className="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        Seleccionar
                                    </button>
                                </div>
                            ))}
                            {!loadingPicker && rows.length === 0 && (
                                <div className="p-3 text-sm text-gray-500">No se encontraron resultados.</div>
                            )}
                        </div>
                    </div>
                    <DialogFooter>
                        <DialogClose asChild>
                            <button
                                type="button"
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Cerrar
                            </button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
