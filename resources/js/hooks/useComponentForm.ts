import { useState, useCallback } from 'react';
import type {
    ComponentData,
    DataSourceOption,
    EventConfig,
    UseComponentFormReturn
} from '@/types/componentes';

interface UseComponentFormProps {
    initialData?: Partial<ComponentData>;
    onSubmit: (data: ComponentData) => Promise<void>;
}

export const useComponentForm = ({ initialData = {}, onSubmit }: UseComponentFormProps): UseComponentFormReturn => {
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

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(false);

    const updateField = useCallback((field: keyof ComponentData, value: unknown) => {
        setFormData(prev => ({ ...prev, [field]: value }));
        // Clear error when user starts typing
        if (errors[field]) {
            setErrors(prev => ({ ...prev, [field]: '' }));
        }
    }, [errors]);

    const updateDataSource = useCallback((dataSource: DataSourceOption[]) => {
        setFormData(prev => ({ ...prev, data_source: dataSource }));
    }, []);

    const resetForm = useCallback(() => {
        setFormData({
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
        setErrors({});
    }, [initialData]);

    const handleSubmit = useCallback(async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            await onSubmit(formData);
        } catch (error: unknown) {
            if (error && typeof error === 'object' && 'response' in error) {
                const axiosError = error as { response?: { data?: { errors?: Record<string, string> } } };
                if (axiosError.response?.data?.errors) {
                    setErrors(axiosError.response.data.errors);
                }
            } else {
                console.error('Error submitting form:', error);
            }
        } finally {
            setLoading(false);
        }
    }, [formData, onSubmit]);

    const isValid = useCallback(() => {
        return formData.name.trim() !== '' &&
            formData.label.trim() !== '' &&
            formData.type.length > 0;
    }, [formData]);

    return {
        formData,
        errors,
        loading,
        updateField,
        updateDataSource,
        resetForm,
        handleSubmit,
        isValid,
        setErrors
    };
};
