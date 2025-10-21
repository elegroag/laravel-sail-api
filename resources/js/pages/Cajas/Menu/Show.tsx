import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
    empresa: {
        id: number;
        nombre: string;
        rut: string;
        direccion: string;
        telefono: string;
        email: string;
        sector_economico: string;
        numero_empleados: number;
        descripcion: string;
        estado: string;
        trabajadores?: any[];
        created_at: string;
        updated_at: string;
    };
};

export default function Show({ empresa }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar esta empresa? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/api/empresas/${empresa.id}`, {
                onSuccess: () => {
                    router.visit('/web/empresas');
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
        <AppLayout title={`Empresa: ${empresa.nombre}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles de Empresa
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa de la empresa
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/web/empresas/${empresa.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/web/empresas"
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
                            <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.nombre}
                            </dd>
                        </div>

                        {/* RUT */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">RUT</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.rut}
                            </dd>
                        </div>

                        {/* Dirección */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.direccion}
                            </dd>
                        </div>

                        {/* Teléfono */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.telefono || 'No especificado'}
                            </dd>
                        </div>

                        {/* Email */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.email || 'No especificado'}
                            </dd>
                        </div>

                        {/* Sector Económico */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Sector Económico</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.sector_economico || 'No especificado'}
                            </dd>
                        </div>

                        {/* Número de Empleados */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Número de Empleados</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {empresa.numero_empleados || 0}
                            </dd>
                        </div>

                        {/* Estado */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estado</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    empresa.estado === 'activa'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                }`}>
                                    {empresa.estado === 'activa' ? 'Activa' : 'Inactiva'}
                                </span>
                            </dd>
                        </div>

                        {/* Descripción */}
                        {empresa.descripcion && (
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Descripción</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {empresa.descripcion}
                                </dd>
                            </div>
                        )}

                        {/* Fecha de creación */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de creación</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(empresa.created_at)}
                            </dd>
                        </div>

                        {/* Última actualización */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Última actualización</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(empresa.updated_at)}
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
                            {deleting ? 'Eliminando...' : 'Eliminar Empresa'}
                        </button>
                        <Link
                            href={`/web/empresas/${empresa.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Empresa
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
