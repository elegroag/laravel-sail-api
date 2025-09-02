import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type Trabajador = {
    id: number;
    nombres: string;
    apellidos: string;
};

type Props = {
    trabajadores: Trabajador[];
};

export default function Create({ trabajadores }: Props) {
    const [formData, setFormData] = useState({
        nombres: '',
        apellidos: '',
        rut: '',
        fecha_nacimiento: '',
        genero: '',
        parentesco: '',
        telefono: '',
        email: '',
        direccion: '',
        estado_civil: '',
        ocupacion: '',
        dependiente_economico: false,
        trabajador_id: ''
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        const checked = (e.target as HTMLInputElement).checked;

        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value
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
            const response = await fetch('/api/nucleos-familiares', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                router.visit('/web/nucleos-familiares');
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    console.error('Error desconocido:', data);
                }
            }
        } catch (error) {
            console.error('Error al crear núcleo familiar:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Crear Núcleo Familiar">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Crear Núcleo Familiar
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Agregar un familiar al núcleo familiar de un trabajador
                        </p>
                    </div>
                    <Link
                        href="/web/nucleos-familiares"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
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
                                    RUT
                                </label>
                                <input
                                    type="text"
                                    name="rut"
                                    id="rut"
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

                            {/* Parentesco */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="parentesco" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Parentesco *
                                </label>
                                <select
                                    name="parentesco"
                                    id="parentesco"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
                                        errors.parentesco ? 'border-red-300' : ''
                                    }`}
                                    value={formData.parentesco}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccionar parentesco</option>
                                    <option value="conyuge">Cónyuge</option>
                                    <option value="hijo">Hijo/a</option>
                                    <option value="padre">Padre</option>
                                    <option value="madre">Madre</option>
                                    <option value="hermano">Hermano/a</option>
                                    <option value="otro">Otro</option>
                                </select>
                                {errors.parentesco && (
                                    <p className="mt-1 text-sm text-red-600">{errors.parentesco}</p>
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

                            {/* Estado Civil */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="estado_civil" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Estado Civil
                                </label>
                                <select
                                    name="estado_civil"
                                    id="estado_civil"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.estado_civil}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccionar estado civil</option>
                                    <option value="soltero">Soltero/a</option>
                                    <option value="casado">Casado/a</option>
                                    <option value="divorciado">Divorciado/a</option>
                                    <option value="viudo">Viudo/a</option>
                                </select>
                            </div>

                            {/* Ocupación */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="ocupacion" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ocupación
                                </label>
                                <input
                                    type="text"
                                    name="ocupacion"
                                    id="ocupacion"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.ocupacion}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Dependiente Económico */}
                            <div className="col-span-6 sm:col-span-3">
                                <div className="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="dependiente_economico"
                                        id="dependiente_economico"
                                        className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        checked={formData.dependiente_economico}
                                        onChange={handleChange}
                                    />
                                    <label htmlFor="dependiente_economico" className="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                        Dependiente económico
                                    </label>
                                </div>
                            </div>

                            {/* Trabajador */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="trabajador_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Trabajador *
                                </label>
                                <select
                                    name="trabajador_id"
                                    id="trabajador_id"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
                                        errors.trabajador_id ? 'border-red-300' : ''
                                    }`}
                                    value={formData.trabajador_id}
                                    onChange={handleChange}
                                >
                                    <option value="">Seleccionar trabajador</option>
                                    {trabajadores.map((trabajador) => (
                                        <option key={trabajador.id} value={trabajador.id}>
                                            {trabajador.nombres} {trabajador.apellidos}
                                        </option>
                                    ))}
                                </select>
                                {errors.trabajador_id && (
                                    <p className="mt-1 text-sm text-red-600">{errors.trabajador_id}</p>
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
                        </div>

                        <div className="flex justify-end pt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Guardando...' : 'Guardar Familiar'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    )
}
