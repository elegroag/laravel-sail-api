import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';

type Props = {
    empresas: {
        data: any[];
        meta: {
            total_empresas: number;
            empresas_activas: number;
            empresas_inactivas: number;
            total_empleados: number;
        };
    };
};

export default function Index({ empresas }: Props) {
    const { data, meta } = empresas;

    const handleDelete = async (empresaId: number, empresaNombre: string) => {
        if (!confirm(`¿Estás seguro de que deseas eliminar la empresa "${empresaNombre}"? Esta acción no se puede deshacer.`)) {
            return;
        }

        try {
            await router.delete(`/api/empresas/${empresaId}`, {
                onSuccess: () => {
                    // La página se recargará automáticamente con los datos actualizados
                },
                onError: () => {
                    alert('Error al eliminar la empresa. Por favor, inténtalo de nuevo.');
                }
            });
        } catch (error) {
            console.error('Error al eliminar empresa:', error);
            alert('Error al eliminar la empresa. Por favor, inténtalo de nuevo.');
        }
    };

    return (
        <AppLayout title="Empresas">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Empresas Registradas
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Lista de todas las empresas en el sistema
                        </p>
                    </div>
                    <Link
                        href="/web/empresas/create"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Nueva Empresa
                    </Link>
                </div>

                {/* Estadísticas */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-indigo-600">{meta.total_empresas}</div>
                            <div className="text-sm text-gray-500">Total Empresas</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">{meta.empresas_activas}</div>
                            <div className="text-sm text-gray-500">Activas</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-red-600">{meta.empresas_inactivas}</div>
                            <div className="text-sm text-gray-500">Inactivas</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{meta.total_empleados}</div>
                            <div className="text-sm text-gray-500">Total Empleados</div>
                        </div>
                    </div>
                </div>

                {/* Lista de empresas */}
                <ul className="divide-y divide-gray-200">
                    {data.map((empresa) => (
                        <li key={empresa.id}>
                            <div className="px-4 py-4 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <div className="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                <span className="text-sm font-medium text-white">
                                                    {empresa.nombre.charAt(0).toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="ml-4">
                                            <div className="flex items-center">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {empresa.nombre}
                                                </div>
                                                <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    empresa.estado === 'activa'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {empresa.estado}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                RUT: {empresa.rut} • {empresa.sector_economico || 'Sin sector definido'}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {empresa.direccion}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <div className="text-right">
                                            <div className="text-sm font-medium text-gray-900">
                                                {empresa.total_trabajadores} empleados
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {empresa.trabajadores_activos} activos
                                            </div>
                                        </div>
                                        <div className="flex space-x-2">
                                            <Link
                                                href={`/web/empresas/${empresa.id}`}
                                                className="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                href={`/web/empresas/${empresa.id}/edit`}
                                                className="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                onClick={() => handleDelete(empresa.id, empresa.nombre)}
                                                className="text-red-600 hover:text-red-900 text-sm font-medium"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {empresa.descripcion && (
                                    <div className="mt-2">
                                        <p className="text-sm text-gray-600">{empresa.descripcion}</p>
                                    </div>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>

                {data.length === 0 && (
                    <div className="text-center py-12">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay empresas</h3>
                        <p className="mt-1 text-sm text-gray-500">Comienza creando una nueva empresa.</p>
                        <div className="mt-6">
                            <Link
                                href="/web/empresas/create"
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                Nueva Empresa
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
