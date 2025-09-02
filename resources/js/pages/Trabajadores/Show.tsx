import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Trabajador = {
    id: number;
    nombres: string;
    apellidos: string;
    rut: string;
    email: string;
    telefono: string;
    fecha_nacimiento: string;
    genero: string;
    direccion: string;
    cargo: string;
    salario: string;
    fecha_ingreso: string;
    fecha_salida: string;
    estado: string;
    empresa: {
        id: number;
        nombre: string;
    };
    nucleosFamiliares?: any[];
    created_at: string;
    updated_at: string;
};

type Props = {
    trabajador: Trabajador;
};

export default function Show({ trabajador }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm(`¿Estás seguro de que deseas eliminar al trabajador "${trabajador.nombres} ${trabajador.apellidos}"? Esta acción no se puede deshacer.`)) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/api/trabajadores/${trabajador.id}`, {
                onSuccess: () => {
                    router.visit('/web/trabajadores');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar trabajador:', error);
            setDeleting(false);
        }
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return 'No especificada';
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatCurrency = (amount: string | number) => {
        if (!amount) return 'No especificado';
        return new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP'
        }).format(Number(amount));
    };

    const getGeneroText = (genero: string) => {
        const generos = {
            masculino: 'Masculino',
            femenino: 'Femenino',
            otro: 'Otro'
        };
        return generos[genero as keyof typeof generos] || 'No especificado';
    };

    const getEstadoText = (estado: string) => {
        const estados = {
            activo: 'Activo',
            inactivo: 'Inactivo',
            suspendido: 'Suspendido'
        };
        return estados[estado as keyof typeof estados] || estado;
    };

    return (
        <AppLayout title={`Trabajador: ${trabajador.nombres} ${trabajador.apellidos}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles del Trabajador
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa del trabajador
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/web/trabajadores/${trabajador.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/web/trabajadores"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                        >
                            Volver al listado
                        </Link>
                    </div>
                </div>

                <div className="border-t border-gray-200">
                    <dl>
                        {/* Nombres */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Nombres</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.nombres}
                            </dd>
                        </div>

                        {/* Apellidos */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Apellidos</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.apellidos}
                            </dd>
                        </div>

                        {/* RUT */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">RUT</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.rut}
                            </dd>
                        </div>

                        {/* Email */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.email || 'No especificado'}
                            </dd>
                        </div>

                        {/* Teléfono */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.telefono || 'No especificado'}
                            </dd>
                        </div>

                        {/* Fecha de Nacimiento */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de Nacimiento</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(trabajador.fecha_nacimiento)}
                            </dd>
                        </div>

                        {/* Género */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Género</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {getGeneroText(trabajador.genero)}
                            </dd>
                        </div>

                        {/* Empresa */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Empresa</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <Link
                                    href={`/web/empresas/${trabajador.empresa.id}`}
                                    className="text-green-600 hover:text-green-900"
                                >
                                    {trabajador.empresa.nombre}
                                </Link>
                            </dd>
                        </div>

                        {/* Dirección */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.direccion || 'No especificada'}
                            </dd>
                        </div>

                        {/* Cargo */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Cargo</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {trabajador.cargo || 'No especificado'}
                            </dd>
                        </div>

                        {/* Salario */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Salario</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatCurrency(trabajador.salario)}
                            </dd>
                        </div>

                        {/* Fecha de Ingreso */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de Ingreso</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(trabajador.fecha_ingreso)}
                            </dd>
                        </div>

                        {/* Fecha de Salida */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de Salida</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(trabajador.fecha_salida)}
                            </dd>
                        </div>

                        {/* Estado */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estado</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    trabajador.estado === 'activo'
                                        ? 'bg-green-100 text-green-800'
                                        : trabajador.estado === 'suspendido'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800'
                                }`}>
                                    {getEstadoText(trabajador.estado)}
                                </span>
                            </dd>
                        </div>

                        {/* Fecha de creación */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de creación</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(trabajador.created_at)}
                            </dd>
                        </div>

                        {/* Última actualización */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Última actualización</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(trabajador.updated_at)}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Núcleo Familiar */}
                {trabajador.nucleosFamiliares && trabajador.nucleosFamiliares.length > 0 && (
                    <div className="border-t border-gray-200">
                        <div className="px-4 py-5 sm:px-6">
                            <h4 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Núcleo Familiar ({trabajador.nucleosFamiliares.length})
                            </h4>
                            <div className="space-y-3">
                                {trabajador.nucleosFamiliares.map((familiar: any) => (
                                    <div key={familiar.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <div className="text-sm font-medium text-gray-900">
                                                {familiar.nombres} {familiar.apellidos}
                                            </div>
                                            <div className="text-sm text-gray-500">
                                                {familiar.parentesco} • {formatDate(familiar.fecha_nacimiento)}
                                            </div>
                                        </div>
                                        <Link
                                            href={`/web/nucleos-familiares/${familiar.id}`}
                                            className="text-green-600 hover:text-green-900 text-sm font-medium"
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
                            {deleting ? 'Eliminando...' : 'Eliminar Trabajador'}
                        </button>
                        <Link
                            href={`/web/trabajadores/${trabajador.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Trabajador
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
