import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ComponentList, FilterBar, PaginationControls, ActionButtons } from '@/components/atomic';
import { useFilters } from '@/hooks/useFilters';
import { usePagination } from '@/hooks/usePagination';

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

    const { filters, searchValue, updateFilter, updateSearch, clearAllFilters, getQueryParams } = useFilters({
        initialFilters: {
            type: '',
            group_id: '',
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
        if (confirm('¿Estás seguro de que deseas eliminar este componente?')) {
            router.delete(`/cajas/componente-dinamico/${id}`, {
                onSuccess: () => {
                    // Data will be refreshed automatically
                }
            });
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
        </AppLayout>
    );
}
