import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import type { LayoutConfig, Permissions, Formulario as FormularioType } from '@/types/cajas';
import { parseJsonSafe } from '@/utils/json';

type Props = {
    formulario: FormularioType;
};

type FormData = {
    name: string;
    title: string;
    description: string;
    module: string;
    endpoint: string;
    method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
    is_active: boolean;
    layout_config: LayoutConfig;
    permissions: Permissions;
};

export default function Edit({ formulario }: Props) {
    const [formData, setFormData] = useState<FormData>({
        name: '',
        title: '',
        description: '',
        module: '',
        endpoint: '',
        method: 'POST',
        is_active: true,
        layout_config: {
            columns: 1,
            spacing: 'md',
            theme: 'default'
        },
        permissions: {
            public: false,
            roles: [] as string[]
        }
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    // Cargar datos del formulario al montar el componente
    useEffect(() => {
        const defaultsLayout: LayoutConfig = { columns: 1, spacing: 'md', theme: 'default' };
        const defaultsPermissions: Permissions = { public: false, roles: [] };

        const layout = parseJsonSafe<LayoutConfig>(formulario.layout_config, defaultsLayout);
        const perms = parseJsonSafe<Permissions>(formulario.permissions, defaultsPermissions);

        const allowedMethods = ['GET','POST','PUT','PATCH','DELETE'] as const;
        const safeMethod = allowedMethods.includes(formulario.method as typeof allowedMethods[number])
            ? (formulario.method as typeof allowedMethods[number])
            : 'POST';

        setFormData({
            name: formulario.name || '',
            title: formulario.title || '',
            description: formulario.description || '',
            module: formulario.module || '',
            endpoint: formulario.endpoint || '',
            method: safeMethod,
            // mantener booleano exactamente, sin coaccionar a true cuando es false
            is_active: Boolean(formulario.is_active),
            layout_config: layout,
            permissions: perms,
        });
    }, [formulario]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        const checked = (e.target as HTMLInputElement).checked;

        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox'
                ? checked
                : name === 'method'
                    ? (value as FormData['method'])
                    : value
        }));

        // Limpiar error cuando el usuario comienza a escribir
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };

    const handleJsonChange = (field: 'layout_config' | 'permissions', value: LayoutConfig | Permissions) => {
        setFormData(prev => ({
            ...prev,
            [field]: value
        }));
    };

    const validate = (): Record<string, string> => {
        const v: Record<string, string> = {};
        const methodAllowed = ['GET','POST','PUT','PATCH','DELETE'];
        const spacingAllowed = ['sm','md','lg'];
        const themeAllowed = ['default','professional','clean','support','feedback'];

        if (!formData.name.trim()) v.name = 'El nombre es obligatorio.';
        if (!formData.title.trim()) v.title = 'El título es obligatorio.';
        if (!formData.module.trim()) v.module = 'El módulo es obligatorio.';
        if (!formData.endpoint.trim()) v.endpoint = 'El endpoint es obligatorio.';
        if (!methodAllowed.includes(formData.method)) v.method = 'Método inválido.';

        const cols = Number(formData.layout_config?.columns ?? 1);
        if (Number.isNaN(cols) || cols < 1 || cols > 3) v.layout_config = 'Columnas debe estar entre 1 y 3.';
        const spacing = formData.layout_config?.spacing ?? 'md';
        if (!spacingAllowed.includes(spacing)) v.layout_config = (v.layout_config || '') || 'Espaciado inválido.';
        const theme = formData.layout_config?.theme ?? 'default';
        if (!themeAllowed.includes(theme)) v.layout_config = (v.layout_config || '') || 'Tema inválido.';

        const perms = formData.permissions || { public: false, roles: [] };
        if (!perms.public) {
            if (!Array.isArray(perms.roles)) v.permissions = 'La lista de roles es inválida.';
        }
        return v;
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        const v = validate();
        if (Object.keys(v).length > 0) {
            setErrors(v);
            return;
        }
        setProcessing(true);
        router.put(
            `/cajas/formulario-dinamico/${formulario.id}`,
            {
                ...formData,
                layout_config: JSON.stringify(formData.layout_config),
                permissions: JSON.stringify(formData.permissions),
            },
            {
                onError: (errs: Record<string, string>) => {
                    setErrors(errs);
                },
                onFinish: () => {
                    setProcessing(false);
                },
            }
        );
    };

    return (
        <AppLayout title={`Editar Formulario: ${formulario.title}`}>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Editar Formulario Dinámico
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Modificar los datos del formulario dinámico
                        </p>
                    </div>
                    <Link
                        href="/cajas/formulario-dinamico"
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Volver
                    </Link>
                </div>
                <div className="px-4 py-5 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        <div className="grid grid-cols-6 gap-6">
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
                                <p className="mt-1 text-xs text-gray-500">Identificador único para el formulario</p>
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="title" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Título *</label>
                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.title ? 'border-red-300' : ''}`}
                                    value={formData.title}
                                    onChange={handleChange}
                                />
                                {errors.title && (<p className="mt-1 text-sm text-red-600">{errors.title}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Título visible del formulario</p>
                            </div>

                            <div className="col-span-6">
                                <label htmlFor="description" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                <textarea
                                    name="description"
                                    id="description"
                                    rows={3}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.description ? 'border-red-300' : ''}`}
                                    value={formData.description}
                                    onChange={handleChange}
                                />
                                {errors.description && (<p className="mt-1 text-sm text-red-600">{errors.description}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Descripción opcional del formulario</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="module" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Módulo *</label>
                                <input
                                    type="text"
                                    name="module"
                                    id="module"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.module ? 'border-red-300' : ''}`}
                                    value={formData.module}
                                    onChange={handleChange}
                                />
                                {errors.module && (<p className="mt-1 text-sm text-red-600">{errors.module}</p>)}
                                <p className="mt-1 text-xs text-gray-500">Módulo al que pertenece</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="endpoint" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Endpoint *</label>
                                <input
                                    type="text"
                                    name="endpoint"
                                    id="endpoint"
                                    required
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 ${errors.endpoint ? 'border-red-300' : ''}`}
                                    value={formData.endpoint}
                                    onChange={handleChange}
                                />
                                {errors.endpoint && (<p className="mt-1 text-sm text-red-600">{errors.endpoint}</p>)}
                                <p className="mt-1 text-xs text-gray-500">URL del endpoint API</p>
                            </div>

                            <div className="col-span-6 sm:col-span-2">
                                <label htmlFor="method" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Método HTTP *</label>
                                <select
                                    name="method"
                                    id="method"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                                    value={formData.method}
                                    onChange={handleChange}
                                >
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="PATCH">PATCH</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                                <p className="mt-1 text-xs text-gray-500">Método HTTP para el endpoint</p>
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="is_active" className="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                <div className="mt-1">
                                    <label className="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            id="is_active"
                                            className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            checked={formData.is_active}
                                            onChange={handleChange}
                                        />
                                        <span className="ml-2 text-sm text-gray-700">Activo</span>
                                    </label>
                                </div>
                                <p className="mt-1 text-xs text-gray-500">Si el formulario está activo y disponible</p>
                            </div>

                            <div className="col-span-6">
                                <div className="border-t border-gray-200 pt-6">
                                    <h4 className="text-sm font-medium text-gray-900 mb-4">Configuración de Layout</h4>
                                    <div className="grid grid-cols-3 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Columnas</label>
                                            <select
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                value={formData.layout_config.columns ?? 1}
                                                onChange={(e) => handleJsonChange('layout_config', { ...formData.layout_config, columns: Number(e.target.value) })}
                                            >
                                                <option value={1}>1 Columna</option>
                                                <option value={2}>2 Columnas</option>
                                                <option value={3}>3 Columnas</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Espaciado</label>
                                            <select
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                value={formData.layout_config.spacing ?? 'md'}
                                                onChange={(e) => handleJsonChange('layout_config', { ...formData.layout_config, spacing: e.target.value as LayoutConfig['spacing'] })}
                                            >
                                                <option value="sm">Pequeño</option>
                                                <option value="md">Mediano</option>
                                                <option value="lg">Grande</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Tema</label>
                                            <select
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                                value={formData.layout_config.theme ?? 'default'}
                                                onChange={(e) => handleJsonChange('layout_config', { ...formData.layout_config, theme: e.target.value as LayoutConfig['theme'] })}
                                            >
                                                <option value="default">Por defecto</option>
                                                <option value="professional">Profesional</option>
                                                <option value="clean">Limpio</option>
                                                <option value="support">Soporte</option>
                                                <option value="feedback">Feedback</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-span-6">
                                <div className="border-t border-gray-200 pt-6">
                                    <h4 className="text-sm font-medium text-gray-900 mb-4">Permisos</h4>
                                    <div className="space-y-4">
                                        <div>
                                            <label className="inline-flex items-center">
                                                <input
                                                    type="checkbox"
                                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    checked={formData.permissions.public}
                                                    onChange={(e) => handleJsonChange('permissions', { ...formData.permissions, public: e.target.checked })}
                                                />
                                                <span className="ml-2 text-sm text-gray-700">Público (accesible sin autenticación)</span>
                                            </label>
                                        </div>
                                        {!formData.permissions.public && (
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-2">Roles permitidos</label>
                                                <div className="grid grid-cols-2 gap-2">
                                                    {(['cliente', 'asesor', 'admin', 'usuario'] as string[]).map(role => (
                                                        <label key={role} className="inline-flex items-center">
                                                            <input
                                                                type="checkbox"
                                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                checked={formData.permissions.roles.includes(role)}
                                                                onChange={(e) => {
                                                                    const roles = (e.target.checked
                                                                        ? [...formData.permissions.roles, role]
                                                                        : formData.permissions.roles.filter((r) => r !== role)) as string[];
                                                                    handleJsonChange('permissions', { ...formData.permissions, roles: roles as string[] });
                                                                }}
                                                            />
                                                            <span className="ml-2 text-sm text-gray-700 capitalize">{role}</span>
                                                        </label>
                                                    ))}
                                                </div>
                                            </div>
                                        )}
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
                                {processing ? 'Actualizando...' : 'Actualizar Formulario'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
