import React from 'react';
import Button from '../atoms/Button';

interface PaginationControlsProps {
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
    from: number | null;
    to: number | null;
    onPageChange: (page: number) => void;
    onPerPageChange: (perPage: number) => void;
    loading?: boolean;
}

const PaginationControls: React.FC<PaginationControlsProps> = ({
    currentPage,
    lastPage,
    perPage,
    total,
    from,
    to,
    onPageChange,
    onPerPageChange,
    loading = false
}) => {
    const perPageOptions = [10, 15, 25, 50, 100];

    const getVisiblePages = () => {
        const delta = 2;
        const range = [];
        const rangeWithDots = [];

        for (let i = Math.max(2, currentPage - delta); i <= Math.min(lastPage - 1, currentPage + delta); i++) {
            range.push(i);
        }

        if (currentPage - delta > 2) {
            rangeWithDots.push(1, '...');
        } else {
            rangeWithDots.push(1);
        }

        rangeWithDots.push(...range);

        if (currentPage + delta < lastPage - 1) {
            rangeWithDots.push('...', lastPage);
        } else if (lastPage > 1) {
            rangeWithDots.push(lastPage);
        }

        return rangeWithDots;
    };

    if (total === 0) return null;

    return (
        <div className="bg-white px-4 py-3 border-t border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
                <div className="text-sm text-gray-700">
                    Mostrando {from || 0}–{to || 0} de {total}
                </div>
                <div className="flex items-center gap-2">
                    <label htmlFor="per_page" className="text-sm text-gray-600">Por página</label>
                    <select
                        id="per_page"
                        className="rounded-md border border-gray-300 px-2 py-1 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        value={perPage}
                        onChange={(e) => onPerPageChange(Number(e.target.value))}
                        disabled={loading}
                    >
                        {perPageOptions.map(n => (
                            <option key={n} value={n}>{n}</option>
                        ))}
                    </select>
                </div>
            </div>

            <div className="inline-flex items-center gap-2">
                <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => onPageChange(1)}
                    disabled={currentPage === 1 || loading}
                >
                    Primera
                </Button>

                <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => onPageChange(Math.max(1, currentPage - 1))}
                    disabled={currentPage === 1 || loading}
                >
                    Anterior
                </Button>

                {/* Páginas numeradas */}
                <div className="hidden sm:inline-flex gap-1">
                    {getVisiblePages().map((page, index) => (
                        <React.Fragment key={index}>
                            {page === '...' ? (
                                <span className="px-3 py-1 text-sm text-gray-500">...</span>
                            ) : (
                                <Button
                                    variant={page === currentPage ? 'primary' : 'secondary'}
                                    size="sm"
                                    onClick={() => onPageChange(page as number)}
                                    disabled={loading}
                                >
                                    {page}
                                </Button>
                            )}
                        </React.Fragment>
                    ))}
                </div>

                {/* Versión móvil simplificada */}
                <div className="sm:hidden">
                    <span className="px-3 py-1 text-sm text-gray-700">
                        {currentPage} de {lastPage}
                    </span>
                </div>

                <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => onPageChange(Math.min(lastPage, currentPage + 1))}
                    disabled={currentPage === lastPage || loading}
                >
                    Siguiente
                </Button>

                <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => onPageChange(lastPage)}
                    disabled={currentPage === lastPage || loading}
                >
                    Última
                </Button>
            </div>
        </div>
    );
};

export default PaginationControls;
