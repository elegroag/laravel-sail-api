import React from 'react';
import Badge from '../atoms/Badge';
import Button from '../atoms/Button';

interface Componente {
    id: number;
    name: string;
    label: string;
    type: string;
    placeholder: string;
    form_type: string;
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
    validacion?: {
        pattern: string | null;
        max_length: number | null;
        min_length: number | null;
        numeric_range: string | null;
        field_size: number;
        detail_info: string | null;
        is_required: boolean;
        custom_rules: Record<string, any>;
        error_messages: Record<string, string>;
    };
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
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

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
            case 'input': return 'Campo de Texto';
            case 'select': return 'Lista Desplegable';
            case 'textarea': return 'Área de Texto';
            case 'date': return 'Campo de Fecha';
            case 'number': return 'Campo Numérico';
            case 'dialog': return 'Diálogo/Modal';
            default: return type;
        }
    };

    return (
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
            <div className="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200">
                <div>
                    <h3 className="text-lg leading-6 font-medium text-gray-900">
                        Detalles del Componente
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">
                        Información completa del componente dinámico
                    </p>
                </div>
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

                    <div>
                        <dt className="text-sm font-medium text-gray-500">Objetivo</dt>
                        <dd className="mt-1 text-sm text-gray-900">{componente.target}</dd>
                    </div>

                    {/* Estados */}
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Estados</dt>
                        <dd className="mt-1 flex flex-wrap gap-1">
                            {componente.is_disabled && (
                                <Badge variant="secondary" size="sm">Deshabilitado</Badge>
                            )}
                            {componente.is_readonly && (
                                <Badge variant="secondary" size="sm">Solo Lectura</Badge>
                            )}
                            {!componente.is_disabled && !componente.is_readonly && (
                                <span className="text-sm text-gray-500">Activo</span>
                            )}
                        </dd>
                    </div>

                    {/* Campos específicos por tipo */}
                    {componente.placeholder && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Placeholder</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.placeholder}</dd>
                        </div>
                    )}

                    {componente.default_value && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Valor por Defecto</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.default_value}</dd>
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

                    {componente.help_text && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Texto de Ayuda</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.help_text}</dd>
                        </div>
                    )}

                    {/* Campos específicos por tipo */}
                    {componente.type === 'select' && componente.data_source && componente.data_source.length > 0 && (
                        <div className="sm:col-span-2">
                            <dt className="text-sm font-medium text-gray-500">Opciones del Select</dt>
                            <dd className="mt-1">
                                <div className="space-y-1">
                                    {componente.data_source.map((option: any, index: number) => (
                                        <div key={index} className="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                                            <span>{option.label}</span>
                                            <code className="text-xs text-gray-500">{option.value}</code>
                                        </div>
                                    ))}
                                </div>
                            </dd>
                        </div>
                    )}

                    {componente.type === 'date' && componente.date_max && (
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Fecha Máxima</dt>
                            <dd className="mt-1 text-sm text-gray-900">{componente.date_max}</dd>
                        </div>
                    )}

                    {componente.type === 'number' && (
                        <>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Valor Mínimo</dt>
                                <dd className="mt-1 text-sm text-gray-900">{componente.number_min || 'Sin límite'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Valor Máximo</dt>
                                <dd className="mt-1 text-sm text-gray-900">{componente.number_max || 'Sin límite'}</dd>
                            </div>
                            <div>
                                <dt className="text-sm font-medium text-gray-500">Incremento</dt>
                                <dd className="mt-1 text-sm text-gray-900">{componente.number_step}</dd>
                            </div>
                        </>
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

                {/* Validación */}
                {componente.validacion && (
                    <div className="mt-8 border-t border-gray-200 pt-6">
                        <h4 className="text-lg font-medium text-gray-900 mb-4">Reglas de Validación</h4>
                        <dl className="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            {componente.validacion.is_required && (
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Requerido</dt>
                                    <dd className="mt-1">
                                        <Badge variant="danger">Sí</Badge>
                                    </dd>
                                </div>
                            )}

                            {componente.validacion.pattern && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Patrón Regex</dt>
                                    <dd className="mt-1 text-sm font-mono text-gray-900 bg-gray-50 p-2 rounded">
                                        {componente.validacion.pattern}
                                    </dd>
                                </div>
                            )}

                            {(componente.validacion.max_length || componente.validacion.min_length) && (
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Longitud</dt>
                                    <dd className="mt-1 text-sm text-gray-900">
                                        {componente.validacion.min_length && `Mín: ${componente.validacion.min_length}`}
                                        {componente.validacion.min_length && componente.validacion.max_length && ' • '}
                                        {componente.validacion.max_length && `Máx: ${componente.validacion.max_length}`}
                                    </dd>
                                </div>
                            )}

                            {componente.validacion.numeric_range && (
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Rango Numérico</dt>
                                    <dd className="mt-1 text-sm text-gray-900">{componente.validacion.numeric_range}</dd>
                                </div>
                            )}

                            {componente.validacion.field_size && (
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Tamaño del Campo</dt>
                                    <dd className="mt-1 text-sm text-gray-900">{componente.validacion.field_size}</dd>
                                </div>
                            )}

                            {componente.validacion.detail_info && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Información Detallada</dt>
                                    <dd className="mt-1 text-sm text-gray-900">{componente.validacion.detail_info}</dd>
                                </div>
                            )}

                            {componente.validacion.custom_rules && Object.keys(componente.validacion.custom_rules).length > 0 && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Reglas Personalizadas</dt>
                                    <dd className="mt-1">
                                        <div className="space-y-2">
                                            {Object.entries(componente.validacion.custom_rules).map(([key, value]: [string, any]) => (
                                                <div key={key} className="flex justify-between items-center p-2 bg-gray-50 rounded text-sm">
                                                    <span className="font-medium">{key}:</span>
                                                    <span>{String(value)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </dd>
                                </div>
                            )}

                            {componente.validacion.error_messages && Object.keys(componente.validacion.error_messages).length > 0 && (
                                <div className="sm:col-span-2">
                                    <dt className="text-sm font-medium text-gray-500">Mensajes de Error</dt>
                                    <dd className="mt-1">
                                        <div className="space-y-2">
                                            {Object.entries(componente.validacion.error_messages).map(([key, value]: [string, any]) => (
                                                <div key={key} className="p-2 bg-red-50 border border-red-200 rounded text-sm">
                                                    <div className="font-medium text-red-900">{key}</div>
                                                    <div className="text-red-700 mt-1">{String(value)}</div>
                                                </div>
                                            ))}
                                        </div>
                                    </dd>
                                </div>
                            )}
                        </dl>
                    </div>
                )}
            </div>
        </div>
    );
};

export default ComponentDetail;
