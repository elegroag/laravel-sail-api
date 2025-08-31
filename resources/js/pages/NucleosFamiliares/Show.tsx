import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type NucleoFamiliar = {
    id: number;
    nombres: string;
    apellidos: string;
    rut: string;
    fecha_nacimiento: string;
    genero: string;
    parentesco: string;
    telefono: string;
    email: string;
    direccion: string;
    estado_civil: string;
    ocupacion: string;
    dependiente_economico: boolean;
    trabajador: {
        id: number;
        nombres: string;
        apellidos: string;
        empresa: {
            nombre: string;
        };
    };
    created_at: string;
    updated_at: string;
};

type Props = {
    nucleo_familiar: NucleoFamiliar;
};

export default function Show({ nucleo_familiar }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm(`¿Estás seguro de que deseas eliminar a "${nucleo_familiar.nombres} ${nucleo_familiar.apellidos}" del núcleo familiar? Esta acción no se puede deshacer.`)) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/api/nucleos-familiares/${nucleo_familiar.id}`, {
                onSuccess: () => {
                    router.visit('/web/nucleos-familiares');
                },
                onError: () => {
                    setDeleting(false);
                }
            });
        } catch (error) {
            console.error('Error al eliminar núcleo familiar:', error);
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

    const getGeneroText = (genero: string) => {
        const generos = {
            masculino: 'Masculino',
            femenino: 'Femenino',
            otro: 'Otro'
        };
        return generos[genero as keyof typeof generos] || 'No especificado';
    };

    const getParentescoText = (parentesco: string) => {
        const parentescos = {
            conyuge: 'Cónyuge',
            hijo: 'Hijo/a',
            padre: 'Padre',
            madre: 'Madre',
            hermano: 'Hermano/a',
            otro: 'Otro'
        };
        return parentescos[parentesco as keyof typeof parentescos] || parentesco;
    };

    const getEstadoCivilText = (estado_civil: string) => {
        const estados = {
            soltero: 'Soltero/a',
            casado: 'Casado/a',
            divorciado: 'Divorciado/a',
            viudo: 'Viudo/a'
        };
        return estados[estado_civil as keyof typeof estados] || 'No especificado';
    };

    return (
        <AppLayout title={`Núcleo Familiar: ${nucleo_familiar.nombres} ${nucleo_familiar.apellidos}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Detalles del Familiar
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Información completa del miembro del núcleo familiar
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link
                            href={`/web/nucleos-familiares/${nucleo_familiar.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar
                        </Link>
                        <Link
                            href="/web/nucleos-familiares"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
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
                                {nucleo_familiar.nombres}
                            </dd>
                        </div>

                        {/* Apellidos */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Apellidos</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.apellidos}
                            </dd>
                        </div>

                        {/* RUT */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">RUT</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.rut || 'No especificado'}
                            </dd>
                        </div>

                        {/* Fecha de Nacimiento */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de Nacimiento</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(nucleo_familiar.fecha_nacimiento)}
                            </dd>
                        </div>

                        {/* Género */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Género</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {getGeneroText(nucleo_familiar.genero)}
                            </dd>
                        </div>

                        {/* Parentesco */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Parentesco</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {getParentescoText(nucleo_familiar.parentesco)}
                            </dd>
                        </div>

                        {/* Trabajador */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Trabajador</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <Link
                                    href={`/web/trabajadores/${nucleo_familiar.trabajador.id}`}
                                    className="text-purple-600 hover:text-purple-900"
                                >
                                    {nucleo_familiar.trabajador.nombres} {nucleo_familiar.trabajador.apellidos}
                                </Link>
                                <br />
                                <span className="text-xs text-gray-500">
                                    Empresa: {nucleo_familiar.trabajador.empresa?.nombre || 'Sin empresa'}
                                </span>
                            </dd>
                        </div>

                        {/* Teléfono */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.telefono || 'No especificado'}
                            </dd>
                        </div>

                        {/* Email */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Email</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.email || 'No especificado'}
                            </dd>
                        </div>

                        {/* Dirección */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.direccion || 'No especificada'}
                            </dd>
                        </div>

                        {/* Estado Civil */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Estado Civil</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {getEstadoCivilText(nucleo_familiar.estado_civil)}
                            </dd>
                        </div>

                        {/* Ocupación */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Ocupación</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {nucleo_familiar.ocupacion || 'No especificada'}
                            </dd>
                        </div>

                        {/* Dependiente Económico */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Dependiente Económico</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    nucleo_familiar.dependiente_economico
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                }`}>
                                    {nucleo_familiar.dependiente_economico ? 'Sí' : 'No'}
                                </span>
                            </dd>
                        </div>

                        {/* Fecha de creación */}
                        <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Fecha de creación</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(nucleo_familiar.created_at)}
                            </dd>
                        </div>

                        {/* Última actualización */}
                        <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt className="text-sm font-medium text-gray-500">Última actualización</dt>
                            <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {formatDate(nucleo_familiar.updated_at)}
                            </dd>
                        </div>
                    </dl>
                </div>

                {/* Botones de acción */}
                <div className="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div className="flex justify-between">
                        <button
                            onClick={handleDelete}
                            disabled={deleting}
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {deleting ? 'Eliminando...' : 'Eliminar Familiar'}
                        </button>
                        <Link
                            href={`/web/nucleos-familiares/${nucleo_familiar.id}/edit`}
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Editar Familiar
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
