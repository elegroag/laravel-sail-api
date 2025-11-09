import React, { useState, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
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
    const [selectedComponente, setSelectedComponente] = useState<Componente | null>(null);

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

        router.get('/mercurio/componente-dinamico', params, {
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

        router.get('/mercurio/componente-dinamico', params, {
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

        router.get('/mercurio/componente-dinamico', params, {
            preserveState: true,
            onFinish: () => setLoading(false)
        });
    };

    const handleEdit = (id: number) => {
        router.visit(`/mercurio/componente-dinamico/${id}/edit`);
    };

    const handleDelete = (id: number) => {
        if (confirm('¿Estás seguro de que deseas eliminar este componente?')) {
            router.delete(`/mercurio/componente-dinamico/${id}`, {
                onSuccess: () => {
                    // Data will be refreshed automatically
                }
            });
        }
    };

    const handleShow = (id: number) => {
        router.visit(`/mercurio/componente-dinamico/${id}/show`);
    };

    const handleDuplicate = (id: number) => {
        router.post(`/mercurio/componente-dinamico/${id}/duplicate`, {}, {
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
            <div className="space-y-6">
                {/* Header */}
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Componentes Dinámicos</h1>
                        <p className="text-gray-600">Gestiona los componentes reutilizables del sistema</p>
                    </div>
                    <ActionButtons
                        actions={[
                            {
                                label: 'Nuevo Componente',
                                onClick: () => router.visit('/mercurio/componente-dinamico/create'),
                                variant: 'primary'
                            }
                        ]}
                    />
                </div>

                {/* Statistics */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div className="bg-white p-4 rounded-lg shadow">
                        <div className="text-2xl font-bold text-blue-600">{componentes_dinamicos.meta.total_componentes}</div>
                        <div className="text-sm text-gray-600">Total Componentes</div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow">
                        <div className="text-2xl font-bold text-green-600">
                            {componentes_dinamicos.data.filter(c => c.validacion?.is_required).length}
                        </div>
                        <div className="text-sm text-gray-600">Campos Requeridos</div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow">
                        <div className="text-2xl font-bold text-orange-600">
                            {componentes_dinamicos.data.filter(c => c.type === 'select').length}
                        </div>
                        <div className="text-sm text-gray-600">Selects</div>
                    </div>
                    <div className="bg-white p-4 rounded-lg shadow">
                        <div className="text-2xl font-bold text-purple-600">
                            {componentes_dinamicos.data.filter(c => c.validacion?.pattern).length}
                        </div>
                        <div className="text-sm text-gray-600">Con Patrón</div>
                    </div>
                </div>

                {/* Filters */}
                <FilterBar
                    searchValue={searchValue}
                    onSearchChange={updateSearch}
                    onSearchSubmit={handleSearch}
                    filters={filterOptions}
                    onClearFilters={clearAllFilters}
                    loading={loading}
                />

                {/* Component List */}
                <ComponentList
                    componentes={componentes_dinamicos.data}
                    loading={loading}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    onShow={handleShow}
                    onDuplicate={handleDuplicate}
                />

                {/* Pagination */}
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
        </AppLayout>
    );
}
