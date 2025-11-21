import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import type { Componente } from '@/types/cajas';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';

type Props = {
    componente?: Componente;
    componentes: Array<Pick<Componente, 'id' | 'name' | 'label' | 'type'>>;
};

type FormData = {
    componente_id: string | number;
    pattern: string;
    default_value: string;
    max_length: string; // mantener como string para input controlado
    min_length: string; // idem
    numeric_range: string;
    field_size: number;
    detail_info: string;
    is_required: boolean;
    custom_rules: Record<string, string>;
    error_messages: Record<string, string>;
};

export default function Create({ componente, componentes }: Props) {
    const [formData, setFormData] = useState<FormData>({
        componente_id: componente?.id?.toString() || '',
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
    const [pickerOpen, setPickerOpen] = useState(false);
    const [pickerQuery, setPickerQuery] = useState('');

    // Preselección desde query: componente_id
    useEffect(() => {
        const sp = new URLSearchParams(window.location.search);
        const cid = sp.get('componente_id');
        if (cid) {
            setFormData(prev => ({ ...prev, componente_id: Number(cid) || cid }));
        }
    }, []);

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

    const handleJsonChange = (field: 'custom_rules' | 'error_messages', key: string, value: string) => {
        setFormData(prev => ({
            ...prev,
            [field]: {
                ...(prev[field] as Record<string, string>),
                [key]: value
            }
        }));
    };

    const addCustomRule = () => {
        const key = prompt('Nombre de la regla personalizada:');
        if (key && key.trim()) {
            setFormData(prev => ({
                ...prev,
                custom_rules: {
                    ...prev.custom_rules,
                    [key.trim()]: ''
                }
            }));
        }
    };

    const removeCustomRule = (key: string) => {
        setFormData(prev => {
            const newRules = { ...prev.custom_rules };
            delete newRules[key];
            return {
                ...prev,
                custom_rules: newRules
            };
        });
    };

    const addErrorMessage = () => {
        const key = prompt('Nombre del mensaje de error (ej: pattern, required, max_length):');
        if (key && key.trim()) {
            setFormData(prev => ({
                ...prev,
                error_messages: {
                    ...prev.error_messages,
                    [key.trim()]: ''
                }
            }));
        }
    };

    const removeErrorMessage = (key: string) => {
        setFormData(prev => {
            const newMessages = { ...prev.error_messages };
            delete newMessages[key];
            return {
                ...prev,
                error_messages: newMessages
            };
        });
    };

    const validate = (): Record<string, string> => {
        const vErrors: Record<string, string> = {};
        // requerido componente
        if (!formData.componente_id || Number(formData.componente_id) <= 0) vErrors.componente_id = 'Selecciona un componente válido.';
        // field_size
        if (!formData.field_size || formData.field_size < 1 || formData.field_size > 100) vErrors.field_size = 'El tamaño debe estar entre 1 y 100.';
        // longitudes coherentes
        const maxL = formData.max_length ? Number(formData.max_length) : null;
        const minL = formData.min_length ? Number(formData.min_length) : null;
        if (maxL !== null && Number.isNaN(maxL)) vErrors.max_length = 'Debe ser un número válido.';
        if (minL !== null && Number.isNaN(minL)) vErrors.min_length = 'Debe ser un número válido.';
        if (maxL !== null && minL !== null && minL > maxL) vErrors.min_length = 'La longitud mínima no puede superar la máxima.';
        // patrón regex válido
        if (formData.pattern) {
            try {
                // Intentar construir RegExp; quitar posibles delimitadores /.../
                const src = formData.pattern.trim();
                const body = src.startsWith('/') && src.lastIndexOf('/') > 0 ? src.slice(1, src.lastIndexOf('/')) : src;
                // flags opcionales si venían al final, no los usamos aquí
                void new RegExp(body);
            } catch {
                vErrors.pattern = 'La expresión regular no es válida.';
            }
        }
        // rango numérico
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

            const response = await fetch('/cajas/componente-validacion', {
                method: 'POST',
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
            console.error('Error al crear validación:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Crear Validación de Componente">
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Crear Validación de Componente
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Define reglas de validación para un componente dinámico
                        </p>
                        {componente && (
                            <p className="mt-1 text-sm text-indigo-600">
                                Para el componente: {componente.label} ({componente.type})
                            </p>
                        )}
                    </div>
            {/* Picker de componente */}
            <Dialog open={pickerOpen} onOpenChange={setPickerOpen}>
                <DialogContent className="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle>Seleccionar componente</DialogTitle>
                        <DialogDescription>
                            Busca y selecciona un componente para asociar esta validación.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-3">
                        <input
                            type="text"
                            value={pickerQuery}
                            onChange={(e) => setPickerQuery(e.target.value)}
                            placeholder="Buscar por nombre o etiqueta..."
                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                        />
                        <div className="max-h-64 overflow-auto divide-y divide-gray-200 rounded border">
                            {componentes
                                .filter(c => {
                                    const q = pickerQuery.toLowerCase();
                                    return !q || c.name.toLowerCase().includes(q) || (c.label?.toLowerCase() || '').includes(q);
                                })
                                .map((c: Pick<Componente,'id'|'name'|'label'|'type'>) => (
                                    <div key={c.id} className="flex items-center justify-between px-3 py-2 hover:bg-gray-50">
                                        <div>
                                            <div className="text-sm font-medium text-gray-900">{c.label} <span className="text-gray-400">({c.name})</span></div>
                                            <div className="text-xs text-gray-500">Tipo: {c.type}</div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setFormData(prev => ({ ...prev, componente_id: c.id }));
                                                setPickerOpen(false);
                                            }}
                                            className="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                        >
                                            Seleccionar
                                        </button>
                                    </div>
                                ))}
                        </div>
                    </div>
                    <DialogFooter>
                        <DialogClose asChild>
                            <button
                                type="button"
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Cerrar
                            </button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
                    <div className="flex gap-2">
                        {formData.componente_id && (
                            <Link
                                href={`/cajas/componente-dinamico/${String(formData.componente_id)}/show`}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Volver al Componente
                            </Link>
                        )}
                        <Link
                            href="/cajas/componente-validacion"
                            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Volver
                        </Link>
                    </div>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
                            {/* Componente */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="componente_id" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Componente *</label>
                                <div className="flex gap-2">
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
                                    <button
                                        type="button"
                                        onClick={() => setPickerOpen(true)}
                                        className="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        title="Buscar componente"
                                    >
                                        Buscar
                                    </button>
                                </div>
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

                            {/* Reglas personalizadas */}
                            <div className="col-span-6">
                                <div className="border-t border-gray-200 pt-6">
                                    <div className="flex justify-between items-center mb-4">
                                        <h4 className="text-sm font-medium text-gray-900">Reglas Personalizadas</h4>
                                        <button
                                            type="button"
                                            onClick={addCustomRule}
                                            className="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            + Agregar Regla
                                        </button>
                                    </div>
                                    <div className="space-y-3">
                                        {Object.entries(formData.custom_rules).map(([key, value]: [string, string]) => (
                                            <div key={key} className="flex gap-3 items-end">
                                                <div className="flex-1">
                                                    <label className="block text-sm font-medium text-gray-700">{key}</label>
                                                    <input
                                                        type="text"
                                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                        value={value}
                                                        onChange={(e) => handleJsonChange('custom_rules', key, e.target.value)}
                                                        placeholder="Valor de la regla"
                                                    />
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={() => removeCustomRule(key)}
                                                    className="inline-flex items-center h-9 px-3 rounded-md border border-red-300 text-sm font-medium text-red-700 hover:bg-red-50"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* Mensajes de error personalizados */}
                            <div className="col-span-6">
                                <div className="border-t border-gray-200 pt-6">
                                    <div className="flex justify-between items-center mb-4">
                                        <h4 className="text-sm font-medium text-gray-900">Mensajes de Error Personalizados</h4>
                                        <button
                                            type="button"
                                            onClick={addErrorMessage}
                                            className="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            + Agregar Mensaje
                                        </button>
                                    </div>
                                    <div className="space-y-3">
                                        {Object.entries(formData.error_messages).map(([key, value]: [string, string]) => (
                                            <div key={key} className="flex gap-3 items-end">
                                                <div className="flex-1">
                                                    <label className="block text-sm font-medium text-gray-700">{key}</label>
                                                    <input
                                                        type="text"
                                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                        value={value}
                                                        onChange={(e) => handleJsonChange('error_messages', key, e.target.value)}
                                                        placeholder="Mensaje de error personalizado"
                                                    />
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={() => removeErrorMessage(key)}
                                                    className="inline-flex items-center h-9 px-3 rounded-md border border-red-300 text-sm font-medium text-red-700 hover:bg-red-50"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Guardando...' : 'Guardar Validación'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
