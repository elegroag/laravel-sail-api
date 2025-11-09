import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
    formulario: {
        id: number;
        name: string;
        title: string;
        description: string | null;
        module: string;
        endpoint: string;
        method: string;
        is_active: boolean;
        layout_config: any;
        permissions: any;
        componentes?: any[];
        created_at: string;
        updated_at: string;
    };
};

export default function Show({ formulario }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar este formulario dinámico? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/mercurio/formulario-dinamico/${formulario.id}`, {
                onSuccess: () => {
                    router.visit('/mercurio/formulario-dinamico');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar formulario:', error);
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

    return (
        <AppLayout title={`Formulario: ${formulario.title}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles del Formulario Dinámico
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa del formulario dinámico
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/mercurio/formulario-dinamico/${formulario.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/mercurio/formulario-dinamico"
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
                                {formulario.name}
                            </dd>
                        </div>

                        {/* Título */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Título</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formulario.title}
                            </dd>
                        </div>

                        {/* Descripción */}
                        {formulario.description && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Descripción</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {formulario.description}
                                </dd>
                            </div>
                        )}

                        {/* Módulo */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Módulo</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formulario.module}
                            </dd>
                        </div>

                        {/* Endpoint y Método */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Endpoint / Método</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formulario.endpoint} <span className="text-xs text-gray-500">({formulario.method})</span>
                            </dd>
                        </div>

                        {/* Estado */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estado</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    formulario.is_active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                }`}>
                                    {formulario.is_active ? 'Activo' : 'Inactivo'}
                                </span>
                            </dd>
                        </div>

                        {/* Configuración de Layout */}
                        {formulario.layout_config && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Configuración de Layout</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="space-y-1">
                                        <div>Columnas: {formulario.layout_config.columns || 'N/A'}</div>
                                        <div>Espaciado: {formulario.layout_config.spacing || 'N/A'}</div>
                                        <div>Tema: {formulario.layout_config.theme || 'N/A'}</div>
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Permisos */}
                        {formulario.permissions && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Permisos</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="space-y-1">
                                        <div>Público: {formulario.permissions.public ? 'Sí' : 'No'}</div>
                                        {!formulario.permissions.public && formulario.permissions.roles && (
                                            <div>Roles: {formulario.permissions.roles.join(', ') || 'Ninguno'}</div>
                                        )}
                                    </div>
                                </dd>
                            </div>
                        )}

                        {/* Fechas */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fechas</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div>Creado: {formatDate(formulario.created_at)}</div>
                                <div>Actualizado: {formatDate(formulario.updated_at)}</div>
                            </dd>
                        </div>

                        {/* ID */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">ID</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formulario.id}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Componentes asociados */}
                {formulario.componentes && formulario.componentes.length > 0 && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Componentes Asociados ({formulario.componentes.length})
                            </h4>
                            <div className="space-y-3">
                                {formulario.componentes.map((componente: any) => (
                                    <div key={componente.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div className="flex items-center space-x-3">
                                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-semibold">
                                                {componente.label?.charAt(0)?.toUpperCase() || componente.name?.charAt(0)?.toUpperCase()}
                                            </div>
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {componente.label || componente.name}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {componente.type} • Grupo {componente.group_id} • Orden {componente.order}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ${
                                                componente.type === 'input' ? 'bg-blue-50 text-blue-700' :
                                                componente.type === 'select' ? 'bg-green-50 text-green-700' :
                                                componente.type === 'textarea' ? 'bg-purple-50 text-purple-700' :
                                                componente.type === 'date' ? 'bg-orange-50 text-orange-700' :
                                                componente.type === 'number' ? 'bg-red-50 text-red-700' :
                                                'bg-gray-50 text-gray-700'
                                            }`}>
                                                {componente.type}
                                            </span>
                                            <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ${
                                                componente.is_required ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'
                                            }`}>
                                                {componente.is_required ? 'Req' : 'Opt'}
                                            </span>
                                        </div>
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
                            {deleting ? 'Eliminando...' : 'Eliminar Formulario'}
                        </button>
                        <Link
                            href={`/mercurio/formulario-dinamico/${formulario.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Formulario
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
