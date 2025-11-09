import { useState, useCallback } from 'react';
import type {
    ValidationData,
    ValidationRule,
    ErrorMessage,
    UseValidationFormReturn
} from '@/types/componentes';

interface UseValidationFormProps {
    initialData?: Partial<ValidationData>;
    onSubmit: (data: ValidationData) => Promise<void>;
}

export const useValidationForm = ({ initialData = {}, onSubmit }: UseValidationFormProps): UseValidationFormReturn => {
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

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(false);

    const updateField = useCallback((field: keyof ValidationData, value: unknown) => {
        setFormData(prev => ({ ...prev, [field]: value }));
        // Clear error when user starts typing
        if (errors[field]) {
            setErrors(prev => ({ ...prev, [field]: '' }));
        }
    }, [errors]);

    const updateCustomRules = useCallback((rules: ValidationRule) => {
        setFormData(prev => ({ ...prev, custom_rules: rules }));
    }, []);

    const updateErrorMessages = useCallback((messages: Record<string, string>) => {
        setFormData(prev => ({ ...prev, error_messages: messages }));
    }, []);

    const addCustomRule = useCallback((key: string, value: unknown) => {
        setFormData(prev => ({
            ...prev,
            custom_rules: { ...prev.custom_rules, [key]: value }
        }));
    }, []);

    const removeCustomRule = useCallback((key: string) => {
        setFormData(prev => {
            const newRules = { ...prev.custom_rules };
            delete newRules[key];
            return { ...prev, custom_rules: newRules };
        });
    }, []);

    const addErrorMessage = useCallback((key: string, value: string) => {
        setFormData(prev => ({
            ...prev,
            error_messages: { ...prev.error_messages, [key]: value }
        }));
    }, []);

    const removeErrorMessage = useCallback((key: string) => {
        setFormData(prev => {
            const newMessages = { ...prev.error_messages };
            delete newMessages[key];
            return { ...prev, error_messages: newMessages };
        });
    }, []);

    const resetForm = useCallback(() => {
        setFormData({
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
        return formData.componente_id > 0 && formData.field_size > 0;
    }, [formData]);

    return {
        formData,
        errors,
        loading,
        updateField,
        updateCustomRules,
        updateErrorMessages,
        addCustomRule,
        removeCustomRule,
        addErrorMessage,
        removeErrorMessage,
        resetForm,
        handleSubmit,
        isValid,
        setErrors
    };
};
