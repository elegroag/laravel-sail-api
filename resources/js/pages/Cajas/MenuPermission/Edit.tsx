import AppLayout from '@/layouts/app-layout';
import { Link, useForm } from '@inertiajs/react';
import { useEffect } from 'react';

type Permission = {
    id: number;
    menu_item: number;
    tipfun: string;
    can_view: boolean;
    opciones: string | null;
    menu_item: {
        id: number;
        title: string;
    };
    tipfun: {
        tipfun: string;
        destipfun: string;
    };
};

type Props = {
    permission: Permission;
    errors: Record<string, string>;
};

export default function Edit({ permission, errors }: Props) {
    const { data, setData, put, processing } = useForm({
        can_view: false,
        opciones: '',
    });

    useEffect(() => {
        setData({
            can_view: permission.can_view,
            opciones: permission.opciones || '',
        });
    }, [permission]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/cajas/menu-permission/${permission.id}`);
    };

    return (
        <AppLayout title="Editar Permiso">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6 border-b">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Editar Permiso
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Modifica los detalles del permiso.
                    </p>
                </div>
                <form onSubmit={handleSubmit} className="p-4 sm:p-6">
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Item del Menú</label>
                            <p className="mt-1 text-sm text-gray-900 p-2 bg-gray-100 rounded-md">{permission.menu_item.title}</p>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Tipo de Funcionario</label>
                            <p className="mt-1 text-sm text-gray-900 p-2 bg-gray-100 rounded-md">{permission.tipfun.destipfun}</p>
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
                            {processing ? 'Actualizando...' : 'Actualizar Permiso'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
