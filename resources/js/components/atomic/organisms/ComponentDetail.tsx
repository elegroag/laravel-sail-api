import React from 'react';
import Badge from '../atoms/Badge';
import Button from '../atoms/Button';

interface Componente {
    id: number;
    name: string;
    label: string;
    type: string;
    form_type?: string;
    group_id: number;
    order: number;
    default_value: string;
    is_disabled: boolean;
    is_readonly: boolean;
    data_source: any[];
    css_classes: string;
    help_text: string;
    target: number;
    event_config: any;
    search_type: string;
    date_max: string;
    number_min: number;
    number_max: number;
    number_step: number;
    created_at: string;
    updated_at: string;
}

interface ComponentDetailProps {
    componente: Componente;
    onEdit?: () => void;
    onDelete?: () => void;
    onDuplicate?: () => void;
    onBack?: () => void;
}

const ComponentDetail: React.FC<ComponentDetailProps> = ({
    componente,
    onEdit,
    onDelete,
    onDuplicate,
    onBack
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
            case 'input': return 'Campo de texto';
            case 'select': return 'Lista desplegable';
            case 'textarea': return 'Área de texto';
            case 'date': return 'Fecha';
            case 'number': return 'Número';
            case 'dialog': return 'Diálogo';
            default: return type;
        }
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return (
        <div className="bg-white shadow overflow-hidden sm:rounded-lg">
            <div className="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div className="flex items-center justify-between">
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        {componente.label}
                    </h3>
                    <div className="flex space-x-2">
                        {onBack && (
                            <Button variant="secondary" onClick={onBack}>
                                Volver
                            </Button>
                        )}
                        {onEdit && (
                            <Button variant="secondary" onClick={onEdit}>
                                Editar
                            </Button>
                        )}
                        {onDuplicate && (
                            <Button variant="secondary" onClick={onDuplicate}>
                                Duplicar
                            </Button>
                        )}
                        {onDelete && (
                            <Button variant="danger" onClick={onDelete}>
                                Eliminar
                            </Button>
                        )}
                    </div>
                </div>
            </div>

            <div className="px-4 py-5 sm:px-6">
                <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    {/* Información básica */}
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.name}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Etiqueta</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.label}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Tipo</dt>
                        <dd className="mt-1">
                            <Badge variant={getTypeColor(componente.type)}>
                                {getTypeLabel(componente.type)}
                            </Badge>
                        </dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Tipo de Formulario</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.form_type}</dd>
                    </div>

                    {/* Configuración de layout */}
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Grupo</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.group_id}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Orden</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.order}</dd>
                    </div>

                    {/* Estado */}
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Deshabilitado</dt>
                        <dd className="mt-1">
                            <Badge variant={componente.is_disabled ? 'danger' : 'success'}>
                                {componente.is_disabled ? 'Sí' : 'No'}
                            </Badge>
                        </dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Solo Lectura</dt>
                        <dd className="mt-1">
                            <Badge variant={componente.is_readonly ? 'warning' : 'success'}>
                                {componente.is_readonly ? 'Sí' : 'No'}
                            </Badge>
                        </dd>
                    </div>

                    {/* Configuración adicional */}
                    {componente.default_value && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Valor por Defecto</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.default_value}</dd>
                        </div>
                    )}

                    {componente.help_text && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Texto de Ayuda</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.help_text}</dd>
                        </div>
                    )}

                    {componente.css_classes && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Clases CSS</dt>
                            <dd className="mt-1 text-sm font-mono text-gray-900 bg-gray-50 p-2 rounded">
                                {componente.css_classes}
                            </dd>
                        </div>
                    )}

                    {/* Configuración específica por tipo */}
                    {componente.date_max && (
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Fecha Máxima</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.date_max}</dd>
                        </div>
                    )}

                    {(componente.number_min !== undefined || componente.number_max !== undefined) && (
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Rango Numérico</dt>
                            <dd className="mt-1 text-sm text-gray-900">
                                {componente.number_min !== undefined && `Mín: ${componente.number_min}`}
                                {componente.number_min !== undefined && componente.number_max !== undefined && ' • '}
                                {componente.number_max !== undefined && `Máx: ${componente.number_max}`}
                            </dd>
                        </div>
                    )}

                    {componente.number_step && (
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Paso Numérico</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.number_step}</dd>
                        </div>
                    )}

                    {/* Fechas */}
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Creado</dt>
                        <dd className="mt-1 text-sm text-gray-900">{formatDate(componente.created_at)}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Actualizado</dt>
                        <dd className="mt-1 text-sm text-gray-900">{formatDate(componente.updated_at)}</dd>
                    </div>
                </dl>
            </div>
        </div>
    );
};

export default ComponentDetail;
