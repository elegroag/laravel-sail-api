import React, { useState } from 'react';
import Button from '../atoms/Button';
import InputField from '../atoms/InputField';

interface DataSourceOption {
    value: string;
    label: string;
}

interface DataSourceEditorProps {
    options: DataSourceOption[];
    onChange: (options: DataSourceOption[]) => void;
    error?: string;
}

const DataSourceEditor: React.FC<DataSourceEditorProps> = ({ options, onChange, error }) => {
    const [newValue, setNewValue] = useState('');
    const [newLabel, setNewLabel] = useState('');

    const addOption = () => {
        if (newValue.trim() && newLabel.trim()) {
            const updatedOptions = [...options, { value: newValue.trim(), label: newLabel.trim() }];
            onChange(updatedOptions);
            setNewValue('');
            setNewLabel('');
        }
    };

    const removeOption = (index: number) => {
        const updatedOptions = options.filter((_, i) => i !== index);
        onChange(updatedOptions);
    };

    const updateOption = (index: number, field: 'value' | 'label', value: string) => {
        const updatedOptions = options.map((option, i) =>
            i === index ? { ...option, [field]: value } : option
        );
        onChange(updatedOptions);
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h4 className="text-sm font-medium text-gray-900">Opciones del Select</h4>
                <span className="text-xs text-gray-500">{options.length} opciones</span>
            </div>

            {/* Lista de opciones existentes */}
            <div className="space-y-2 max-h-48 overflow-y-auto">
                {options.map((option, index) => (
                    <div key={index} className="flex gap-2 items-center p-2 bg-gray-50 rounded">
                        <InputField
                            type="text"
                            value={option.value}
                            onChange={(e) => updateOption(index, 'value', e.target.value)}
                            placeholder="Valor"
                            className="flex-1"
                        />
                        <InputField
                            type="text"
                            value={option.label}
                            onChange={(e) => updateOption(index, 'label', e.target.value)}
                            placeholder="Etiqueta"
                            className="flex-1"
                        />
                        <Button
                            type="button"
                            variant="danger"
                            size="sm"
                            onClick={() => removeOption(index)}
                        >
                            ✕
                        </Button>
                    </div>
                ))}
            </div>

            {/* Agregar nueva opción */}
            <div className="flex gap-2 items-end p-3 border border-gray-200 rounded">
                <div className="flex-1">
                    <InputField
                        label="Valor"
                        type="text"
                        value={newValue}
                        onChange={(e) => setNewValue(e.target.value)}
                        placeholder="ej: option1"
                    />
                </div>
                <div className="flex-1">
                    <InputField
                        label="Etiqueta"
                        type="text"
                        value={newLabel}
                        onChange={(e) => setNewLabel(e.target.value)}
                        placeholder="ej: Opción 1"
                    />
                </div>
                <Button
                    type="button"
                    variant="primary"
                    size="sm"
                    onClick={addOption}
                    disabled={!newValue.trim() || !newLabel.trim()}
                >
                    +
                </Button>
            </div>

            {error && (
                <p className="text-sm text-red-600">{error}</p>
            )}
        </div>
    );
};

export default DataSourceEditor;
