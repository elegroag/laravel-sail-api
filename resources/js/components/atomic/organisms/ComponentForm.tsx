import React, { useState, useEffect } from 'react';
import FormField from '../molecules/FormField';
import DataSourceEditor from '../molecules/DataSourceEditor';
import ActionButtons from '../molecules/ActionButtons';
import ErrorMessage from '../atoms/ErrorMessage';

interface ComponentData {
    name: string;
    type: string;
    label: string;
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
}

interface ComponentFormProps {
    initialData?: Partial<ComponentData>;
    onSubmit: (data: ComponentData) => Promise<void>;
    onCancel?: () => void;
    loading?: boolean;
    errors?: Record<string, string>;
}

const COMPONENT_TYPES = [
    { value: 'input', label: 'Campo de Texto' },
    { value: 'select', label: 'Lista Desplegable' },
    { value: 'textarea', label: 'Área de Texto' },
    { value: 'date', label: 'Campo de Fecha' },
    { value: 'number', label: 'Campo Numérico' },
    { value: 'dialog', label: 'Diálogo/Modal' },
];

const ComponentForm: React.FC<ComponentFormProps> = ({
    initialData = {},
    onSubmit,
    onCancel,
    loading = false,
    errors = {}
}) => {
    const [formData, setFormData] = useState<ComponentData>({
        name: '',
        type: 'input',
        label: '',
        placeholder: '',
        form_type: 'input',
        group_id: 1,
        order: 1,
        default_value: '',
        is_disabled: false,
        is_readonly: false,
        data_source: [],
        css_classes: '',
        help_text: '',
        target: -1,
        event_config: {},
        search_type: '',
        date_max: '',
        number_min: 0,
        number_max: 0,
        number_step: 1,
        ...initialData
    });

    useEffect(() => {
        setFormData(prev => ({ ...prev, ...initialData }));
    }, [initialData]);

    const handleChange = (field: keyof ComponentData, value: any) => {
        setFormData(prev => ({ ...prev, [field]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await onSubmit(formData);
    };

    const renderTypeSpecificFields = () => {
        switch (formData.type) {
            case 'select':
                return (
                    <div className="col-span-6">
                        <DataSourceEditor
                            options={formData.data_source || []}
                            onChange={(options) => handleChange('data_source', options)}
                            error={errors.data_source}
                        />
                    </div>
                );

            case 'date':
                return (
                    <div className="col-span-6 sm:col-span-3">
                        <FormField
                            type="input"
                            inputType="date"
                            label="Fecha Máxima Permitida"
                            name="date_max"
                            value={formData.date_max}
                            onChange={(e) => handleChange('date_max', e.target.value)}
                            error={errors.date_max}
                            helperText="Fecha máxima que se puede seleccionar"
                        />
                    </div>
                );

            case 'number':
                return (
                    <>
                        <div className="col-span-6 sm:col-span-2">
                            <FormField
                                type="input"
                                inputType="number"
                                label="Valor Mínimo"
                                name="number_min"
                                value={formData.number_min}
                                onChange={(e) => handleChange('number_min', Number(e.target.value))}
                                error={errors.number_min}
                            />
                        </div>
                        <div className="col-span-6 sm:col-span-2">
                            <FormField
                                type="input"
                                inputType="number"
                                label="Valor Máximo"
                                name="number_max"
                                value={formData.number_max}
                                onChange={(e) => handleChange('number_max', Number(e.target.value))}
                                error={errors.number_max}
                            />
                        </div>
                        <div className="col-span-6 sm:col-span-2">
                            <FormField
                                type="input"
                                inputType="number"
                                label="Incremento"
                                name="number_step"
                                value={formData.number_step}
                                onChange={(e) => handleChange('number_step', Number(e.target.value))}
                                error={errors.number_step}
                                helperText="Valor de incremento/decremento"
                            />
                        </div>
                    </>
                );

            default:
                return null;
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {Object.keys(errors).length > 0 && (
                <ErrorMessage message="Por favor, corrige los errores en el formulario." />
            )}

            <div className="grid grid-cols-6 gap-6">
                {/* Campos básicos */}
                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Nombre del Componente"
                        name="name"
                        value={formData.name}
                        onChange={(e) => handleChange('name', e.target.value)}
                        error={errors.name}
                        helperText="Nombre único para identificar el componente"
                        required
                    />
                </div>

                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="select"
                        label="Tipo de Componente"
                        name="type"
                        value={formData.type}
                        onChange={(e) => handleChange('type', e.target.value)}
                        options={COMPONENT_TYPES}
                        error={errors.type}
                        required
                    />
                </div>

                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Etiqueta"
                        name="label"
                        value={formData.label}
                        onChange={(e) => handleChange('label', e.target.value)}
                        error={errors.label}
                        helperText="Texto que se mostrará como etiqueta del campo"
                        required
                    />
                </div>

                <div className="col-span-6 sm:col-span-3">
                    <FormField
                        type="input"
                        label="Placeholder"
                        name="placeholder"
                        value={formData.placeholder}
                        onChange={(e) => handleChange('placeholder', e.target.value)}
                        error={errors.placeholder}
                        helperText="Texto de ayuda que aparece dentro del campo"
                    />
                </div>

                {/* Configuración de layout */}
                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Grupo"
                        name="group_id"
                        value={formData.group_id}
                        onChange={(e) => handleChange('group_id', Number(e.target.value))}
                        error={errors.group_id}
                        required
                    />
                </div>

                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Orden"
                        name="order"
                        value={formData.order}
                        onChange={(e) => handleChange('order', Number(e.target.value))}
                        error={errors.order}
                        required
                    />
                </div>

                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="input"
                        inputType="number"
                        label="Objetivo"
                        name="target"
                        value={formData.target}
                        onChange={(e) => handleChange('target', Number(e.target.value))}
                        error={errors.target}
                        helperText="ID del objetivo del componente"
                    />
                </div>

                {/* Estados */}
                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="checkbox"
                        label="Deshabilitado"
                        name="is_disabled"
                        checked={formData.is_disabled}
                        onChange={(e) => handleChange('is_disabled', e.target.checked)}
                        helperText="Si el campo debe estar deshabilitado"
                    />
                </div>

                <div className="col-span-6 sm:col-span-2">
                    <FormField
                        type="checkbox"
                        label="Solo Lectura"
                        name="is_readonly"
                        checked={formData.is_readonly}
                        onChange={(e) => handleChange('is_readonly', e.target.checked)}
                        helperText="Si el campo debe ser solo de lectura"
                    />
                </div>

                {/* Campos específicos por tipo */}
                {renderTypeSpecificFields()}

                {/* Campos adicionales */}
                <div className="col-span-6">
                    <FormField
                        type="input"
                        label="Clases CSS"
                        name="css_classes"
                        value={formData.css_classes}
                        onChange={(e) => handleChange('css_classes', e.target.value)}
                        error={errors.css_classes}
                        helperText="Clases CSS adicionales para el componente"
                    />
                </div>

                <div className="col-span-6">
                    <FormField
                        type="textarea"
                        label="Texto de Ayuda"
                        name="help_text"
                        value={formData.help_text}
                        onChange={(e) => handleChange('help_text', e.target.value)}
                        error={errors.help_text}
                        helperText="Texto explicativo que se mostrará al usuario"
                        rows={2}
                    />
                </div>

                <div className="col-span-6">
                    <FormField
                        type="input"
                        label="Valor por Defecto"
                        name="default_value"
                        value={formData.default_value}
                        onChange={(e) => handleChange('default_value', e.target.value)}
                        error={errors.default_value}
                        helperText="Valor que tendrá el campo por defecto"
                    />
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
                            label: 'Guardar Componente',
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

export default ComponentForm;
