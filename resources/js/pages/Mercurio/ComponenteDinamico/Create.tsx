import React from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentForm, ActionButtons } from '@/components/atomic';
import { useComponentForm } from '@/hooks/useComponentForm';

interface Props {
    formulario?: {
        id: number;
        name: string;
        title: string;
    };
}

export default function Create({ formulario }: Props) {
    const { formData, errors, loading, updateField, updateDataSource, handleSubmit, isValid } = useComponentForm({
        initialData: formulario ? {
            formulario_id: formulario.id
        } : {},
        onSubmit: async (data) => {
            await router.post('/mercurio/componente-dinamico', data);
        }
    });

    const handleCancel = () => {
        router.visit('/mercurio/componente-dinamico');
    };

    return (
        <AppLayout title="Crear Componente Dinámico">
            <div className="max-w-4xl mx-auto">
                <div className="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div className="flex justify-between items-center">
                            <div>
                                <h3 className="text-lg leading-6 font-medium text-gray-900">
                                    Crear Nuevo Componente
                                </h3>
                                <p className="mt-1 max-w-2xl text-sm text-gray-500">
                                    Define las propiedades y configuración del componente dinámico
                                </p>
                                {formulario && (
                                    <p className="mt-2 text-sm text-blue-600">
                                        Para el formulario: <strong>{formulario.title}</strong>
                                    </p>
                                )}
                            </div>
                            <ActionButtons
                                actions={[
                                    {
                                        label: 'Cancelar',
                                        onClick: handleCancel,
                                        variant: 'secondary'
                                    }
                                ]}
                            />
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="px-4 py-5 sm:px-6">
                        <ComponentForm
                            initialData={formData}
                            onSubmit={handleSubmit}
                            onCancel={handleCancel}
                            loading={loading}
                            errors={errors}
                        />
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
