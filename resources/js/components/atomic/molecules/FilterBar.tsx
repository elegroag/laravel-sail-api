import React, { memo, useState } from 'react';
import FormField from './FormField';
import Button from '../atoms/Button';
import { SearchIcon, FilterIcon, XIcon } from '@heroicons/react/outline';

interface FilterOption {
    key: string;
    label: string;
    value: string;
    options?: Array<{ value: string; label: string }>;
    onChange: (value: string) => void;
}

interface FilterBarProps {
    searchValue: string;
    onSearchChange: (value: string) => void;
    onSearchSubmit: () => void;
    filters: FilterOption[];
    onClearFilters: () => void;
    loading?: boolean;
    placeholder?: string;
    showAdvancedFilters?: boolean;
    className?: string;
}

const FilterBar = memo<FilterBarProps>(({
    searchValue,
    onSearchChange,
    onSearchSubmit,
    filters,
    onClearFilters,
    loading = false,
    placeholder = 'Buscar...',
    showAdvancedFilters = true,
    className = ''
}) => {
    const [showFilters, setShowFilters] = useState(false);

    const hasActiveFilters = filters.some(filter => filter.value !== '') || searchValue.trim() !== '';

    const handleSearchKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            onSearchSubmit();
        }
    };

    return (
        <div className={`bg-white border border-gray-200 rounded-lg shadow-sm ${className}`}>
            {/* Main Search Bar */}
            <div className="p-4">
                <div className="flex items-center space-x-4">
                    <div className="flex-1 relative">
                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <SearchIcon className="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            type="text"
                            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder={placeholder}
                            value={searchValue}
                            onChange={(e) => onSearchChange(e.target.value)}
                            onKeyPress={handleSearchKeyPress}
                        />
                    </div>

                    <Button
                        variant="primary"
                        size="sm"
                        onClick={onSearchSubmit}
                        loading={loading}
                        disabled={loading}
                    >
                        Buscar
                    </Button>

                    {showAdvancedFilters && filters.length > 0 && (
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={() => setShowFilters(!showFilters)}
                        >
                            <FilterIcon className="h-4 w-4 mr-2" />
                            Filtros
                            {hasActiveFilters && (
                                <span className="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {filters.filter(f => f.value !== '').length + (searchValue.trim() ? 1 : 0)}
                                </span>
                            )}
                        </Button>
                    )}

                    {hasActiveFilters && (
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={onClearFilters}
                        >
                            <XIcon className="h-4 w-4 mr-2" />
                            Limpiar
                        </Button>
                    )}
                </div>
            </div>

            {/* Advanced Filters Panel */}
            {showAdvancedFilters && showFilters && filters.length > 0 && (
                <div className="border-t border-gray-200 p-4 bg-gray-50">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {filters.map((filter) => (
                            <div key={filter.key} className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700">
                                    {filter.label}
                                </label>
                                {filter.options ? (
                                    <select
                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={filter.value}
                                        onChange={(e) => filter.onChange(e.target.value)}
                                    >
                                        <option value="">Todos</option>
                                        {filter.options.map((option) => (
                                            <option key={option.value} value={option.value}>
                                                {option.label}
                                            </option>
                                        ))}
                                    </select>
                                ) : (
                                    <input
                                        type="text"
                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        value={filter.value}
                                        onChange={(e) => filter.onChange(e.target.value)}
                                        placeholder={`Filtrar por ${filter.label.toLowerCase()}`}
                                    />
                                )}
                            </div>
                        ))}
                    </div>

                    <div className="mt-4 flex justify-end space-x-2">
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={() => setShowFilters(false)}
                        >
                            Cerrar
                        </Button>
                        <Button
                            variant="primary"
                            size="sm"
                            onClick={() => {
                                onSearchSubmit();
                                setShowFilters(false);
                            }}
                            loading={loading}
                        >
                            Aplicar Filtros
                        </Button>
                    </div>
                </div>
            )}
        </div>
    );
});

FilterBar.displayName = 'FilterBar';

export default FilterBar;
