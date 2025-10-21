import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
    menu_item: {
        id: number;
        title: string;
        default_url: string | null;
        icon: string | null;
        color: string | null;
        nota: string | null;
        parent_id: number | null;
        codapl: string;
        controller: string;
        action: string;
        is_visible?: number | null;
        tipo?: string | null;
        position?: number | null;
    };
};

export default function Show({ menu_item }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar este item de menú? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/cajas/menu/${menu_item.id}`, {
                onSuccess: () => {
                    router.visit('/cajas/menu');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar empresa:', error);
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
        <AppLayout title={`Menu Item: ${menu_item.title}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles de Menu Item
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa del item de menú
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/cajas/menu/${menu_item.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/cajas/menu"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Volver al listado
                        </Link>
                    </div>
                </div>

                <div className="border-t border-gray-200">
                    <dl>
                        {/* Título */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Título</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.title}
                            </dd>
                        </div>

                        {/* Controller / Action */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Controller / Action</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.controller} / {menu_item.action}
                            </dd>
                        </div>

                        {/* URL por defecto */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">URL por defecto</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.default_url || 'N/A'}
                            </dd>
                        </div>

                        {/* Icono / Color */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Icono / Color</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {(menu_item.icon || 'N/A')} / {(menu_item.color || 'N/A')}
                            </dd>
                        </div>

                        {/* Padre */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Padre</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.parent_id ?? 'N/A'}
                            </dd>
                        </div>

                        {/* Aplicación */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Aplicación</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.codapl}
                            </dd>
                        </div>

                        {/* Visible / Tipo / Posición */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Visible / Tipo / Posición</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {(menu_item.is_visible ?? 'N/A')} / {(menu_item.tipo ?? 'N/A')} / {(menu_item.position ?? 'N/A')}
                            </dd>
                        </div>

                        {/* Nota */}
                        {menu_item.nota && (
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Nota</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {menu_item.nota}
                                </dd>
                            </div>
                        )}

                        {/* Descripción */}
                        {empresa.descripcion && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Descripción</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {empresa.descripcion}
                                </dd>
                            </div>
                        )}

                        {/* Identificador */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">ID</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {menu_item.id}
                            </dd>
                        </div>

                        {/* Controller.Action para permisos */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Permiso</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {`${menu_item.controller}.${menu_item.action}`}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Trabajadores asociados (si existen) */}
                {empresa.trabajadores && empresa.trabajadores.length > 0 && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Trabajadores Asociados ({empresa.trabajadores.length})
                            </h4>
                            <div className="space-y-3">
                                {empresa.trabajadores.map((trabajador: any) => (
                                    <div key={trabajador.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <div className="text-sm font-medium text-gray-900">
                                                {trabajador.nombre}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {trabajador.cargo}
                                            </div>
                                        </div>
                                        <Link
                                            href={`/web/trabajadores/${trabajador.id}`}
                                            className="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                        >
                                            Ver detalles
                                        </Link>
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
                            {deleting ? 'Eliminando...' : 'Eliminar Item'}
                        </button>
                        <Link
                            href={`/cajas/menu/${menu_item.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Item
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
