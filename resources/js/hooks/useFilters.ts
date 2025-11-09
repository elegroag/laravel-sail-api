import { useState, useCallback, useMemo } from 'react';
import type { UseFiltersReturn } from '@/types/componentes';

interface UseFiltersProps {
    initialFilters?: Record<string, string>;
    searchKey?: string;
}

export const useFilters = ({
    initialFilters = {},
    searchKey = 'q'
}: UseFiltersProps = {}): UseFiltersReturn => {
    const [filters, setFilters] = useState<Record<string, string>>(initialFilters);
    const [searchValue, setSearchValue] = useState('');

    const updateFilter = useCallback((key: string, value: string) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    }, []);

    const updateSearch = useCallback((value: string) => {
        setSearchValue(value);
    }, []);

    const clearFilter = useCallback((key: string) => {
        setFilters(prev => {
            const newFilters = { ...prev };
            delete newFilters[key];
            return newFilters;
        });
    }, []);

    const clearAllFilters = useCallback(() => {
        setFilters({});
        setSearchValue('');
    }, []);

    const hasActiveFilters = useMemo(() => {
        return Object.values(filters).some(value => value !== '') || searchValue.trim() !== '';
    }, [filters, searchValue]);

    const getQueryParams = useCallback(() => {
        const params: Record<string, string> = {};

        // Add search if present
        if (searchValue.trim()) {
            params[searchKey] = searchValue.trim();
        }

        // Add filters
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== '') {
                params[key] = value;
            }
        });

        return params;
    }, [filters, searchValue, searchKey]);

    const applyFilters = useCallback((newFilters: Record<string, string>) => {
        setFilters(newFilters);
    }, []);

    const getFilterValue = useCallback((key: string) => {
        return filters[key] || '';
    }, [filters]);

    const getActiveFilterCount = useMemo(() => {
        let count = 0;
        if (searchValue.trim()) count++;
        count += Object.values(filters).filter(value => value !== '').length;
        return count;
    }, [filters, searchValue]);

    return {
        // State
        filters,
        searchValue,

        // Computed
        hasActiveFilters,
        activeFilterCount: getActiveFilterCount,

        // Actions
        updateFilter,
        updateSearch,
        clearFilter,
        clearAllFilters,
        applyFilters,
        getFilterValue,

        // Helpers
        getQueryParams
    };
};
