import type { DeliveryMethod, FormActionRecovery, FormBasicRecovery, UserType } from '@/types/auth';
import type React from 'react';
import { useEffect, useMemo, useReducer, useRef, useState } from 'react';
import { router, useForm } from '@inertiajs/react'
import { TipoFuncionario } from '@/constants/auth';

const initialFormState: FormBasicRecovery = {
    documentType: '',
    identification: '',
    email: '',
    errors: {},
    isSubmitting: false,
    isSuccess: false,
    delivery_method: 'email',
    whatsapp: '',
};

const formReducer = (state: FormBasicRecovery, action: FormActionRecovery | { type: 'CLEAR_ERROR'; field: keyof FormBasicRecovery['errors'] }): FormBasicRecovery => {
    switch (action.type) {
        case 'SET_FIELD':
            return { ...state, [action.field]: action.value };
        case 'SET_ERROR':
            return { ...state, errors: { ...state.errors, [action.field]: action.error } };
        case 'CLEAR_ERROR': {
            const { [action.field]: _omit, ...rest } = state.errors
            return { ...state, errors: rest }
        }
        case 'CLEAR_ERRORS':
            return { ...state, errors: {} };
        case 'SET_SUBMITTING':
            return { ...state, isSubmitting: action.payload };
        case 'SET_SUCCESS':
            return { ...state, isSuccess: action.payload };
        case 'RESET_FORM':
            return initialFormState;
        default:
            return state;
    }
}

interface ResetPasswordProps {
    Coddoc: Record<string, string>;
}

