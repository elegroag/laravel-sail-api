import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

type Props = {
    componente: {
        id: number;
        name: string;
        type: string;
        label: string;
        placeholder: string | null;
        form_type: string;
        group_id: number;
        order: number;
        default_value: string | null;
        is_disabled: boolean;
        is_readonly: boolean;
        data_source: any[] | null;
        css_classes: string | null;
        help_text: string | null;
        target: number;
        event_config: any;
        search_type: string | null;
        date_max: string | null;
        number_min: number | null;
        number_max: number | null;
        number_step: number;
    };
};

export default function Edit({ componente }: Props) {
    const [formData, setFormData] = useState({
        name: '',
        type: 'input',
        label: '',
        placeholder: '',
        form_type: 'input',
        group_id: 1,
        order: 1,
        default_value: '',
        is_disabled: false,
        is_readonly: false,
        data_source: [] as Array<{value: string, label: string}>,
        css_classes: '',
        help_text: '',
        target: -1,
        event_config: {} as any,
        search_type: '',
        date_max: '',
        number_min: '',
        number_max: '',
        number_step: 1,
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    // Cargar datos del componente al montar el componente
    useEffect(() => {
        setFormData({
            name: componente.name || '',
            type: componente.type || 'input',
            label: componente.label || '',
            placeholder: componente.placeholder || '',
            form_type: componente.form_type || 'input',
            group_id: componente.group_id || 1,
            order: componente.order || 1,
            default_value: componente.default_value || '',
            is_disabled: componente.is_disabled || false,
            is_readonly: componente.is_readonly || false,
            data_source: componente.data_source || [],
            css_classes: componente.css_classes || '',
            help_text: componente.help_text || '',
            target: componente.target || -1,
            event_config: componente.event_config || {},
            search_type: componente.search_type || '',
            date_max: componente.date_max || '',
            number_min: componente.number_min?.toString() || '',
            number_max: componente.number_max?.toString() || '',
            number_step: componente.number_step || 1,
        });
    }, [componente]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        const checked = (e.target as HTMLInputElement).checked;

        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : type === 'number' ? Number(value) : value
        }));

        // Limpiar error cuando el usuario comienza a escribir
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };

    const handleDataSourceChange = (index: number, field: 'value' | 'label', value: string) => {
        setFormData(prev => ({
            ...prev,
            data_source: prev.data_source.map((item, i) =>
                i === index ? { ...item, [field]: value } : item
            )
        }));
    };

    const addDataSourceItem = () => {
        setFormData(prev => ({
            ...prev,
            data_source: [...prev.data_source, { value: '', label: '' }]
        }));
    };

    const removeDataSourceItem = (index: number) => {
        setFormData(prev => ({
            ...prev,
            data_source: prev.data_source.filter((_, i) => i !== index)
        }));
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        try {
            const submitData = {
                ...formData,
                data_source: formData.type === 'select' ? formData.data_source : null,
                event_config: Object.keys(formData.event_config).length > 0 ? formData.event_config : null,
                date_max: formData.type === 'date' && formData.date_max ? formData.date_max : null,
                number_min: formData.type === 'number' && formData.number_min ? Number(formData.number_min) : null,
                number_max: formData.type === 'number' && formData.number_max ? Number(formData.number_max) : null,
                number_step: formData.type === 'number' ? Number(formData.number_step) : 1,
            };

            const response = await fetch(`/mercurio/componente-dinamico/${componente.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(submitData)
            });

            const data = await response.json();

            if (response.ok) {
                router.visit('/mercurio/componente-dinamico');
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    console.error('Error desconocido:', data);
                }
            }
        } catch (error) {
            console.error('Error al actualizar componente:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title={`Editar Componente: ${componente.label}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Editar Componente Dinámico
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Modificar los datos del componente dinámico
                        </p>
                    </div>
                    <Link
                        href="/mercurio/componente-dinamico"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver
                    </Link>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
                            {/* Nombre único */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre único *</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.name ? 'border-red-300' : ''}`}
                                    value={formData.name}
                                    onChange={handleChange}
                                />
                                {errors.name && (<p className="mt-1 text-sm text-red-600">{errors.name}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Identificador único para el componente</p>
                            </div>

                            {/* Tipo */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="type" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo *</label>
                                <select
                                    name="type"
                                    id="type"
                                    required
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                                    value={formData.type}
                                    onChange={handleChange}
                                >
                                    <option value="input">Input (Texto)</option>
                                    <option value="select">Select (Lista desplegable)</option>
                                    <option value="textarea">Textarea (Texto largo)</option>
                                    <option value="date">Date (Fecha)</option>
                                    <option value="number">Number (Número)</option>
                                    <option value="dialog">Dialog (Modal)</option>
                                </select>
                                <p className="mt-1 text-xs text-gray-500">Tipo de componente</p>
                            </div>

                            {/* Etiqueta */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="label" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Etiqueta *</label>
                                <input
                                    type="text"
                                    name="label"
                                    id="label"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.label ? 'border-red-300' : ''}`}
                                    value={formData.label}
                                    onChange={handleChange}
                                />
                                {errors.label && (<p className="mt-1 text-sm text-red-600">{errors.label}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Texto visible del campo</p>
                            </div>

                            {/* Placeholder */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="placeholder" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Placeholder</label>
                                <input
                                    type="text"
                                    name="placeholder"
                                    id="placeholder"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.placeholder ? 'border-red-300' : ''}`}
                                    value={formData.placeholder}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Texto de ayuda dentro del campo</p>
                            </div>

                            {/* Grupo y Orden */}
                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="group_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Grupo *</label>
                                <input
                                    type="number"
                                    name="group_id"
                                    id="group_id"
                                    required
                                    min="1"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.group_id ? 'border-red-300' : ''}`}
                                    value={formData.group_id}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">ID del grupo al que pertenece</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="order" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Orden *</label>
                                <input
                                    type="number"
                                    name="order"
                                    id="order"
                                    required
                                    min="1"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.order ? 'border-red-300' : ''}`}
                                    value={formData.order}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Orden de aparición en el formulario</p>
                            </div>

                            {/* Estados */}
                            <div className="col-span-6 sm:col-span-2">
                                <div className="space-y-3">
                                    <div>
                                        <label className="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                name="is_disabled"
                                                id="is_disabled"
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                checked={formData.is_disabled}
                                                onChange={handleChange}
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Deshabilitado</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label className="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                name="is_readonly"
                                                id="is_readonly"
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                checked={formData.is_readonly}
                                                onChange={handleChange}
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Solo lectura</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {/* Valor por defecto */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="default_value" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor por defecto</label>
                                <input
                                    type="text"
                                    name="default_value"
                                    id="default_value"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.default_value ? 'border-red-300' : ''}`}
                                    value={formData.default_value}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Texto de ayuda */}
                            <div className="col-span-6">
                                <label htmlFor="help_text" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Texto de ayuda</label>
                                <textarea
                                    name="help_text"
                                    id="help_text"
                                    rows={2}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.help_text ? 'border-red-300' : ''}`}
                                    value={formData.help_text}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Data Source para Select */}
                            {formData.type === 'select' && (
                                <div className="col-span-6">
                                    <div className="border-t border-gray-200 pt-6">
                                        <h4 className="text-sm font-medium text-gray-900 mb-4">Opciones del Select</h4>
                                        <div className="space-y-3">
                                            {formData.data_source.map((item, index) => (
                                                <div key={index} className="flex gap-3 items-end">
                                                    <div className="flex-1">
                                                        <label className="block text-sm font-medium text-gray-700">Valor</label>
                                                        <input
                                                            type="text"
                                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                            value={item.value}
                                                            onChange={(e) => handleDataSourceChange(index, 'value', e.target.value)}
                                                            placeholder="Valor interno"
                                                        />
                                                    </div>
                                                    <div className="flex-1">
                                                        <label className="block text-sm font-medium text-gray-700">Etiqueta</label>
                                                        <input
                                                            type="text"
                                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                            value={item.label}
                                                            onChange={(e) => handleDataSourceChange(index, 'label', e.target.value)}
                                                            placeholder="Texto visible"
                                                        />
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={() => removeDataSourceItem(index)}
                                                        className="inline-flex items-center h-9 px-3 rounded-md border border-red-300 text-sm font-medium text-red-700 hover:bg-red-50"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>
                                            ))}
                                            <button
                                                type="button"
                                                onClick={addDataSourceItem}
                                                className="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                            >
                                                + Agregar Opción
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Configuración específica por tipo */}
                            {formData.type === 'date' && (
                                <div className="col-span-6 sm:col-span-3">
                                    <label htmlFor="date_max" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha máxima</label>
                                    <input
                                        type="date"
                                        name="date_max"
                                        id="date_max"
                                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.date_max ? 'border-red-300' : ''}`}
                                        value={formData.date_max}
                                        onChange={handleChange}
                                    />
                                </div>
                            )}

                            {formData.type === 'number' && (
                                <>
                                    <div className="col-span-6 sm:col-span-2">
                                        <label htmlFor="number_min" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor mínimo</label>
                                        <input
                                            type="number"
                                            step="any"
                                            name="number_min"
                                            id="number_min"
                                            className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.number_min ? 'border-red-300' : ''}`}
                                            value={formData.number_min}
                                            onChange={handleChange}
                                        />
                                    </div>
                                    <div className="col-span-6 sm:col-span-2">
                                        <label htmlFor="number_max" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor máximo</label>
                                        <input
                                            type="number"
                                            step="any"
                                            name="number_max"
                                            id="number_max"
                                            className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.number_max ? 'border-red-300' : ''}`}
                                            value={formData.number_max}
                                            onChange={handleChange}
                                        />
                                    </div>
                                    <div className="col-span-6 sm:col-span-2">
                                        <label htmlFor="number_step" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Incremento *</label>
                                        <input
                                            type="number"
                                            step="any"
                                            name="number_step"
                                            id="number_step"
                                            required
                                            min="0.01"
                                            className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.number_step ? 'border-red-300' : ''}`}
                                            value={formData.number_step}
                                            onChange={handleChange}
                                        />
                                    </div>
                                </>
                            )}
                        </div>

                        <div className="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Actualizando...' : 'Actualizar Componente'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
