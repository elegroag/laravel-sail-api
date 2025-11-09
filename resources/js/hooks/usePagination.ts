import { useState, useCallback, useMemo } from 'react';
import type { UsePaginationReturn } from '@/types/componentes';

interface UsePaginationProps {
    initialPage?: number;
    initialPerPage?: number;
    totalItems?: number;
}

interface PaginationState {
    currentPage: number;
    perPage: number;
    totalItems: number;
}

export const usePagination = ({
    initialPage = 1,
    initialPerPage = 10,
    totalItems = 0
}: UsePaginationProps = {}): UsePaginationReturn => {
    const [state, setState] = useState<PaginationState>({
        currentPage: initialPage,
        perPage: initialPerPage,
        totalItems
    });

    const totalPages = useMemo(() => {
        return Math.ceil(state.totalItems / state.perPage);
    }, [state.totalItems, state.perPage]);

    const hasNextPage = useMemo(() => {
        return state.currentPage < totalPages;
    }, [state.currentPage, totalPages]);

    const hasPrevPage = useMemo(() => {
        return state.currentPage > 1;
    }, [state.currentPage]);

    const from = useMemo(() => {
        return state.totalItems === 0 ? 0 : (state.currentPage - 1) * state.perPage + 1;
    }, [state.currentPage, state.perPage, state.totalItems]);

    const to = useMemo(() => {
        return Math.min(state.currentPage * state.perPage, state.totalItems);
    }, [state.currentPage, state.perPage, state.totalItems]);

    const goToPage = useCallback((page: number) => {
        const validPage = Math.max(1, Math.min(page, totalPages));
        setState(prev => ({ ...prev, currentPage: validPage }));
    }, [totalPages]);

    const nextPage = useCallback(() => {
        if (hasNextPage) {
            setState(prev => ({ ...prev, currentPage: prev.currentPage + 1 }));
        }
    }, [hasNextPage]);

    const prevPage = useCallback(() => {
        if (hasPrevPage) {
            setState(prev => ({ ...prev, currentPage: prev.currentPage - 1 }));
        }
    }, [hasPrevPage]);

    const firstPage = useCallback(() => {
        setState(prev => ({ ...prev, currentPage: 1 }));
    }, []);

    const lastPage = useCallback(() => {
        setState(prev => ({ ...prev, currentPage: totalPages }));
    }, [totalPages]);

    const setPerPage = useCallback((perPage: number) => {
        setState(prev => ({
            ...prev,
            perPage,
            currentPage: 1 // Reset to first page when changing perPage
        }));
    }, []);

    const setTotalItems = useCallback((totalItems: number) => {
        setState(prev => ({ ...prev, totalItems }));
    }, []);

    const reset = useCallback(() => {
        setState({
            currentPage: initialPage,
            perPage: initialPerPage,
            totalItems
        });
    }, [initialPage, initialPerPage, totalItems]);

    const getVisiblePages = useCallback(() => {
        const delta = 2;
        const range = [];
        const rangeWithDots = [];

        for (let i = Math.max(2, state.currentPage - delta); i <= Math.min(totalPages - 1, state.currentPage + delta); i++) {
            range.push(i);
        }

        if (state.currentPage - delta > 2) {
            rangeWithDots.push(1, '...');
        } else {
            rangeWithDots.push(1);
        }

        rangeWithDots.push(...range);

        if (state.currentPage + delta < totalPages - 1) {
            rangeWithDots.push('...', totalPages);
        } else if (totalPages > 1) {
            rangeWithDots.push(totalPages);
        }

        return rangeWithDots;
    }, [state.currentPage, totalPages]);

    return {
        // State
        currentPage: state.currentPage,
        perPage: state.perPage,
        totalItems: state.totalItems,
        totalPages,

        // Computed values
        hasNextPage,
        hasPrevPage,
        from,
        to,

        // Actions
        goToPage,
        nextPage,
        prevPage,
        firstPage,
        lastPage,
        setPerPage,
        setTotalItems,
        reset,

        // Helpers
        getVisiblePages
    };
};
