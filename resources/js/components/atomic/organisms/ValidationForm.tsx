import React, { useState, useEffect } from 'react';
import FormField from '../molecules/FormField';
import ActionButtons from '../molecules/ActionButtons';
import ErrorMessage from '../atoms/ErrorMessage';
import Button from '../atoms/Button';

interface ValidationData {
    componente_id: number;
    pattern: string;
    default_value: string;
    max_length: number;
    min_length: number;
    numeric_range: string;
    field_size: number;
    detail_info: string;
    is_required: boolean;
    custom_rules: Record<string, any>;
    error_messages: Record<string, string>;
}

interface ValidationFormProps {
    initialData?: Partial<ValidationData>;
    componentes: Array<{ id: number; name: string; label: string }>;
    onSubmit: (data: ValidationData) => Promise<void>;
    onCancel?: () => void;
    loading?: boolean;
    errors?: Record<string, string>;
}

const ValidationForm: React.FC<ValidationFormProps> = ({
    initialData = {},
    componentes,
    onSubmit,
    onCancel,
    loading = false,
    errors = {}
}) => {
    const [formData, setFormData] = useState<ValidationData>({
        componente_id: 0,
        pattern: '',
        default_value: '',
        max_length: 0,
        min_length: 0,
        numeric_range: '',
        field_size: 42,
        detail_info: '',
        is_required: false,
        custom_rules: {},
        error_messages: {},
        ...initialData
    });

    useEffect(() => {
        setFormData(prev => ({ ...prev, ...initialData }));
    }, [initialData]);

    const handleChange = (field: keyof ValidationData, value: any) => {
        setFormData(prev => ({ ...prev, [field]: value }));
    };

    const handleJsonChange = (field: 'custom_rules' | 'error_messages', key: string, value: string) => {
        setFormData(prev => ({
            ...prev,
            [field]: {
                ...prev[field],
                [key]: value
            }
        }));
    };

    const addCustomRule = () => {
        const key = prompt('Nombre de la regla personalizada:');
        if (key && key.trim()) {
            setFormData(prev => ({
                ...prev,
                custom_rules: {
                    ...prev.custom_rules,
                    [key.trim()]: ''
                }
            }));
        }
    };

    const removeCustomRule = (key: string) => {
        setFormData(prev => {
            const newRules = { ...prev.custom_rules };
            delete newRules[key];
            return {
                ...prev,
                custom_rules: newRules
            };
        });
    };

    const addErrorMessage = () => {
        const key = prompt('Nombre del mensaje de error (ej: pattern, required, max_length):');
        if (key && key.trim()) {
            setFormData(prev => ({
                ...prev,
                error_messages: {
                    ...prev.error_messages,
                    [key.trim()]: ''
                }
            }));
        }
    };

    const removeErrorMessage = (key: string) => {
        setFormData(prev => {
            const newMessages = { ...prev.error_messages };
            delete newMessages[key];
            return {
                ...prev,
                error_messages: newMessages
            };
        });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await onSubmit(formData);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {Object.keys(errors).length > 0 && (
                <ErrorMessage message="Por favor, corrige los errores en el formulario." />
            )}

            <div className="grid grid-cols-6 gap-6">
                {/* Componente */}
                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="select"
                        label="Componente"
                        name="componente_id"
                        value={formData.componente_id?.toString()}
                        onChange={(e) => handleChange('componente_id', Number(e.target.value))}
                        options={componentes.map(comp => ({
                            value: comp.id,
                            label: `${comp.label} (${comp.name})`
                        }))}
                        error={errors.componente_id}
                        helperText="Componente al que se aplicarán estas reglas de validación"
                        required
                    />
                </div>

                {/* Patrón regex */}
                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Patrón Regex"
                        name="pattern"
                        value={formData.pattern}
                        onChange={(e) => handleChange('pattern', e.target.value)}
                        error={errors.pattern}
                        helperText="Expresión regular para validar el formato del campo"
                        placeholder="ej: /^[a-zA-Z\s]+$/"
                    />
                </div>

                {/* Longitudes */}
                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Longitud Máxima"
                        name="max_length"
                        value={formData.max_length || ''}
                        onChange={(e) => handleChange('max_length', Number(e.target.value) || 0)}
                        error={errors.max_length}
                        helperText="Número máximo de caracteres"
                    />
                </div>

                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Longitud Mínima"
                        name="min_length"
                        value={formData.min_length || ''}
                        onChange={(e) => handleChange('min_length', Number(e.target.value) || 0)}
                        error={errors.min_length}
                        helperText="Número mínimo de caracteres"
                    />
                </div>

                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Tamaño del Campo"
                        name="field_size"
                        value={formData.field_size}
                        onChange={(e) => handleChange('field_size', Number(e.target.value))}
                        error={errors.field_size}
                        helperText="Tamaño visual del campo (1-100)"
                        required
                    />
                </div>

                {/* Rango numérico */}
                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Rango Numérico"
                        name="numeric_range"
                        value={formData.numeric_range}
                        onChange={(e) => handleChange('numeric_range', e.target.value)}
                        error={errors.numeric_range}
                        helperText="Rango válido para campos numéricos (formato: min-max)"
                        placeholder="ej: 1-100"
                    />
                </div>

                {/* Valor por defecto */}
                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Valor por Defecto"
                        name="default_value"
                        value={formData.default_value}
                        onChange={(e) => handleChange('default_value', e.target.value)}
                        error={errors.default_value}
                        helperText="Valor que se asignará por defecto para validación"
                    />
                </div>

                {/* Información detallada */}
                <div className="col-span-6">
                    <FormField
                        type="textarea"
                        label="Información Detallada"
                        name="detail_info"
                        value={formData.detail_info}
                        onChange={(e) => handleChange('detail_info', e.target.value)}
                        error={errors.detail_info}
                        helperText="Información adicional sobre las reglas de validación"
                        rows={3}
                    />
                </div>

                {/* Campo requerido */}
                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="checkbox"
                        label="Campo requerido"
                        name="is_required"
                        checked={formData.is_required}
                        onChange={(e) => handleChange('is_required', e.target.checked)}
                        helperText="Si este campo debe ser obligatorio"
                    />
                </div>

                {/* Reglas personalizadas */}
                <div className="col-span-6">
                    <div className="border-t border-gray-200 pt-6">
                        <div className="flex justify-between items-center mb-4">
                            <h4 className="text-sm font-medium text-gray-900">Reglas Personalizadas</h4>
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={addCustomRule}
                            >
                                + Agregar Regla
                            </Button>
                        </div>
                        <div className="space-y-3">
                            {Object.entries(formData.custom_rules).map(([key, value]: [string, any]) => (
                                <div key={key} className="flex gap-3 items-end">
                                    <div className="flex-1">
                                        <label className="block text-sm font-medium text-gray-700">{key}</label>
                                        <input
                                            type="text"
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            value={value}
                                            onChange={(e) => handleJsonChange('custom_rules', key, e.target.value)}
                                            placeholder="Valor de la regla"
                                        />
                                    </div>
                                    <Button
                                        type="button"
                                        variant="danger"
                                        size="sm"
                                        onClick={() => removeCustomRule(key)}
                                    >
                                        ✕
                                    </Button>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Mensajes de error personalizados */}
                <div className="col-span-6">
                    <div className="border-t border-gray-200 pt-6">
                        <div className="flex justify-between items-center mb-4">
                            <h4 className="text-sm font-medium text-gray-900">Mensajes de Error Personalizados</h4>
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={addErrorMessage}
                            >
                                + Agregar Mensaje
                            </Button>
                        </div>
                        <div className="space-y-3">
                            {Object.entries(formData.error_messages).map(([key, value]: [string, any]) => (
                                <div key={key} className="flex gap-3 items-end">
                                    <div className="flex-1">
                                        <label className="block text-sm font-medium text-gray-700">{key}</label>
                                        <input
                                            type="text"
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2"
                                            value={value}
                                            onChange={(e) => handleJsonChange('error_messages', key, e.target.value)}
                                            placeholder="Mensaje de error personalizado"
                                        />
                                    </div>
                                    <Button
                                        type="button"
                                        variant="danger"
                                        size="sm"
                                        onClick={() => removeErrorMessage(key)}
                                    >
                                        ✕
                                    </Button>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <div className="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <ActionButtons
                    actions={[
                        {
                            label: 'Cancelar',
                            onClick: onCancel || (() => {}),
                            variant: 'secondary',
                            disabled: loading
                        },
                        {
                            label: 'Guardar Validación',
                            onClick: () => {}, // Se maneja en el onSubmit del form
                            variant: 'primary',
                            loading,
                            disabled: loading
                        }
                    ]}
                    align="right"
                />
            </div>
        </form>
    );
};

export default ValidationForm;
