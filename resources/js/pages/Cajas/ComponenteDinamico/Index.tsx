import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentList, FilterBar, PaginationControls, ActionButtons } from '@/components/atomic';
import { useFilters } from '@/hooks/useFilters';
import { usePagination } from '@/hooks/usePagination';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';

interface Componente {
    id: number;
    name: string;
    label: string;
    type: string;
    group_id: number;
    order: number;
    is_disabled: boolean;
    is_readonly: boolean;
    validacion?: {
        is_required: boolean;
        pattern: string | null;
    };
}

interface Props {
    componentes_dinamicos: {
        data: Componente[];
        meta: {
            total_componentes: number;
            pagination: {
                current_page: number;
                last_page: number;
                per_page: number;
                from: number | null;
                to: number | null;
                total: number;
            };
        };
    };
}

export default function Index({ componentes_dinamicos }: Props) {
    const [loading, setLoading] = useState(false);
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [pendingDelete, setPendingDelete] = useState<{ id: number; name: string } | null>(null);

    const { filters, searchValue, updateFilter, updateSearch, clearAllFilters, getQueryParams } = useFilters({
        initialFilters: {
            type: '',
            group_id: '',
            has_validation: '',
        }
    });

    const { currentPage, perPage, totalItems, totalPages, from, to, goToPage, setPerPage, setTotalItems } = usePagination({
        initialPage: componentes_dinamicos.meta.pagination.current_page,
        initialPerPage: componentes_dinamicos.meta.pagination.per_page,
        totalItems: componentes_dinamicos.meta.total_componentes
    });

    // Update pagination when data changes
    useEffect(() => {
        setTotalItems(componentes_dinamicos.meta.total_componentes);
    }, [componentes_dinamicos.meta.total_componentes, setTotalItems]);

    const handleSearch = () => {
        performSearch();
    };

    const handleFilterChange = (key: string, value: string) => {
        updateFilter(key, value);
        // Auto-search when filters change
        setTimeout(performSearch, 300);
    };

    const performSearch = () => {
        setLoading(true);
        const params = {
            ...getQueryParams(),
            page: 1, // Reset to first page on search
            per_page: perPage
        };

        router.get('/cajas/componente-dinamico', params, {
            preserveState: true,
            onFinish: () => setLoading(false)
        });
    };

    const handlePageChange = (page: number) => {
        goToPage(page);
        setLoading(true);
        const params = {
            ...getQueryParams(),
            page,
            per_page: perPage
        };

        router.get('/cajas/componente-dinamico', params, {
            preserveState: true,
            onFinish: () => setLoading(false)
        });
    };

    const handlePerPageChange = (newPerPage: number) => {
        setPerPage(newPerPage);
        setLoading(true);
        const params = {
            ...getQueryParams(),
            page: 1, // Reset to first page
            per_page: newPerPage
        };

        router.get('/cajas/componente-dinamico', params, {
            preserveState: true,
            onFinish: () => setLoading(false)
        });
    };

    const handleEdit = (id: number) => {
        router.visit(`/cajas/componente-dinamico/${id}/edit`);
    };

    const handleDelete = (id: number) => {
        const comp = componentes_dinamicos.data.find(c => c.id === id);
        setPendingDelete({ id, name: comp?.name || 'componente' });
        setConfirmOpen(true);
    };

    const confirmDelete = async () => {
        if (!pendingDelete) return;
        setLoading(true);
        try {
            await router.delete(`/cajas/componente-dinamico/${pendingDelete.id}`, {
                onFinish: () => {
                    setConfirmOpen(false);
                    setPendingDelete(null);
                    setLoading(false);
                }
            });
        } catch {
            setLoading(false);
        }
    };

    const handleShow = (id: number) => {
        router.visit(`/cajas/componente-dinamico/${id}/show`);
    };

    const handleDuplicate = (id: number) => {
        router.post(`/cajas/componente-dinamico/${id}/duplicate`, {}, {
            onSuccess: () => {
                // Refresh the list
                router.reload();
            }
        });
    };

    const handleValidations = (id: number) => {
        router.visit(`/cajas/componente-validacion?componente_id=${id}`);
    };

    const filterOptions = [
        {
            key: 'type',
            label: 'Tipo',
            value: filters.type,
            options: [
                { value: '', label: 'Todos los tipos' },
                { value: 'input', label: 'Campo de Texto' },
                { value: 'select', label: 'Lista Desplegable' },
                { value: 'textarea', label: 'Área de Texto' },
                { value: 'date', label: 'Campo de Fecha' },
                { value: 'number', label: 'Campo Numérico' },
                { value: 'dialog', label: 'Diálogo' },
            ],
            onChange: (value: string) => handleFilterChange('type', value)
        },
        {
            key: 'group_id',
            label: 'Grupo',
            value: filters.group_id,
            onChange: (value: string) => handleFilterChange('group_id', value)
        }
        ,
        {
            key: 'has_validation',
            label: 'Validación',
            value: filters.has_validation,
            options: [
                { value: '', label: 'Todos' },
                { value: '1', label: 'Con validación' },
                { value: '0', label: 'Sin validación' },
            ],
            onChange: (value: string) => handleFilterChange('has_validation', value)
        }
    ];

    return (
        <AppLayout title="Componentes Dinámicos">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">Componentes Dinámicos</h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">Gestiona los componentes reutilizables del sistema</p>
                    </div>
                    <ActionButtons
                        actions={[
                            {
                                label: 'Nuevo Componente',
                                onClick: () => router.visit('/cajas/componente-dinamico/create'),
                                variant: 'primary'
                            }
                        ]}
                    />
                </div>

                {/* Estadísticas */}
                <div className="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-indigo-600">{componentes_dinamicos.meta.total_componentes}</div>
                            <div className="text-sm text-gray-500">Total Componentes</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">{componentes_dinamicos.data.filter(c => c.validacion?.is_required).length}</div>
                            <div className="text-sm text-gray-500">Requeridos</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-orange-600">{componentes_dinamicos.data.filter(c => c.type === 'select').length}</div>
                            <div className="text-sm text-gray-500">Selects</div>
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="px-4 py-5 sm:px-6">
                    <FilterBar
                        searchValue={searchValue}
                        onSearchChange={updateSearch}
                        onSearchSubmit={handleSearch}
                        filters={filterOptions}
                        onClearFilters={clearAllFilters}
                        loading={loading}
                    />
                </div>

                {/* Component List */}
                <div className="px-4 py-5 sm:px-6">
                    <ComponentList
                        componentes={componentes_dinamicos.data}
                        loading={loading}
                        onEdit={handleEdit}
                        onDelete={handleDelete}
                        onShow={handleShow}
                        onDuplicate={handleDuplicate}
                        onValidations={handleValidations}
                    />
                </div>

                {/* Pagination */}
                <div className="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <PaginationControls
                        currentPage={currentPage}
                        lastPage={totalPages}
                        perPage={perPage}
                        total={totalItems}
                        from={from}
                        to={to}
                        onPageChange={handlePageChange}
                        onPerPageChange={handlePerPageChange}
                        loading={loading}
                    />
                </div>
            </div>
            {/* Modal de confirmación de eliminación */}
            <Dialog open={confirmOpen} onOpenChange={setConfirmOpen}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar eliminación</DialogTitle>
                    <DialogDescription>
                        Esta acción eliminará definitivamente el componente "{pendingDelete?.name}". No podrás deshacerla.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose asChild>
                        <button
                            type="button"
                            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            disabled={loading}
                        >
                            Cancelar
                        </button>
                    </DialogClose>
                    <button
                        type="button"
                        onClick={confirmDelete}
                        disabled={loading}
                        className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                    >
                        {loading ? 'Eliminando...' : 'Eliminar'}
                    </button>
                </DialogFooter>
            </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
