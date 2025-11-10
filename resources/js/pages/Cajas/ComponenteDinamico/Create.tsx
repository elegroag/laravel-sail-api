import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentForm, ActionButtons } from '@/components/atomic';

interface Props {
    formulario?: {
        id: number;
        name: string;
        title: string;
    };
}

export default function Create({ formulario }: Props) {
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = async (data: any) => {
        setLoading(true);
        setErrors({});

        try {
            await router.post('/cajas/componente-dinamico', data as any);
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
        </AppLayout>
    );
}
