import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';

type Props = {
    trabajadores: {
        data: any[];
        meta: {
            total_trabajadores: number;
            trabajadores_activos: number;
            trabajadores_inactivos: number;
            salario_promedio: number;
            salario_total: number;
            distribucion_genero: {
                masculino: number;
                femenino: number;
                otro: number;
            };
        };
    };
};

export default function Index({ trabajadores }: Props) {
    const { data, meta } = trabajadores;

    const handleDelete = async (trabajadorId: number, trabajadorNombre: string) => {
        if (!confirm(`¿Estás seguro de que deseas eliminar al trabajador "${trabajadorNombre}"? Esta acción no se puede deshacer.`)) {
            return;
        }

        try {
            await router.delete(`/api/trabajadores/${trabajadorId}`, {
                onSuccess: () => {
                    // La página se recargará automáticamente con los datos actualizados
                },
                onError: () => {
                    alert('Error al eliminar el trabajador. Por favor, inténtalo de nuevo.');
                }
            });
        } catch (error) {
            console.error('Error al eliminar trabajador:', error);
            alert('Error al eliminar el trabajador. Por favor, inténtalo de nuevo.');
        }
    };

    return (
        <AppLayout title="Trabajadores">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Trabajadores Registrados
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Lista de todos los trabajadores en el sistema
                        </p>
                    </div>
                    <Link
                        href="/web/trabajadores/create"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                    >
                        Nuevo Trabajador
                    </Link>
                </div>

                {/* Estadísticas */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-5">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">{meta.total_trabajadores}</div>
                            <div className="text-sm text-gray-500">Total</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{meta.trabajadores_activos}</div>
                            <div className="text-sm text-gray-500">Activos</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-red-600">{meta.trabajadores_inactivos}</div>
                            <div className="text-sm text-gray-500">Inactivos</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-purple-600">
                                ${Math.round(meta.salario_promedio || 0).toLocaleString()}
                            </div>
                            <div className="text-sm text-gray-500">Salario Promedio</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-indigo-600">
                                ${Math.round(meta.salario_total || 0).toLocaleString()}
                            </div>
                            <div className="text-sm text-gray-500">Nómina Total</div>
                        </div>
                    </div>
                </div>

                {/* Distribución por género */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h4 className="text-sm font-medium text-gray-900 mb-2">Distribución por Género</h4>
                    <div className="grid grid-cols-3 gap-4">
                        <div className="text-center">
                            <div className="text-lg font-semibold text-blue-600">{meta.distribucion_genero.masculino}</div>
                            <div className="text-xs text-gray-500">Masculino</div>
                        </div>
                        <div className="text-center">
                            <div className="text-lg font-semibold text-pink-600">{meta.distribucion_genero.femenino}</div>
                            <div className="text-xs text-gray-500">Femenino</div>
                        </div>
                        <div className="text-center">
                            <div className="text-lg font-semibold text-gray-600">{meta.distribucion_genero.otro}</div>
                            <div className="text-xs text-gray-500">Otro</div>
                        </div>
                    </div>
                </div>

                {/* Lista de trabajadores */}
                <ul className="divide-y divide-gray-200">
                    {data.map((trabajador) => (
                        <li key={trabajador.id}>
                            <div className="px-4 py-4 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <div className="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center">
                                                <span className="text-sm font-medium text-white">
                                                    {trabajador.nombres.charAt(0).toUpperCase()}{trabajador.apellidos.charAt(0).toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="ml-4">
                                            <div className="flex items-center">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {trabajador.nombre_completo}
                                                </div>
                                                <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    trabajador.estado === 'activo'
                                                        ? 'bg-green-100 text-green-800'
                                                        : trabajador.estado === 'inactivo'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-yellow-100 text-yellow-800'
                                                }`}>
                                                    {trabajador.estado}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {trabajador.cargo} • {trabajador.empresa?.nombre || 'Sin empresa'}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                RUT: {trabajador.rut} • {trabajador.email}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                Edad: {trabajador.edad} años • Antigüedad: {Math.floor((trabajador.antiguedad_dias || 0) / 365)} años
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-4">
                                        <div className="text-right">
                                            <div className="text-sm font-medium text-gray-900">
                                                {trabajador.salario_formateado}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {trabajador.total_familiares || 0} familiares
                                            </div>
                                        </div>
                                        <div className="flex space-x-2">
                                            <Link
                                                href={`/web/trabajadores/${trabajador.id}`}
                                                className="text-green-600 hover:text-green-900 text-sm font-medium"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                href={`/web/trabajadores/${trabajador.id}/edit`}
                                                className="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                onClick={() => handleDelete(trabajador.id, `${trabajador.nombres} ${trabajador.apellidos}`)}
                                                className="text-red-600 hover:text-red-900 text-sm font-medium"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    ))}
                </ul>

                {data.length === 0 && (
                    <div className="text-center py-12">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay trabajadores</h3>
                        <p className="mt-1 text-sm text-gray-500">Comienza creando un nuevo trabajador.</p>
                        <div className="mt-6">
                            <Link
                                href="/web/trabajadores/create"
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                            >
                                Nuevo Trabajador
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
