import AppLayout from '@/layouts/app-layout';
import { Link, useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import type { Componente, DataSourceItem } from '@/types/cajas';

type Props = { componente: Componente };

type FormState = {
    name: string;
    type: Componente['type'];
    label: string;
    placeholder: string;
    form_type: string;
    group_id: number;
    order: number;
    default_value: string;
    is_disabled: boolean;
    is_readonly: boolean;
    data_source: DataSourceItem[];
    css_classes: string;
    help_text: string;
    target: number;
    event_config: Record<string, string | number | boolean | null>;
    search_type: string;
    search_endpoint: string;
    date_max: string;
    number_min: string | number | null;
    number_max: string | number | null;
    number_step: number;
    formulario_id?: number;
};

export default function Edit({ componente }: Props) {
    const { data, setData, put, processing, errors, setError, clearErrors, transform } = useForm<FormState>({
        name: '',
        type: 'input' as Componente['type'],
        label: '',
        placeholder: '',
        form_type: 'input',
        group_id: 1,
        order: 1,
        default_value: '',
        is_disabled: false,
        is_readonly: false,
        data_source: [] as DataSourceItem[],
        css_classes: '',
        help_text: '',
        target: -1,
        event_config: {} as FormState['event_config'],
        search_type: '',
        search_endpoint: '',
        date_max: '',
        number_min: '',
        number_max: '',
        number_step: 1,
        formulario_id: componente.formulario_id ?? undefined,
    });

    // Cargar datos del componente al montar el componente
    useEffect(() => {
        setData({
            name: componente.name || '',
            type: (componente.type as Componente['type']) || 'input',
            label: componente.label || '',
            placeholder: componente.placeholder || '',
            form_type: componente.form_type || 'input',
            group_id: componente.group_id || 1,
            order: componente.order || 1,
            default_value: componente.default_value || '',
            is_disabled: componente.is_disabled || false,
            is_readonly: componente.is_readonly || false,
            data_source: (componente.data_source as DataSourceItem[] | null) || [],
            css_classes: componente.css_classes || '',
            help_text: componente.help_text || '',
            target: componente.target || -1,
            event_config: (componente.event_config as unknown as FormState['event_config']) || {},
            search_type: (componente.search_type as string) || '',
            search_endpoint: (componente as any).search_endpoint || '',
            date_max: componente.date_max || '',
            number_min: componente.number_min?.toString() || '',
            number_max: componente.number_max?.toString() || '',
            number_step: componente.number_step || 1,
            formulario_id: componente.formulario_id ?? undefined,
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [componente.id]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value, type, checked } = e.target as HTMLInputElement;
        const v = type === 'checkbox' ? checked : type === 'number' ? Number(value) : value;
        setData(name as keyof FormState, v as never);
        clearErrors();
    };

    const handleDataSourceChange = (index: number, field: 'value' | 'label', value: string) => {
        const list = Array.isArray(data.data_source) ? [...data.data_source] : [];
        list[index] = { ...(list[index] || { value: '', label: '' }), [field]: value } as DataSourceItem;
        setData('data_source', list);
    };

    const addDataSourceItem = () => {
        const list = Array.isArray(data.data_source) ? [...data.data_source] : [];
        list.push({ value: '', label: '' });
        setData('data_source', list);
    };

    const removeDataSourceItem = (index: number) => {
        const list = Array.isArray(data.data_source) ? data.data_source.filter((_, i) => i !== index) : [];
        setData('data_source', list);
    };

    const validate = (): Record<string, string> => {
        const v: Record<string, string> = {};
        const typeAllowed = ['input','select','textarea','date','number','dialog'];
        if (!data.name.trim()) v.name = 'El nombre es obligatorio.';
        if (!data.label.trim()) v.label = 'La etiqueta es obligatoria.';
        if (!typeAllowed.includes(data.type)) v.type = 'Tipo inválido.';
        if (!data.group_id || Number(data.group_id) < 1) v.group_id = 'Grupo debe ser un entero ≥ 1.';
        if (!data.order || Number(data.order) < 1) v.order = 'Orden debe ser un entero ≥ 1.';
        // Validar search_type cuando aplica (form_type select o dialog)
        const appliesSearchType = data.form_type === 'select' || data.form_type === 'dialog';
        if (appliesSearchType) {
            const allowedSearch = ['ninguno', 'local', 'ajax', 'collection', ''];
            if (!allowedSearch.includes(data.search_type)) {
                v.search_type = 'Tipo de búsqueda inválido.';
            }
            if (data.search_type === 'ajax') {
                if (!data.search_endpoint || data.search_endpoint.trim().length < 160) {
                    v.search_endpoint = 'Debe especificar un endpoint (mínimo 160 caracteres).';
                }
            }
        }
        // Nota: data_source ya NO es requerido cuando type === 'select'
        if (data.type === 'select' && Array.isArray(data.data_source) && data.data_source.length > 0) {
            const invalid = data.data_source.find(it => !it.value.trim() || !it.label.trim());
            if (invalid) v.data_source = 'Todas las opciones deben tener valor y etiqueta.';
        }
        if (data.type === 'number') {
            const step = Number(data.number_step);
            if (!(step > 0)) v.number_step = 'El incremento debe ser mayor que 0.';
            const min = data.number_min !== '' ? Number(data.number_min) : null;
            const max = data.number_max !== '' ? Number(data.number_max) : null;
            if (min !== null && Number.isNaN(min)) v.number_min = 'Número mínimo inválido.';
            if (max !== null && Number.isNaN(max)) v.number_max = 'Número máximo inválido.';
            if (min !== null && max !== null && min > max) v.number_min = 'El mínimo no puede ser mayor que el máximo.';
        }
        if (data.type === 'date' && data.date_max) {
            const ts = Date.parse(data.date_max);
            if (Number.isNaN(ts)) v.date_max = 'Fecha máxima inválida.';
        }
        return v;
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        const v = validate();
        if (Object.keys(v).length > 0) {
            clearErrors();
            Object.entries(v).forEach(([k, msg]) => setError(k as any, msg));
            return;
        }
        transform((current) => {
            const normalizedSearchType = (current.search_type === 'ninguno' || current.search_type === '') ? null : current.search_type;
            return {
                ...current,
                search_type: normalizedSearchType,
                data_source: (current.type === 'select' && normalizedSearchType === 'local') ? current.data_source : null,
                search_endpoint: normalizedSearchType === 'ajax' ? current.search_endpoint : null,
                event_config: (current.event_config && Object.keys(current.event_config).length > 0) ? current.event_config : null,
                date_max: current.type === 'date' && current.date_max ? current.date_max : null,
                number_min: current.type === 'number' && current.number_min ? Number(current.number_min) : null,
                number_max: current.type === 'number' && current.number_max ? Number(current.number_max) : null,
                number_step: current.type === 'number' ? Number(current.number_step) : 1,
            };
        });
        put(`/cajas/componente-dinamico/${componente.id}`, {
            preserveState: true,
        });
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
                    <div className="flex space-x-2">
                    <Link
                        href={`/cajas/componente-dinamico?formulario_id=${componente.formulario_id}`}
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-400 hover:bg-indigo-400"
                    >
                        Volver con formulario
                    </Link>

                     <Link
                        href="/cajas/componente-dinamico"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver 
                    </Link>
                    </div>

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
                                    value={data.name}
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
                                    value={data.type}
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

                            {/* Tipo de búsqueda (solo para form_type select o dialog) */}
                            {(data.form_type === 'select' || data.form_type === 'dialog') && (
                                <div className="col-span-6 sm:col-span-3">
                                    <label htmlFor="search_type" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de búsqueda</label>
                                    <select
                                        name="search_type"
                                        id="search_type"
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                                        value={data.search_type}
                                        onChange={handleChange}
                                    >
                                        <option value="">Seleccione</option>
                                        <option value="ninguno">Ninguno</option>
                                        <option value="local">Local</option>
                                        <option value="ajax">Ajax</option>
                                        <option value="collection">Collection</option>
                                    </select>
                                    {errors.search_type && (<p className="mt-1 text-sm text-red-600">{errors.search_type}</p>)}
                                    <p className="mt-1 text-xs text-gray-500">Aplica para componentes de tipo select o dialog</p>
                                </div>
                            )}

                            {/* Endpoint de búsqueda (solo cuando search_type = ajax) */}
                            {(data.form_type === 'select' || data.form_type === 'dialog') && data.search_type === 'ajax' && (
                                <div className="col-span-6">
                                    <label htmlFor="search_endpoint" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Endpoint de búsqueda (AJAX)</label>
                                    <input
                                        type="text"
                                        name="search_endpoint"
                                        id="search_endpoint"
                                        minLength={160}
                                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.search_endpoint ? 'border-red-300' : ''}`}
                                        value={data.search_endpoint}
                                        onChange={handleChange}
                                        placeholder="https://api.midominio.com/recurso?param1=... (mínimo 160 caracteres)"
                                    />
                                    {errors.search_endpoint && (<p className="mt-1 text-sm text-red-600">{errors.search_endpoint}</p>)}
                                    <p className="mt-1 text-xs text-gray-500">URL completa a consultar por AJAX. Debe tener mínimo 160 caracteres.</p>
                                </div>
                            )}
                            {/* Etiqueta */}
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="label" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Etiqueta *</label>
                                <input
                                    type="text"
                                    name="label"
                                    id="label"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.label ? 'border-red-300' : ''}`}
                                    value={data.label}
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
                                    value={data.placeholder}
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
                                    value={data.group_id}
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
                                    value={data.order}
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
                                                checked={data.is_disabled}
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
                                                checked={data.is_readonly}
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
                                    value={data.default_value}
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
                                    value={data.help_text}
                                    onChange={handleChange}
                                />
                            </div>

                            {/* Data Source para Select (visible cuando search_type = local) */}
                            {data.type === 'select' && (data.search_type === 'local') && (
                                <div className="col-span-6">
                                    <div className="border-t border-gray-200 pt-6">
                                        <h4 className="text-sm font-medium text-gray-900 mb-4">Opciones del Select</h4>
                                        <div className="space-y-3">
                                            {data.data_source.map((item: DataSourceItem, index: number) => (
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
                            {data.type === 'date' && (
                                <div className="col-span-6 sm:col-span-3">
                                    <label htmlFor="date_max" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha máxima</label>
                                    <input
                                        type="date"
                                        name="date_max"
                                        id="date_max"
                                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.date_max ? 'border-red-300' : ''}`}
                                        value={data.date_max}
                                        onChange={handleChange}
                                    />
                                </div>
                            )}

                            {data.type === 'number' && (
                                <>
                                    <div className="col-span-6 sm:col-span-2">
                                        <label htmlFor="number_min" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor mínimo</label>
                                        <input
                                            type="number"
                                            step="any"
                                            name="number_min"
                                            id="number_min"
                                            className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.number_min ? 'border-red-300' : ''}`}
                                            value={data.number_min as number | string | undefined}
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
                                            value={data.number_max as number | string | undefined}
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
                                            value={data.number_step}
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
