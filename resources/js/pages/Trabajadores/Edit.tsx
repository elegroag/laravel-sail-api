import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

type Empresa = {
    id: number;
    nombre: string;
};

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
    empresa_id: number;
};

type Props = {
    trabajador: Trabajador;
    empresas: Empresa[];
};

export default function Edit({ trabajador, empresas }: Props) {
    const [formData, setFormData] = useState({
        nombres: '',
        apellidos: '',
        rut: '',
        email: '',
        telefono: '',
        fecha_nacimiento: '',
        genero: '',
        direccion: '',
        cargo: '',
        salario: '',
        fecha_ingreso: '',
        fecha_salida: '',
        estado: 'activo',
        empresa_id: ''
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    // Cargar datos del trabajador al montar el componente
    useEffect(() => {
        setFormData({
            nombres: trabajador.nombres || '',
            apellidos: trabajador.apellidos || '',
            rut: trabajador.rut || '',
            email: trabajador.email || '',
            telefono: trabajador.telefono || '',
            fecha_nacimiento: trabajador.fecha_nacimiento || '',
            genero: trabajador.genero || '',
            direccion: trabajador.direccion || '',
            cargo: trabajador.cargo || '',
            salario: trabajador.salario || '',
            fecha_ingreso: trabajador.fecha_ingreso || '',
            fecha_salida: trabajador.fecha_salida || '',
            estado: trabajador.estado || 'activo',
            empresa_id: trabajador.empresa_id?.toString() || ''
        });
    }, [trabajador]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Limpiar error cuando el usuario comienza a escribir
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        try {
            await router.put(`/api/trabajadores/${trabajador.id}`, formData, {
                onSuccess: () => {
                    router.visit('/web/trabajadores');
                },
                onError: (errors) => {
                    setErrors(errors);
                },
                onFinish: () => {
                    setProcessing(false);
                }
            });
        } catch (error) {
            console.error('Error al actualizar trabajador:', error);
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Editar Trabajador">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Editar Trabajador
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Modificar los datos del trabajador
                        </p>
                    </div>
                    <Link
                        href="/web/trabajadores"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver
                    </Link>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
                            {/* Nombres */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="nombres" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombres *
                                </label>
                                <input
                                    type="text"
                                    name="nombres"
                                    id="nombres"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.nombres ? 'border-red-300' : ''
                                    }`}
                                    value={formData.nombres}
                                    onChange={handleChange}
                                />
                                {errors.nombres && (
                                    <p className="mt-1 text-sm text-red-600">{errors.nombres}</p>
                                )}
                            </div>

                            {/* Apellidos */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="apellidos" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Apellidos *
                                </label>
                                <input
                                    type="text"
                                    name="apellidos"
                                    id="apellidos"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.apellidos ? 'border-red-300' : ''
                                    }`}
                                    value={formData.apellidos}
                                    onChange={handleChange}
                                />
                                {errors.apellidos && (
                                    <p className="mt-1 text-sm text-red-600">{errors.apellidos}</p>
                                )}
                            </div>

                            {/* RUT */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="rut" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    RUT *
                                </label>
                                <input
                                    type="text"
                                    name="rut"
                                    id="rut"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.rut ? 'border-red-300' : ''
                                    }`}
                                    value={formData.rut}
                                    onChange={handleChange}
                                />
                                {errors.rut && (
                                    <p className="mt-1 text-sm text-red-600">{errors.rut}</p>
                                )}
                            </div>

                            {/* Email */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="email" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.email ? 'border-red-300' : ''
                                    }`}
                                    value={formData.email}
                                    onChange={handleChange}
                                />
                                {errors.email && (
                                    <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                                )}
                            </div>

                            {/* Teléfono */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="telefono" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Teléfono
                                </label>
                                <input
                                    type="tel"
                                    name="telefono"
                                    id="telefono"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.telefono}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Fecha de Nacimiento */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="fecha_nacimiento" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Nacimiento
                                </label>
                                <input
                                    type="date"
                                    name="fecha_nacimiento"
                                    id="fecha_nacimiento"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.fecha_nacimiento}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Género */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="genero" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Género
                                </label>
                                <select
                                    name="genero"
                                    id="genero"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.genero}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccionar género</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            {/* Empresa */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="empresa_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Empresa *
                                </label>
                                <select
                                    name="empresa_id"
                                    id="empresa_id"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.empresa_id ? 'border-red-300' : ''
                                    }`}
                                    value={formData.empresa_id}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccionar empresa</option>
                                    {empresas.map((empresa) => (
                                        <option key={empresa.id} value={empresa.id}>
                                            {empresa.nombre}
                                        </option>
                                    ))}
                                </select>
                                {errors.empresa_id && (
                                    <p className="mt-1 text-sm text-red-600">{errors.empresa_id}</p>
                                )}
                            </div>

                            {/* Dirección */}
                            <div className="col-span-6">
                                <label htmlFor="direccion" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Dirección
                                </label>
                                <input
                                    type="text"
                                    name="direccion"
                                    id="direccion"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.direccion}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Cargo */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="cargo" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Cargo
                                </label>
                                <input
                                    type="text"
                                    name="cargo"
                                    id="cargo"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.cargo}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Salario */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="salario" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Salario
                                </label>
                                <input
                                    type="number"
                                    name="salario"
                                    id="salario"
                                    step="0.01"
                                    min="0"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ${
                                        errors.salario ? 'border-red-300' : ''
                                    }`}
                                    value={formData.salario}
                                    onChange={handleChange}
                                />
                                {errors.salario && (
                                    <p className="mt-1 text-sm text-red-600">{errors.salario}</p>
                                )}
                            </div>

                            {/* Fecha de Ingreso */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="fecha_ingreso" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Ingreso
                                </label>
                                <input
                                    type="date"
                                    name="fecha_ingreso"
                                    id="fecha_ingreso"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.fecha_ingreso}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Fecha de Salida */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="fecha_salida" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Salida
                                </label>
                                <input
                                    type="date"
                                    name="fecha_salida"
                                    id="fecha_salida"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.fecha_salida}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Estado */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="estado" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Estado
                                </label>
                                <select
                                    name="estado"
                                    id="estado"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value={formData.estado}
                                    onChange={handleChange}
                                >
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                            </div>
                        </div>

                        <div className="flex justify-end pt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Actualizando...' : 'Actualizar Trabajador'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    )
}
