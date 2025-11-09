import React from 'react';
import Badge from '../atoms/Badge';
import Button from '../atoms/Button';
import Spinner from '../atoms/Spinner';

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

interface ComponentListProps {
    componentes: Componente[];
    loading?: boolean;
    onEdit?: (id: number) => void;
    onDelete?: (id: number) => void;
    onShow?: (id: number) => void;
    onDuplicate?: (id: number) => void;
}

const ComponentList: React.FC<ComponentListProps> = ({
    componentes,
    loading = false,
    onEdit,
    onDelete,
    onShow,
    onDuplicate
}) => {
    const getTypeColor = (type: string) => {
        switch (type) {
            case 'input': return 'primary';
            case 'select': return 'success';
            case 'textarea': return 'warning';
            case 'date': return 'danger';
            case 'number': return 'secondary';
            case 'dialog': return 'default';
            default: return 'default';
        }
    };

    const getTypeLabel = (type: string) => {
        switch (type) {
            case 'input': return 'Texto';
            case 'select': return 'Select';
            case 'textarea': return 'Área';
            case 'date': return 'Fecha';
            case 'number': return 'Número';
            case 'dialog': return 'Diálogo';
            default: return type;
        }
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center py-12">
                <Spinner size="lg" />
                <span className="ml-2 text-gray-600">Cargando componentes...</span>
            </div>
        );
    }

    if (componentes.length === 0) {
        return (
            <div className="text-center py-12">
                <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 className="mt-2 text-sm font-medium text-gray-900">No hay componentes</h3>
                <p className="mt-1 text-sm text-gray-500">Comienza creando tu primer componente dinámico.</p>
            </div>
        );
    }

    return (
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
            <ul className="divide-y divide-gray-200">
                {componentes.map((componente) => (
                    <li key={componente.id} className="px-6 py-4">
                        <div className="flex items-center justify-between">
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center space-x-3">
                                    <div className="flex-1 min-w-0">
                                        <p className="text-sm font-medium text-gray-900 truncate">
                                            {componente.label}
                                        </p>
                                        <p className="text-sm text-gray-500 truncate">
                                            {componente.name}
                                        </p>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Badge variant={getTypeColor(componente.type)}>
                                            {getTypeLabel(componente.type)}
                                        </Badge>
                                        {componente.validacion?.is_required && (
                                            <Badge variant="danger" size="sm">
                                                Req
                                            </Badge>
                                        )}
                                        {componente.is_disabled && (
                                            <Badge variant="secondary" size="sm">
                                                Desh
                                            </Badge>
                                        )}
                                        {componente.is_readonly && (
                                            <Badge variant="secondary" size="sm">
                                                Solo Lect
                                            </Badge>
                                        )}
                                    </div>
                                </div>
                                <div className="mt-2 flex items-center text-sm text-gray-500">
                                    <span>Grupo {componente.group_id}</span>
                                    <span className="mx-2">•</span>
                                    <span>Orden {componente.order}</span>
                                    {componente.validacion?.pattern && (
                                        <>
                                            <span className="mx-2">•</span>
                                            <span className="text-xs bg-gray-100 px-1 py-0.5 rounded">
                                                Patrón
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>
                            <div className="flex items-center space-x-2">
                                {onShow && (
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        onClick={() => onShow(componente.id)}
                                    >
                                        Ver
                                    </Button>
                                )}
                                {onEdit && (
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        onClick={() => onEdit(componente.id)}
                                    >
                                        Editar
                                    </Button>
                                )}
                                {onDuplicate && (
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        onClick={() => onDuplicate(componente.id)}
                                    >
                                        Duplicar
                                    </Button>
                                )}
                                {onDelete && (
                                    <Button
                                        variant="danger"
                                        size="sm"
                                        onClick={() => onDelete(componente.id)}
                                    >
                                        Eliminar
                                    </Button>
                                )}
                            </div>
                        </div>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default ComponentList;
