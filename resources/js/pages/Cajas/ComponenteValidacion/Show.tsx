import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
    validacion: {
        id: number;
        componente_id: number;
        pattern: string | null;
        default_value: string | null;
        max_length: number | null;
        min_length: number | null;
        numeric_range: string | null;
        field_size: number;
        detail_info: string | null;
        is_required: boolean;
        custom_rules: any;
        error_messages: any;
        componente?: {
            id: number;
            name: string;
            label: string;
            type: string;
            group_id: number;
            order: number;
        };
        created_at: string;
        updated_at: string;
    };
};

export default function Show({ validacion }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar estas reglas de validación? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/cajas/componente-validacion/${validacion.id}`, {
                onSuccess: () => {
                    router.visit('/cajas/componente-validacion');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar validación:', error);
            setDeleting(false);
        }
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getTypeColor = (type: string) => {
        switch (type) {
            case 'input': return 'bg-blue-50 text-blue-700 border-blue-200';
            case 'select': return 'bg-green-50 text-green-700 border-green-200';
            case 'textarea': return 'bg-purple-50 text-purple-700 border-purple-200';
            case 'date': return 'bg-orange-50 text-orange-700 border-orange-200';
            case 'number': return 'bg-red-50 text-red-700 border-red-200';
            case 'dialog': return 'bg-gray-50 text-gray-700 border-gray-200';
            default: return 'bg-gray-50 text-gray-700 border-gray-200';
        }
    };

    return (
        <AppLayout title={`Validación: Componente ${validacion.componente_id}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles de la Validación
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa de las reglas de validación
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/cajas/componente-validacion/${validacion.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/cajas/componente-validacion"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Volver al listado
                        </Link>
                    </div>
                </div>

                <div className="border-t border-gray-200">
                    <dl>
                        {/* Componente asociado */}
                        {validacion.componente && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Componente Asociado</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <Link
                                        href={`/cajas/componente-dinamico/${validacion.componente.id}/show`}
                                        className="text-indigo-600 hover:text-indigo-900"
                                    >
                                        {validacion.componente.label}
                                    </Link>
                                    <div className="text-xs text-gray-500 mt-1">
                                        {validacion.componente.name} • Grupo {validacion.componente.group_id} • Orden {validacion.componente.order}
                                    </div>
                                    <div className="mt-2">
                                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${getTypeColor(validacion.componente.type)}`}>
                                            {validacion.componente.type}
                                        </span>
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Estado requerido */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estado</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    validacion.is_required ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                                }`}>
                                    {validacion.is_required ? 'Requerido' : 'Opcional'}
                                </span>
                            </dd>
                        </div>

                        {/* Patrón regex */}
                        {validacion.pattern && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Patrón Regex</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="p-2 bg-gray-50 rounded text-xs font-mono text-gray-700 break-all">
                                        {validacion.pattern}
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Longitudes */}
                        {(validacion.max_length || validacion.min_length) && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Longitud de Caracteres</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="space-y-1">
                                        {validacion.min_length && <div>Mínimo: {validacion.min_length} caracteres</div>}
                                        {validacion.max_length && <div>Máximo: {validacion.max_length} caracteres</div>}
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Rango numérico */}
                        {validacion.numeric_range && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Rango Numérico</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {validacion.numeric_range}
                                </dd>
                            </div>
                        )}

                        {/* Tamaño del campo */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Tamaño del Campo</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {validacion.field_size}
                            </dd>
                        </div>

                        {/* Valor por defecto */}
                        {validacion.default_value && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Valor por Defecto</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {validacion.default_value}
                                </dd>
                            </div>
                        )}

                        {/* Información detallada */}
                        {validacion.detail_info && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Información Detallada</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {validacion.detail_info}
                                </dd>
                            </div>
                        )}

                        {/* Fechas */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fechas</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div>Creado: {formatDate(validacion.created_at)}</div>
                                <div>Actualizado: {formatDate(validacion.updated_at)}</div>
                            </dd>
                        </div>

                        {/* ID */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">ID</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {validacion.id}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Reglas personalizadas */}
                {validacion.custom_rules && Object.keys(validacion.custom_rules).length > 0 && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Reglas Personalizadas ({Object.keys(validacion.custom_rules).length})
                            </h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {Object.entries(validacion.custom_rules).map(([key, value]: [string, any]) => (
                                    <div key={key} className="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div className="text-sm font-medium text-gray-900">{key}</div>
                                            <div className="text-sm text-gray-600">{String(value)}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {/* Mensajes de error */}
                {validacion.error_messages && Object.keys(validacion.error_messages).length > 0 && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Mensajes de Error Personalizados ({Object.keys(validacion.error_messages).length})
                            </h4>
                            <div className="space-y-3">
                                {Object.entries(validacion.error_messages).map(([key, value]: [string, any]) => (
                                    <div key={key} className="p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <div className="text-sm font-medium text-red-900">{key}</div>
                                        <div className="text-sm text-red-700 mt-1">{String(value)}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {/* Botones de acción */}
                <div className="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div className="flex justify-between">
                        <button
                            onClick={handleDelete}
                            disabled={deleting}
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {deleting ? 'Eliminando...' : 'Eliminar Validación'}
                        </button>
                        <Link
                            href={`/cajas/componente-validacion/${validacion.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Validación
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
