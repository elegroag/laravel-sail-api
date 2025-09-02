import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Empresa = {
    id: number;
    nombre: string;
};

type Props = {
    empresas: Empresa[];
};

export default function Create({ empresas }: Props) {
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
            const response = await fetch('/api/trabajadores', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                router.visit('/web/trabajadores');
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    console.error('Error desconocido:', data);
                }
            }
        } catch (error) {
            console.error('Error al crear trabajador:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Crear Trabajador">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Crear Trabajador
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Formulario para crear un nuevo trabajador
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.fecha_ingreso}
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
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
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
                                {processing ? 'Guardando...' : 'Guardar Trabajador'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    )
}
