import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Create() {
    const [formData, setFormData] = useState({
        title: '',
        default_url: '',
        icon: '',
        color: '',
        nota: '',
        parent_id: '',
        codapl: 'CA',
        controller: '',
        action: ''
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
            const response = await fetch('/cajas/menu', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ...formData,
                    parent_id: formData.parent_id ? Number(formData.parent_id) : null,
                })
            });

            const data = await response.json();

            if (response.ok) {
                router.visit('/cajas/menu');
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    console.error('Error desconocido:', data);
                }
            }
        } catch (error) {
            console.error('Error al crear empresa:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Crear Menu Item">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Crear Menu Item
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Formulario para crear un nuevo item de menú
                        </p>
                    </div>
                    <Link
                        href="/cajas/menu"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver
                    </Link>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Título *</label>
                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.title ? 'border-red-300' : ''}`}
                                    value={formData.title}
                                    onChange={handleChange}
                                />
                                {errors.title && (<p className="mt-1 text-sm text-red-600">{errors.title}</p>)}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="codapl" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Aplicación *</label>
                                <select
                                    name="codapl"
                                    id="codapl"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.codapl}
                                    onChange={handleChange}
                                >
                                    <option value="CA">CA</option>
                                </select>
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="controller" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Controller *</label>
                                <input
                                    type="text"
                                    name="controller"
                                    id="controller"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.controller ? 'border-red-300' : ''}`}
                                    value={formData.controller}
                                    onChange={handleChange}
                                />
                                {errors.controller && (<p className="mt-1 text-sm text-red-600">{errors.controller}</p>)}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="action" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Action *</label>
                                <input
                                    type="text"
                                    name="action"
                                    id="action"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.action ? 'border-red-300' : ''}`}
                                    value={formData.action}
                                    onChange={handleChange}
                                />
                                {errors.action && (<p className="mt-1 text-sm text-red-600">{errors.action}</p>)}
                            </div>

                            <div className="col-span-6">
                                <label htmlFor="default_url" className="block text-sm font-medium text-gray-700 dark:text-gray-300">URL por defecto</label>
                                <input
                                    type="text"
                                    name="default_url"
                                    id="default_url"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.default_url ? 'border-red-300' : ''}`}
                                    value={formData.default_url}
                                    onChange={handleChange}
                                />
                                {errors.default_url && (<p className="mt-1 text-sm text-red-600">{errors.default_url}</p>)}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="icon" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Icono</label>
                                <input
                                    type="text"
                                    name="icon"
                                    id="icon"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.icon ? 'border-red-300' : ''}`}
                                    value={formData.icon}
                                    onChange={handleChange}
                                />
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="color" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                                <input
                                    type="text"
                                    name="color"
                                    id="color"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.color ? 'border-red-300' : ''}`}
                                    value={formData.color}
                                    onChange={handleChange}
                                />
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="parent_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Padre</label>
                                <input
                                    type="number"
                                    name="parent_id"
                                    id="parent_id"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${errors.parent_id ? 'border-red-300' : ''}`}
                                    value={formData.parent_id}
                                    onChange={handleChange}
                                />
                            </div>

                            <div className="col-span-6">
                                <label htmlFor="nota" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Nota</label>
                                <textarea
                                    name="nota"
                                    id="nota"
                                    rows={3}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.nota}
                                    onChange={handleChange}
                                />
                            </div>
                        </div>
                        <div className="grid grid-cols-6 gap-6">
                            {/* Nombre */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre *
                                </label>
                                <input
                                    type="text"
                                    name="nombre"
                                    id="nombre"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
                                        errors.nombre ? 'border-red-300' : ''
                                    }`}
                                    value={formData.nombre}
                                    onChange={handleChange}
                                />
                                {errors.nombre && (
                                    <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
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

                            {/* Dirección */}
                            <div className="col-span-6">
                                <label htmlFor="direccion" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Dirección *
                                </label>
                                <input
                                    type="text"
                                    name="direccion"
                                    id="direccion"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
                                        errors.direccion ? 'border-red-300' : ''
                                    }`}
                                    value={formData.direccion}
                                    onChange={handleChange}
                                />
                                {errors.direccion && (
                                    <p className="mt-1 text-sm text-red-600">{errors.direccion}</p>
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

                            {/* Sector Económico */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="sector_economico" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Sector Económico
                                </label>
                                <input
                                    type="text"
                                    name="sector_economico"
                                    id="sector_economico"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.sector_economico}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Número de Empleados */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="numero_empleados" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Número de Empleados
                                </label>
                                <input
                                    type="number"
                                    name="numero_empleados"
                                    id="numero_empleados"
                                    min="0"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 ${
                                        errors.numero_empleados ? 'border-red-300' : ''
                                    }`}
                                    value={formData.numero_empleados}
                                    onChange={handleChange}
                                />
                                {errors.numero_empleados && (
                                    <p className="mt-1 text-sm text-red-600">{errors.numero_empleados}</p>
                                )}
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
                                    <option value="activa">Activa</option>
                                    <option value="inactiva">Inactiva</option>
                                </select>
                            </div>

                            {/* Descripción */}
                            <div className="col-span-6">
                                <label htmlFor="descripcion" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción
                                </label>
                                <textarea
                                    name="descripcion"
                                    id="descripcion"
                                    rows={3}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    value={formData.descripcion}
                                    onChange={handleChange}
                                />
                            </div>
                        </div>

                        <div className="flex justify-end pt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Guardando...' : 'Guardar Empresa'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    )
}
