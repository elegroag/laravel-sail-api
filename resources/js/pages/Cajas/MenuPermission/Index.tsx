import AppLayout from '@/layouts/app-layout';
import { Link, router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

type MenuItem = {
    id: number;
    title: string;
    controller: string;
    action: string;
    default_url: string;
    codapl: string;
    tipo: string;
    is_visible: boolean;
    position: number;
};

type TipFun = {
    tipfun: string;
    destipfun: string;
};

type Permission = {
    id: number;
    menu_item: number;
    tipfun: string;
    can_view: boolean;
    opciones: string | null;
    tipfun_details?: TipFun;
};

type Props = {
    menu_items: {
        data: MenuItem[];
        meta: {
            total_menu_items: number;
            pagination?: {
                current_page: number;
                last_page: number;
                per_page: number;
                from: number | null;
                to: number | null;
                total: number;
            };
        };
    };
};

export default function Index({ menu_items }: Props) {
    const { data, meta } = menu_items;

    const [selectedItem, setSelectedItem] = useState<MenuItem | null>(null);
    const [permissions, setPermissions] = useState<Permission[]>([]);
    const [tiposFuncionarios, setTiposFuncionarios] = useState<TipFun[]>([]);
    const [loadingPermissions, setLoadingPermissions] = useState(false);
    const [permissionsError, setPermissionsError] = useState<string | null>(null);
    const [saving, setSaving] = useState(false);

    const searchParams = useMemo(() => new URLSearchParams(window.location.search), []);
    const [q, setQ] = useState<string>(searchParams.get('q') || '');
    const [tipo, setTipo] = useState<string>(searchParams.get('tipo') || '');
    const [codapl, setCodapl] = useState<string>(searchParams.get('codapl') || '');
    const perPage = meta.pagination?.per_page || 10;

    useEffect(() => {
        const sp = new URLSearchParams(window.location.search);
        setQ(sp.get('q') || '');
        setTipo(sp.get('tipo') || '');
        setCodapl(sp.get('codapl') || '');
    }, [window.location.search]);

    const currentFilterParams = useMemo(() => ({ q: q || undefined, tipo: tipo || undefined, codapl: codapl || undefined, per_page: perPage }), [q, tipo, codapl, perPage]);

    const applyFilters = () => {
        router.get('/cajas/menu-permission', { ...currentFilterParams, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const clearFilters = () => {
        setQ('');
        setTipo('');
        setCodapl('');
        router.get('/cajas/menu-permission', { per_page: perPage, page: 1 }, { preserveState: true, preserveScroll: true });
    };

    const handleSelectItem = async (item: MenuItem) => {
        setSelectedItem(item);
        setLoadingPermissions(true);
        setPermissionsError(null);
        try {
            const res = await fetch(`/cajas/menu-permission/${item.id}/permissions`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            if (!res.ok) {
                throw new Error('No fue posible cargar los permisos');
            }
            const json = await res.json();
            setPermissions(json.permissions || []);
            setTiposFuncionarios(json.tipos_funcionarios || []);
        } catch (e: any) {
            setPermissions([]);
            setPermissionsError(e?.message || 'Error desconocido');
        } finally {
            setLoadingPermissions(false);
        }
    };

    const handlePermissionChange = (tipfun: string, field: 'can_view' | 'opciones', value: any) => {
        const existingPermissionIndex = permissions.findIndex(p => p.tipfun === tipfun);
        const updatedPermissions = [...permissions];

        if (existingPermissionIndex > -1) {
            updatedPermissions[existingPermissionIndex] = {
                ...updatedPermissions[existingPermissionIndex],
                [field]: value
            };
        } else {
            const newPermission: Permission = {
                id: 0, // Temp ID
                menu_item: selectedItem!.id,
                tipfun: tipfun,
                can_view: field === 'can_view' ? value : false,
                opciones: field === 'opciones' ? value : null,
            };
            updatedPermissions.push(newPermission);
        }
        setPermissions(updatedPermissions);
    };

    const savePermissions = async () => {
        if (!selectedItem) return;
        setSaving(true);
        try {
            for (const p of permissions) {
                if(p.menu_item !== selectedItem.id) continue;

                await fetch(`/cajas/menu-permission/ajax`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        menu_item: selectedItem.id,
                        tipfun: p.tipfun,
                        can_view: p.can_view,
                        opciones: p.opciones
                    })
                });
            }
            await handleSelectItem(selectedItem); // Refresh
        } catch (error) {
            console.error("Error saving permissions", error);
        } finally {
            setSaving(false);
        }
    };


    return (
        <AppLayout title="Permisos de Menú">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Permisos de Menú
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Administra los permisos para cada item del menú por tipo de funcionario.
                    </p>
                </div>

                {/* Filtros */}
                <div className="px-4 sm:px-6 pb-4">
                    <div className="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <div className="sm:col-span-2">
                            <label htmlFor="q" className="block text-sm font-medium text-gray-700">Buscar Item</label>
                            <input
                                id="q"
                                type="text"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                                placeholder="Título, controller, action..."
                                value={q}
                                onChange={(e) => setQ(e.target.value)}
                                onKeyDown={(e) => { if (e.key === 'Enter') applyFilters(); }}
                            />
                        </div>
                        <div>
                            <label htmlFor="tipo" className="block text-sm font-medium text-gray-700">Tipo Menú</label>
                            <select
                                id="tipo"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={tipo}
                                onChange={(e) => setTipo(e.target.value)}
                            >
                                <option value="">Todos</option>
                                <option value="A">Administrador</option>
                                <option value="E">Empresa</option>
                                <option value="P">Particular</option>
                                <option value="T">Trabajador</option>
                                <option value="F">Foniñez</option>
                            </select>
                        </div>
                        <div>
                            <label htmlFor="codapl" className="block text-sm font-medium text-gray-700">Aplicación</label>
                            <select
                                id="codapl"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-600 p-2"
                                value={codapl}
                                onChange={(e) => setCodapl(e.target.value)}
                            >
                                <option value="">Todas</option>
                                <option value="CA">CA</option>
                                <option value="ME">ME</option>
                            </select>
                        </div>
                        <div className="flex items-end gap-2">
                            <button onClick={applyFilters} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">Filtrar</button>
                            <button onClick={clearFilters} className="inline-flex items-center h-9 px-3 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Limpiar</button>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div className="lg:col-span-1">
                        <ul className="divide-y divide-gray-200 h-[75vh] overflow-y-auto">
                            {data.map((menu_item) => (
                                <li key={menu_item.id} onClick={() => handleSelectItem(menu_item)} className={`cursor-pointer hover:bg-gray-50 ${selectedItem?.id === menu_item.id ? 'bg-indigo-50' : ''}`}>
                                    <div className="px-4 py-4 sm:px-6">
                                        <div className="flex items-center justify-between">
                                            <div className="text-sm font-medium text-indigo-600 truncate">{menu_item.title}</div>
                                            <div className="ml-2 flex-shrink-0 flex">
                                                <p className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${menu_item.codapl === 'CA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                    {menu_item.codapl}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="mt-2 sm:flex sm:justify-between">
                                            <div className="sm:flex">
                                                <p className="flex items-center text-sm text-gray-500">
                                                    {menu_item.controller}
                                                </p>
                                                <p className="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                    {menu_item.action}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <aside className="lg:col-span-2 m-2">
                        <div className="sticky top-4 rounded-xl bg-white shadow-md ring-1 ring-gray-200 overflow-hidden">
                            <div className="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                <div>
                                    <h4 className="text-sm font-semibold text-gray-900">Permisos del Item</h4>
                                    {selectedItem ? (
                                        <p className="text-xs text-gray-500">Item: <span className="font-medium">{selectedItem.title}</span></p>
                                    ) : (
                                        <p className="text-xs text-gray-500">Selecciona un item para ver sus permisos</p>
                                    )}
                                </div>
                                {selectedItem && (
                                    <button onClick={savePermissions} disabled={saving} className="inline-flex items-center h-8 px-2.5 rounded-md border border-transparent text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                                        {saving ? 'Guardando...' : 'Guardar Cambios'}
                                    </button>
                                )}
                            </div>
                            <div className="p-4 max-h-[70vh] overflow-auto">
                                {loadingPermissions && <p>Cargando permisos...</p>}
                                {permissionsError && <p className="text-red-500">{permissionsError}</p>}
                                {!loadingPermissions && !permissionsError && selectedItem && (
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tipo Funcionario
                                                </th>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Puede Ver
                                                </th>
                                                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Opciones Adicionales
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {tiposFuncionarios.map(tf => {
                                                const permission = permissions.find(p => p.tipfun === tf.tipfun);
                                                return (
                                                    <tr key={tf.tipfun}>
                                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{tf.destipfun}</td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <input
                                                                type="checkbox"
                                                                className="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                                checked={permission?.can_view || false}
                                                                onChange={e => handlePermissionChange(tf.tipfun, 'can_view', e.target.checked)}
                                                            />
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <input
                                                                type="text"
                                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600 p-2"
                                                                value={permission?.opciones || ''}
                                                                onChange={e => handlePermissionChange(tf.tipfun, 'opciones', e.target.value)}
                                                            />
                                                        </td>
                                                    </tr>
                                                )
                                            })}
                                        </tbody>
                                    </table>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>

                {meta.pagination && (
                    <div className="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {/* Pagination component can be extracted and reused */}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
