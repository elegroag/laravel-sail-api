import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentForm, ActionButtons } from '@/components/atomic';
import type { Formulario as FormularioType } from '@/types/cajas';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';

interface Props {
    formulario?: Pick<FormularioType, 'id' | 'name' | 'title'>;
    formularios?: Array<Pick<FormularioType, 'id' | 'name' | 'title'>>;
}

// Tipado local alineado con ComponentForm (estructura esperada)
type ComponentData = {
    name: string;
    type: string;
    label: string;
    placeholder: string;
    form_type: string;
    group_id: number;
    order: number;
    default_value: string;
    is_disabled: boolean;
    is_readonly: boolean;
    data_source: Array<{ value: string; label: string }>;
    css_classes: string;
    help_text: string;
    target: number;
    event_config: Record<string, string>;
    search_type: string;
    date_max: string;
    number_min: number;
    number_max: number;
    number_step: number;
};

export default function Create({ formulario, formularios = [] }: Props) {
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [formPickerOpen, setFormPickerOpen] = useState(false);
    const [formPickerQuery, setFormPickerQuery] = useState('');
    const [selectedFormulario, setSelectedFormulario] = useState<Pick<FormularioType, 'id' | 'name' | 'title'> | null>(formulario ?? null);

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
            <div className="max-w-4xl mx-auto">
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
                                        variant: 'default'
                                    },
                                ]}
                            />
                        </div>
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
                        <input
                            type="text"
                            value={formPickerQuery}
                            onChange={(e) => setFormPickerQuery(e.target.value)}
                            placeholder="Buscar por nombre o título..."
                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                        />
                        <div className="max-h-64 overflow-auto divide-y divide-gray-200 rounded border">
                            {formularios && formularios
                                .filter(f => {
                                    const q = formPickerQuery.toLowerCase();
                                    return !q || f.name.toLowerCase().includes(q) || f.title.toLowerCase().includes(q);
                                })
                                .map((f) => (
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
                            {(!formularios || formularios.length === 0) && (
                                <div className="p-3 text-sm text-gray-500">No hay formularios cargados en esta vista.</div>
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