const useRecoveryController = ({Coddoc}: ResetPasswordProps) => {

    const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null);
    const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null)

     const documentTypeOptions = useMemo(
        () => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label })),
        [Coddoc]
    )

    const [formState, dispatch] = useReducer(formReducer, initialFormState);

    const documentTypeRef = useRef<HTMLButtonElement>(null);
    const identificationRef = useRef<HTMLInputElement>(null);
    const emailRef = useRef<HTMLInputElement>(null);
    const whatsappRef = useRef<HTMLInputElement>(null);

    const tipoValue: string = selectedUserType
        ? (TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario] ?? '')
        : '';

    const { data, setData } = useForm({
        documento: formState.identification ?? '',
        coddoc: formState.documentType ?? '',
        tipo: tipoValue,
        email: formState.email ?? '',
        whatsapp: formState.whatsapp ?? '',
        delivery_method: (formState.delivery_method || 'email') as DeliveryMethod,
    });

    // Mantener sincronizado delivery_method si cambia desde el estado del formulario
    useEffect(() => {
        if (formState.delivery_method && formState.delivery_method !== data.delivery_method) {
            setData('delivery_method', formState.delivery_method as DeliveryMethod)
        }
    }, [formState.delivery_method, data.delivery_method, setData])

    // Mantener sincronizado 'tipo' según selectedUserType
    useEffect(() => {
        const nextTipo = selectedUserType ? (TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario] ?? '') : ''
        if (data.tipo !== nextTipo) {
            setData('tipo', nextTipo)
        }
    }, [selectedUserType, data.tipo, setData])

    const handleUserTypeSelect = (userType: UserType) => {
        setSelectedUserType(userType);
        dispatch({ type: 'RESET_FORM' });
    };

    const handleBack = () => {
        setSelectedUserType(null);
        dispatch({ type: 'RESET_FORM' });
    };

    const validateField = (field: keyof FormBasicRecovery['errors'], value: string) => {
        switch (field) {
            case 'documentType':
                if (!value) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Selecciona un tipo de documento' });
                    return false;
                }
                break;
            case 'identification':
                if (!value) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Ingresa tu número de identificación' });
                    return false;
                }
                if (value.length < 6) {
                    dispatch({ type: 'SET_ERROR', field, error: 'El número de identificación debe tener al menos 6 caracteres' });
                    return false;
                }
                break;
            case 'email': {
                // Solo valida email cuando el canal elegido es email
                if (data.delivery_method !== 'email') break;
                if (!value) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Ingresa tu correo electrónico' });
                    return false;
                }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Ingresa un correo electrónico válido' });
                    return false;
                }
                break;
            }
            case 'whatsapp': {
                // Solo valida whatsapp cuando el canal elegido es whatsapp
                if (data.delivery_method !== 'whatsapp') break;
                if (!value) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Ingresa tu WhatsApp' });
                    return false;
                }
                if (!/^\+?\d{10,15}$/.test(value)) {
                    dispatch({ type: 'SET_ERROR', field, error: 'Ingresa un número de WhatsApp válido (10-15 dígitos)' });
                    return false;
                }
                break;
            }
        }
        dispatch({ type: 'CLEAR_ERROR', field });
        return true;
    };

    const handleFieldChange = (field: keyof FormBasicRecovery, value: string) => {
        dispatch({ type: 'SET_FIELD', field, value });
        // Sincroniza con useForm para el envío
        switch (field) {
            case 'documentType':
                setData('coddoc', value)
                break
            case 'identification':
                setData('documento', value)
                break
            case 'email':
                setData('email', value)
                break
            case 'whatsapp':
                setData('whatsapp', value)
                break
            case 'delivery_method':
                setData('delivery_method', value as DeliveryMethod)
                break
        }
        if (formState.errors[field as keyof FormBasicRecovery['errors']]) {
            validateField(field as keyof FormBasicRecovery['errors'], value);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const isDocumentTypeValid = validateField('documentType', data.coddoc);
        const isIdentificationValid = validateField('identification', data.documento);
        const isEmailValid = validateField('email', data.email ?? '');
        const isWhatsappValid = validateField('whatsapp', data.whatsapp ?? '');
       
        const requiresEmail = data.delivery_method === 'email';
        const requiresWhatsapp = data.delivery_method === 'whatsapp';

        const hasErrors = !isDocumentTypeValid || !isIdentificationValid || (requiresEmail && !isEmailValid) || (requiresWhatsapp && !isWhatsappValid)
        if (hasErrors) {
            if (!isDocumentTypeValid) documentTypeRef.current?.focus();
            else if (!isIdentificationValid) identificationRef.current?.focus();
            else if (requiresEmail && !isEmailValid) emailRef.current?.focus();
            else if (requiresWhatsapp && !isWhatsappValid) whatsappRef.current?.focus();
            return;
        }

        dispatch({ type: 'SET_SUBMITTING', payload: true });

        try {
            const response = await fetch(route('api.recovery_send'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            const responseJson = await response.json();

            if (response.ok && responseJson?.success) {
                setToast({ message: '¡Correo de recuperación enviado exitosamente! Serás redirigido al login.', type: 'success' })
                dispatch({ type: 'RESET_FORM' })
                
                setTimeout(() => {
                    router.visit(route('verify.show', {
                        tipo: responseJson.data.tipo,
                        coddoc: responseJson.data.coddoc,
                        documento: responseJson.data.documento,
                    }));
                }, 1500);
            } else {
                console.error('Error al enviar el correo de recuperación:', responseJson)
                setToast({ message: typeof responseJson?.message === 'string' ? responseJson.message : 'No fue posible enviar el correo de recuperación.', type: 'error' })
            }
        } catch (error) {
            setToast({ message: 'No fue posible completar el envío. Intenta nuevamente.'+ error, type: 'error' })
        } finally {
            dispatch({ type: 'SET_SUBMITTING', payload: false });
        }
    };

    return {
        events: {
            handleBack,
            handleFieldChange,
            handleSubmit,
            handleUserTypeSelect,
        },
        formState,
        selectedUserType,
        toast,
        setToast,
        documentTypeOptions,
        domRef: {
            documentTypeRef,
            identificationRef,
            emailRef,
            whatsappRef
        },
    };
};

export default useRecoveryController;
