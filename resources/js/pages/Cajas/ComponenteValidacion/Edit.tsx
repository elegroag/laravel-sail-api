import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import type { Validacion as ValidacionType, Componente } from '@/types/cajas';

type Props = {
    validacion: ValidacionType & {
        custom_rules: Record<string, string> | null;
        error_messages: Record<string, string> | null;
    };
    componentes: Array<Pick<Componente, 'id' | 'name' | 'label'>>;
};

type FormData = {
    componente_id: string;
    pattern: string;
    default_value: string;
    max_length: string;
    min_length: string;
    numeric_range: string;
    field_size: number;
    detail_info: string;
    is_required: boolean;
    custom_rules: Record<string, string>;
    error_messages: Record<string, string>;
};

export default function Edit({ validacion, componentes }: Props) {
    const [formData, setFormData] = useState<FormData>({
        componente_id: '',
        pattern: '',
        default_value: '',
        max_length: '',
        min_length: '',
        numeric_range: '',
        field_size: 42,
        detail_info: '',
        is_required: false,
        custom_rules: {},
        error_messages: {},
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    // Cargar datos de la validación al montar el componente
    useEffect(() => {
        setFormData({
            componente_id: validacion.componente_id?.toString() || '',
            pattern: validacion.pattern || '',
            default_value: validacion.default_value || '',
            max_length: validacion.max_length?.toString() || '',
            min_length: validacion.min_length?.toString() || '',
            numeric_range: validacion.numeric_range || '',
            field_size: validacion.field_size || 42,
            detail_info: validacion.detail_info || '',
            is_required: validacion.is_required || false,
            custom_rules: validacion.custom_rules || {},
            error_messages: validacion.error_messages || {},
        });
    }, [validacion]);

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

    // (Se removieron helpers de reglas/mensajes personalizados al no existir UI asociada en esta pantalla)

    const validate = (): Record<string, string> => {
        const vErrors: Record<string, string> = {};
        if (!formData.componente_id || Number(formData.componente_id) <= 0) vErrors.componente_id = 'Selecciona un componente válido.';
        if (!formData.field_size || formData.field_size < 1 || formData.field_size > 100) vErrors.field_size = 'El tamaño debe estar entre 1 y 100.';
        const maxL = formData.max_length ? Number(formData.max_length) : null;
        const minL = formData.min_length ? Number(formData.min_length) : null;
        if (maxL !== null && Number.isNaN(maxL)) vErrors.max_length = 'Debe ser un número válido.';
        if (minL !== null && Number.isNaN(minL)) vErrors.min_length = 'Debe ser un número válido.';
        if (maxL !== null && minL !== null && minL > maxL) vErrors.min_length = 'La longitud mínima no puede superar la máxima.';
        if (formData.pattern) {
            try {
                const src = formData.pattern.trim();
                const body = src.startsWith('/') && src.lastIndexOf('/') > 0 ? src.slice(1, src.lastIndexOf('/')) : src;
                void new RegExp(body);
            } catch {
                vErrors.pattern = 'La expresión regular no es válida.';
            }
        }
        if (formData.numeric_range) {
            const m = formData.numeric_range.match(/^\s*(-?\d+(?:\.\d+)?)\s*-\s*(-?\d+(?:\.\d+)?)\s*$/);
            if (!m) {
                vErrors.numeric_range = 'Formato inválido. Usa min-max (ej: 1-100).';
            } else {
                const a = Number(m[1]);
                const b = Number(m[2]);
                if (Number.isNaN(a) || Number.isNaN(b)) vErrors.numeric_range = 'Valores numéricos inválidos.';
                else if (a > b) vErrors.numeric_range = 'El mínimo no puede ser mayor que el máximo.';
            }
        }
        return vErrors;
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        const vErrors = validate();
        if (Object.keys(vErrors).length > 0) {
            setErrors(vErrors);
            return;
        }
        setProcessing(true);

        try {
            const submitData = {
                ...formData,
                componente_id: Number(formData.componente_id),
                max_length: formData.max_length ? Number(formData.max_length) : null,
                min_length: formData.min_length ? Number(formData.min_length) : null,
                custom_rules: Object.keys(formData.custom_rules).length > 0 ? formData.custom_rules : null,
                error_messages: Object.keys(formData.error_messages).length > 0 ? formData.error_messages : null,
            };

            const response = await fetch(`/cajas/componente-validacion/${validacion.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(submitData)
            });

            const data = await response.json();

            if (response.ok) {
                router.visit('/cajas/componente-validacion');
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    console.error('Error desconocido:', data);
                }
            }
        } catch (error) {
            console.error('Error al actualizar validación:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title={`Editar Validación: Componente ${validacion.componente_id}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Editar Validación de Componente
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Modificar las reglas de validación del componente
                        </p>
                    </div>
                    <Link
                        href="/cajas/componente-validacion"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver
                    </Link>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
                            {/* Componente */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="componente_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Componente *</label>
                                <select
                                    name="componente_id"
                                    id="componente_id"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.componente_id ? 'border-red-300' : ''}`}
                                    value={formData.componente_id}
                                    onChange={handleChange}
                                >
                                    <option value="">Selecciona un componente</option>
                                    {componentes.map(comp => (
                                        <option key={comp.id} value={comp.id}>
                                            {comp.label} ({comp.name})
                                        </option>
                                    ))}
                                </select>
                                {errors.componente_id && (<p className="mt-1 text-sm text-red-600">{errors.componente_id}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Componente al que se aplicarán estas reglas de validación</p>
                            </div>

                            {/* Patrón regex */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="pattern" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Patrón Regex</label>
                                <input
                                    type="text"
                                    name="pattern"
                                    id="pattern"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.pattern ? 'border-red-300' : ''}`}
                                    value={formData.pattern}
                                    onChange={handleChange}
                                    placeholder="ej: /^[a-zA-Z\s]+$/"
                                />
                                {errors.pattern && (<p className="mt-1 text-sm text-red-600">{errors.pattern}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Expresión regular para validar el formato del campo</p>
                            </div>

                            {/* Longitud máxima y mínima */}
                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="max_length" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitud Máxima</label>
                                <input
                                    type="number"
                                    name="max_length"
                                    id="max_length"
                                    min="1"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.max_length ? 'border-red-300' : ''}`}
                                    value={formData.max_length}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Número máximo de caracteres</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="min_length" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitud Mínima</label>
                                <input
                                    type="number"
                                    name="min_length"
                                    id="min_length"
                                    min="0"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.min_length ? 'border-red-300' : ''}`}
                                    value={formData.min_length}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Número mínimo de caracteres</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="field_size" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Tamaño del Campo *</label>
                                <input
                                    type="number"
                                    name="field_size"
                                    id="field_size"
                                    required
                                    min="1"
                                    max="100"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.field_size ? 'border-red-300' : ''}`}
                                    value={formData.field_size}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Tamaño visual del campo (1-100)</p>
                            </div>

                            {/* Rango numérico */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="numeric_range" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Rango Numérico</label>
                                <input
                                    type="text"
                                    name="numeric_range"
                                    id="numeric_range"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.numeric_range ? 'border-red-300' : ''}`}
                                    value={formData.numeric_range}
                                    onChange={handleChange}
                                    placeholder="ej: 1-100"
                                />
                                {errors.numeric_range && (<p className="mt-1 text-sm text-red-600">{errors.numeric_range}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Rango válido para campos numéricos (formato: min-max)</p>
                            </div>

                            {/* Valor por defecto */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="default_value" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor por Defecto</label>
                                <input
                                    type="text"
                                    name="default_value"
                                    id="default_value"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.default_value ? 'border-red-300' : ''}`}
                                    value={formData.default_value}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Valor que se asignará por defecto</p>
                            </div>

                            {/* Información detallada */}
                            <div className="col-span-6">
                                <label htmlFor="detail_info" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Información Detallada</label>
                                <textarea
                                    name="detail_info"
                                    id="detail_info"
                                    rows={3}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.detail_info ? 'border-red-300' : ''}`}
                                    value={formData.detail_info}
                                    onChange={handleChange}
                                />
                                <p className="mt-1 text-xs text-gray-500">Información adicional sobre las reglas de validación</p>
                            </div>

                            {/* Campo requerido */}
                            <div className="col-span-6 sm:col-span-2">
                                <div className="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="is_required"
                                        id="is_required"
                                        className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        checked={formData.is_required}
                                        onChange={handleChange}
                                    />
                                    <label htmlFor="is_required" className="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Campo requerido
                                    </label>
                                </div>
                                <p className="mt-1 text-xs text-gray-500">Si este campo debe ser obligatorio</p>
                            </div>
                        </div>

                        <div className="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Actualizando...' : 'Actualizar Validación'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
