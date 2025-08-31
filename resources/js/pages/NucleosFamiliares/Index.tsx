import AppLayout from '@/layouts/app-layout';
import { Link } from '@inertiajs/react';

type Props = {
    nucleos_familiares: {
        data: any[];
        meta: {
            total_familiares: number;
            dependientes_economicos: number;
            independientes_economicos: number;
            edad_promedio: number;
            distribucion_parentesco: {
                [key: string]: number;
            };
        };
    };
};

export default function Index({ nucleos_familiares }: Props) {
    const { data, meta } = nucleos_familiares;

    return (
        <AppLayout title="Núcleos Familiares">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Núcleos Familiares Registrados
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Lista de todos los familiares de trabajadores en el sistema
                        </p>
                    </div>
                    <Link
                        href="/nucleos-familiares/create"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
                    >
                        Nuevo Familiar
                    </Link>
                </div>

                {/* Estadísticas */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-purple-600">{meta.total_familiares}</div>
                            <div className="text-sm text-gray-500">Total Familiares</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">{meta.dependientes_economicos}</div>
                            <div className="text-sm text-gray-500">Dependientes</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{meta.independientes_economicos}</div>
                            <div className="text-sm text-gray-500">Independientes</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-indigo-600">
                                {Math.round(meta.edad_promedio || 0)}
                            </div>
                            <div className="text-sm text-gray-500">Edad Promedio</div>
                        </div>
                    </div>
                </div>

                {/* Distribución por parentesco */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h4 className="text-sm font-medium text-gray-900 mb-2">Distribución por Parentesco</h4>
                    <div className="grid grid-cols-4 gap-2 text-xs">
                        {Object.entries(meta.distribucion_parentesco).map(([parentesco, cantidad]) => (
                            <div key={parentesco} className="text-center">
                                <div className="font-semibold text-purple-600">{cantidad}</div>
                                <div className="text-gray-500 capitalize">{parentesco}</div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Lista de núcleos familiares */}
                <ul className="divide-y divide-gray-200">
                    {data.map((familiar) => (
                        <li key={familiar.id}>
                            <div className="px-4 py-4 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <div className="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                                <span className="text-sm font-medium text-white">
                                                    {familiar.nombres.charAt(0).toUpperCase()}{familiar.apellidos.charAt(0).toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="ml-4">
                                            <div className="flex items-center">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {familiar.nombre_completo}
                                                </div>
                                                <span className={`ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    familiar.dependiente_economico
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-blue-100 text-blue-800'
                                                }`}>
                                                    {familiar.es_dependiente}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {familiar.parentesco_formateado} • {familiar.edad} años
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                RUT: {familiar.rut}
                                            </div>
                                            {familiar.trabajador && (
                                                <div className="text-sm text-gray-500">
                                                    Trabajador: {familiar.trabajador.nombre_completo}
                                                </div>
                                            )}
                                            {familiar.empresa_trabajador && (
                                                <div className="text-sm text-gray-500">
                                                    Empresa: {familiar.empresa_trabajador.nombre_empresa}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-4">
                                        <div className="text-right">
                                            {familiar.ocupacion && (
                                                <div className="text-sm font-medium text-gray-900">
                                                    {familiar.ocupacion}
                                                </div>
                                            )}
                                            {familiar.estado_civil_formateado && (
                                                <div className="text-sm text-gray-500">
                                                    {familiar.estado_civil_formateado}
                                                </div>
                                            )}
                                            <div className="text-sm text-gray-500">
                                                {familiar.genero}
                                            </div>
                                        </div>
                                        <div className="flex space-x-2">
                                            <Link
                                                href={`/nucleos-familiares/${familiar.id}`}
                                                className="text-purple-600 hover:text-purple-900 text-sm font-medium"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                href={`/nucleos-familiares/${familiar.id}/edit`}
                                                className="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                            >
                                                Editar
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                {familiar.email && (
                                    <div className="mt-2 ml-14">
                                        <p className="text-sm text-gray-600">Email: {familiar.email}</p>
                                    </div>
                                )}
                                {familiar.telefono && (
                                    <div className="mt-1 ml-14">
                                        <p className="text-sm text-gray-600">Teléfono: {familiar.telefono}</p>
                                    </div>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>

                {data.length === 0 && (
                    <div className="text-center py-12">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No hay núcleos familiares</h3>
                        <p className="mt-1 text-sm text-gray-500">Comienza agregando un familiar de un trabajador.</p>
                        <div className="mt-6">
                            <Link
                                href="/nucleos-familiares/create"
                                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
                            >
                                Nuevo Familiar
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
