import AppLayout from '@/layouts/app-layout';
import { Link, useForm } from '@inertiajs/react';

type SelectOption = {
    id?: string | number;
    value: string;
    label: string;
};

type Props = {
    menu_items: Array<{ id: number; title: string }>;
    tipos_funcionarios: Array<{ tipfun: string; destipfun: string }>;
    errors: Record<string, string>;
};

export default function Create({ menu_items, tipos_funcionarios, errors }: Props) {
    const { data, setData, post, processing } = useForm({
        menu_item: '',
        tipfun: '',
        can_view: false,
        opciones: '',
    });

    const menuItemOptions: SelectOption[] = menu_items.map(item => ({
        value: String(item.id),
        label: item.title,
    }));

    const tipFunOptions: SelectOption[] = tipos_funcionarios.map(tf => ({
        value: tf.tipfun,
        label: tf.destipfun,
    }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/cajas/menu-permission');
    };

    return (
        <AppLayout title="Crear Permiso">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6 border-b">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Crear Nuevo Permiso
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Asigna un permiso a un item del menú para un tipo de funcionario.
                    </p>
                </div>
                <form onSubmit={handleSubmit} className="p-4 sm:p-6">
                    <div className="space-y-4">
                        <div>
                            <label htmlFor="menu_item" className="block text-sm font-medium text-gray-700">Item del Menú</label>
                            <select
                                id="menu_item"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={data.menu_item}
                                onChange={(e) => setData('menu_item', e.target.value)}
                                required
                            >
                                <option value="">-- Seleccione un item --</option>
                                {menuItemOptions.map(option => (
                                    <option key={option.value} value={option.value}>{option.label}</option>
                                ))}
                            </select>
                            {errors.menu_item && <p className="mt-1 text-xs text-red-600">{errors.menu_item}</p>}
                        </div>

                        <div>
                            <label htmlFor="tipfun" className="block text-sm font-medium text-gray-700">Tipo de Funcionario</label>
                            <select
                                id="tipfun"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={data.tipfun}
                                onChange={(e) => setData('tipfun', e.target.value)}
                                required
                            >
                                <option value="">-- Seleccione un tipo --</option>
                                {tipFunOptions.map(option => (
                                    <option key={option.value} value={option.value}>{option.label}</option>
                                ))}
                            </select>
                            {errors.tipfun && <p className="mt-1 text-xs text-red-600">{errors.tipfun}</p>}
                        </div>

                        <div>
                            <label htmlFor="opciones" className="block text-sm font-medium text-gray-700">Opciones Adicionales</label>
                            <input
                                id="opciones"
                                type="text"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                                value={data.opciones}
                                onChange={(e) => setData('opciones', e.target.value)}
                            />
                            {errors.opciones && <p className="mt-1 text-xs text-red-600">{errors.opciones}</p>}
                        </div>

                        <div className="flex items-start">
                            <div className="flex items-center h-5">
                                <input
                                    id="can_view"
                                    type="checkbox"
                                    className="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    checked={data.can_view}
                                    onChange={(e) => setData('can_view', e.target.checked)}
                                />
                            </div>
                            <div className="ml-3 text-sm">
                                <label htmlFor="can_view" className="font-medium text-gray-700">Puede Ver</label>
                                <p className="text-gray-500">Indica si el tipo de funcionario puede ver este item en el menú.</p>
                            </div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3">
                        <Link href="/cajas/menu-permission" className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center h-9 px-3 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {processing ? 'Creando...' : 'Crear Permiso'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
