import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
    componente: {
        id: number;
        name: string;
        type: string;
        label: string;
        placeholder: string | null;
        form_type: string;
        group_id: number;
        order: number;
        default_value: string | null;
        is_disabled: boolean;
        is_readonly: boolean;
        data_source: any[] | null;
        css_classes: string | null;
        help_text: string | null;
        target: number;
        event_config: any;
        search_type: string | null;
        date_max: string | null;
        number_min: number | null;
        number_max: number | null;
        number_step: number;
        validacion?: {
            id: number;
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
        };
        formulario?: {
            id: number;
            name: string;
            title: string;
        };
        created_at: string;
        updated_at: string;
    };
};

export default function Show({ componente }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar este componente dinámico? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/cajas/componente-dinamico/${componente.id}`, {
                onSuccess: () => {
                    router.visit('/cajas/componente-dinamico');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar componente:', error);
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
        <AppLayout title={`Componente: ${componente.label}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles del Componente Dinámico
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa del componente dinámico
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/cajas/componente-dinamico/${componente.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/cajas/componente-dinamico"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Volver al listado
                        </Link>
                    </div>
                </div>

                <div className="border-t border-gray-200">
                    <dl>
                        {/* Nombre */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Nombre único</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {componente.name}
                            </dd>
                        </div>

                        {/* Tipo */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Tipo</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${getTypeColor(componente.type)}`}>
                                    {componente.type}
                                </span>
                            </dd>
                        </div>

                        {/* Etiqueta */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Etiqueta</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {componente.label}
                            </dd>
                        </div>

                        {/* Placeholder */}
                        {componente.placeholder && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Placeholder</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.placeholder}
                                </dd>
                            </div>
                        )}

                        {/* Grupo y Orden */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Grupo / Orden</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Grupo {componente.group_id} • Orden {componente.order}
                            </dd>
                        </div>

                        {/* Estados */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estados</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div className="flex gap-4">
                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        componente.is_disabled ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                                    }`}>
                                        {componente.is_disabled ? 'Deshabilitado' : 'Habilitado'}
                                    </span>
                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        componente.is_readonly ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'
                                    }`}>
                                        {componente.is_readonly ? 'Solo lectura' : 'Editable'}
                                    </span>
                                </div>
                            </dd>
                        </div>

                        {/* Valor por defecto */}
                        {componente.default_value && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Valor por defecto</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.default_value}
                                </dd>
                            </div>
                        )}

                        {/* Data Source para Select */}
                        {componente.type === 'select' && componente.data_source && componente.data_source.length > 0 && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Opciones del Select</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="space-y-1">
                                        {componente.data_source.map((option: any, index: number) => (
                                            <div key={index} className="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                <span className="font-medium">{option.label}</span>
                                                <span className="text-xs text-gray-500">({option.value})</span>
                                            </div>
                                        ))}
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Configuración específica por tipo */}
                        {componente.type === 'date' && componente.date_max && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Fecha máxima</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.date_max}
                                </dd>
                            </div>
                        )}

                        {componente.type === 'number' && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Configuración numérica</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="space-y-1">
                                        {componente.number_min !== null && <div>Mínimo: {componente.number_min}</div>}
                                        {componente.number_max !== null && <div>Máximo: {componente.number_max}</div>}
                                        <div>Incremento: {componente.number_step}</div>
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Texto de ayuda */}
                        {componente.help_text && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Texto de ayuda</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.help_text}
                                </dd>
                            </div>
                        )}

                        {/* Formulario asociado */}
                        {componente.formulario && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Formulario asociado</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <Link
                                        href={`/cajas/formulario-dinamico/${componente.formulario.id}/show`}
                                        className="text-indigo-600 hover:text-indigo-900"
                                    >
                                        {componente.formulario.title}
                                    </Link>
                                </dd>
                            </div>
                        )}

                        {/* Fechas */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fechas</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div>Creado: {formatDate(componente.created_at)}</div>
                                <div>Actualizado: {formatDate(componente.updated_at)}</div>
                            </dd>
                        </div>

                        {/* ID */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">ID</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {componente.id}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Validación */}
                {componente.validacion && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Configuración de Validación
                            </h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm font-medium text-gray-900">Estado</span>
                                        <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
                                            componente.validacion.is_required ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'
                                        }`}>
                                            {componente.validacion.is_required ? 'Requerido' : 'Opcional'}
                                        </span>
                                    </div>

                                    {componente.validacion.pattern && (
                                        <div>
                                            <span className="text-sm font-medium text-gray-900">Patrón</span>
                                            <div className="mt-1 p-2 bg-gray-50 rounded text-xs font-mono text-gray-700 break-all">
                                                {componente.validacion.pattern}
                                            </div>
                                        </div>
                                    )}

                                    {(componente.validacion.max_length || componente.validacion.min_length) && (
                                        <div>
                                            <span className="text-sm font-medium text-gray-900">Longitud</span>
                                            <div className="mt-1 text-sm text-gray-600">
                                                {componente.validacion.min_length && `Mín: ${componente.validacion.min_length}`}
                                                {componente.validacion.min_length && componente.validacion.max_length && ' • '}
                                                {componente.validacion.max_length && `Máx: ${componente.validacion.max_length}`}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <div className="space-y-4">
                                    {componente.validacion.custom_rules && Object.keys(componente.validacion.custom_rules).length > 0 && (
                                        <div>
                                            <span className="text-sm font-medium text-gray-900">Reglas Personalizadas</span>
                                            <div className="mt-1 space-y-1">
                                                {Object.entries(componente.validacion.custom_rules).map(([key, value]: [string, any]) => (
                                                    <div key={key} className="text-xs text-gray-600">
                                                        <span className="font-medium">{key}:</span> {String(value)}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {componente.validacion.error_messages && Object.keys(componente.validacion.error_messages).length > 0 && (
                                        <div>
                                            <span className="text-sm font-medium text-gray-900">Mensajes de Error</span>
                                            <div className="mt-1 space-y-1">
                                                {Object.entries(componente.validacion.error_messages).map(([key, value]: [string, any]) => (
                                                    <div key={key} className="text-xs text-red-600 bg-red-50 p-2 rounded">
                                                        <span className="font-medium">{key}:</span> {String(value)}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {componente.validacion.detail_info && (
                                        <div>
                                            <span className="text-sm font-medium text-gray-900">Información Adicional</span>
                                            <div className="mt-1 text-sm text-gray-600">
                                                {componente.validacion.detail_info}
                                            </div>
                                        </div>
                                    )}
                                </div>
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
                            {deleting ? 'Eliminando...' : 'Eliminar Componente'}
                        </button>
                        <Link
                            href={`/cajas/componente-dinamico/${componente.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Componente
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
