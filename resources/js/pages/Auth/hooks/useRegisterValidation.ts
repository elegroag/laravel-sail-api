import type { FormAction, FormState } from '@/types/auth';
import { MutableRefObject, RefObject, useCallback } from 'react';

// Tipos del formulario reutilizados aquí para SOLID (separación de responsabilidades)
// Tipos importados desde '@/types/register'

// Tipo flexible para refs de inputs (acepta RefObject o MutableRefObject con null)
type InputRef = RefObject<HTMLInputElement | null> | MutableRefObject<HTMLInputElement | null>;

interface Refs {
    firstNameRef: InputRef;
    lastNameRef: InputRef;
    emailRef: InputRef;
    identificationRef: InputRef;
    passwordRef: InputRef;
    confirmPasswordRef: InputRef;
    companyNameRef: InputRef;
    companyNitRef: InputRef;
}

interface UseRegisterValidationParams {
    state: FormState;
    step: number;
    refs: Refs;
    dispatch: (action: FormAction) => void;
}

// Validación para flujo de empresa
function validateCompanyStep(step: number, state: FormState, refs: Refs, dispatch: (action: FormAction) => void): boolean {
    const { firstNameRef, lastNameRef, emailRef, identificationRef, passwordRef, confirmPasswordRef, companyNameRef, companyNitRef } = refs;
    let isValid = true;

    // Paso 1: datos de empresa
    if (step === 1) {
        if (!state.companyName.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'companyName', error: 'El nombre de la empresa es requerido' });
            companyNameRef.current?.focus();
            isValid = false;
        }
        if (!state.companyNit.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'companyNit', error: 'El NIT de la empresa es requerido' });
            if (isValid) companyNitRef.current?.focus();
            isValid = false;
        }
        if (!state.documentType) {
            dispatch({ type: 'SET_ERROR', field: 'documentType', error: 'El tipo de documento de la empresa es requerido' });
            isValid = false;
        }
        if (!state.societyType) {
            dispatch({ type: 'SET_ERROR', field: 'societyType', error: 'El tipo de sociedad es requerido' });
            isValid = false;
        }
        if (!state.companyCategory) {
            dispatch({ type: 'SET_ERROR', field: 'companyCategory', error: 'La categoría de empresa es requerida' });
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso 2: selección de responsable (solo para jurídica)
    if (step === 2 && state.companyCategory === 'J') {
        if (!state.userRole) {
            dispatch({ type: 'SET_ERROR', field: 'userRole', error: 'Debes indicar si eres representante o delegado' });
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso 3: datos del representante (o paso 2 para natural)
    if (step === 3 || (state.companyCategory === 'N' && step === 2)) {
        if (!state.repName?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repName', error: 'El nombre del representante es requerido' });
            isValid = false;
        }
        if (!state.repIdentification?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repIdentification', error: 'La identificación del representante es requerida' });
            isValid = false;
        }
        if (!state.repEmail?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repEmail', error: 'El email del representante es requerido' });
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.repEmail)) {
            dispatch({ type: 'SET_ERROR', field: 'repEmail', error: 'Email del representante inválido' });
            isValid = false;
        }
        if (!state.repPhone?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repPhone', error: 'El teléfono del representante es requerido' });
            isValid = false;
        }
        console.log('state', state, 'paso', step, 'isValid', isValid);
        return isValid;
    }

    // Paso 4: datos del delegado (solo si userRole === 'delegado')
    if (step === 4 && state.userRole === 'delegado') {
        if (!state.position.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'position', error: 'El cargo u ocupación es requerido' });
            isValid = false;
        }
        if (!state.firstName.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'firstName', error: 'El nombre del delegado es requerido' });
            firstNameRef.current?.focus();
            isValid = false;
        }
        if (!state.lastName.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'lastName', error: 'El apellido del delegado es requerido' });
            if (isValid) lastNameRef.current?.focus();
            isValid = false;
        }
        if (!state.email.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'email', error: 'El email del delegado es requerido' });
            if (isValid) emailRef.current?.focus();
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
            dispatch({ type: 'SET_ERROR', field: 'email', error: 'Email del delegado inválido' });
            if (isValid) emailRef.current?.focus();
            isValid = false;
        }
        if (!state.city) {
            dispatch({ type: 'SET_ERROR', field: 'city', error: 'La ciudad es requerida' });
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso 5: datos del representante (solo cuando hay delegado)
    if (step === 5 && state.userRole === 'delegado') {
        if (!state.repName?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repName', error: 'El nombre del representante es requerido' });
            isValid = false;
        }
        if (!state.repIdentification?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repIdentification', error: 'La identificación del representante es requerida' });
            isValid = false;
        }
        if (!state.repEmail?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repEmail', error: 'El email del representante es requerido' });
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.repEmail)) {
            dispatch({ type: 'SET_ERROR', field: 'repEmail', error: 'Email del representante inválido' });
            isValid = false;
        }
        if (!state.repPhone?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'repPhone', error: 'El teléfono del representante es requerido' });
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso sesión: paso 4 (sin delegado) o paso 6 (con delegado)
    const isSessionStep = (state.userRole === 'delegado' && step === 6) || (state.userRole !== 'delegado' && step === 4);
    if (isSessionStep) {
        if (!state.documentType) {
            dispatch({ type: 'SET_ERROR', field: 'documentType', error: 'El tipo de documento es requerido' });
            isValid = false;
        }
        if (!state.identification.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'identification', error: 'La identificación es requerida' });
            if (isValid) identificationRef.current?.focus();
            isValid = false;
        }
        if (!state.password.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'password', error: 'La contraseña es requerida' });
            if (isValid) passwordRef.current?.focus();
            isValid = false;
        } else {
            const strongPwd = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/;
            if (!strongPwd.test(state.password)) {
                dispatch({
                    type: 'SET_ERROR',
                    field: 'password',
                    error: 'La contraseña debe tener mínimo 10 caracteres, incluir 1 mayúscula, 1 número y 1 símbolo',
                });
                if (isValid) passwordRef.current?.focus();
                isValid = false;
            }
        }
        if (state.password !== state.confirmPassword) {
            dispatch({ type: 'SET_ERROR', field: 'confirmPassword', error: 'Las contraseñas no coinciden' });
            if (isValid) confirmPasswordRef.current?.focus();
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    return isValid;
}

// Validación para flujo de trabajador
function validateWorkerStep(step: number, state: FormState, refs: Refs, dispatch: (action: FormAction) => void): boolean {
    const { firstNameRef, lastNameRef, emailRef, identificationRef, passwordRef, confirmPasswordRef, companyNameRef, companyNitRef } = refs;
    let isValid = true;

    // Paso 1: datos personales
    if (step === 1) {
        if (!state.firstName.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'firstName', error: 'El nombre es requerido' });
            firstNameRef.current?.focus();
            isValid = false;
        }
        if (!state.lastName.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'lastName', error: 'El apellido es requerido' });
            if (isValid) lastNameRef.current?.focus();
            isValid = false;
        }
        if (!state.email.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'email', error: 'El email es requerido' });
            if (isValid) emailRef.current?.focus();
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
            dispatch({ type: 'SET_ERROR', field: 'email', error: 'Email inválido' });
            if (isValid) emailRef.current?.focus();
            isValid = false;
        }
        if (!state.city) {
            dispatch({ type: 'SET_ERROR', field: 'city', error: 'La ciudad es requerida' });
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso 2: datos de empresa (nit, razón social, cargo)
    if (step === 2) {
        const reasons: string[] = [];
        if (!state.companyNit?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'companyNit', error: 'El NIT de la empresa es requerido' });
            reasons.push('companyNit');
            companyNitRef.current?.focus();
            isValid = false;
        }
        if (!state.companyName?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'companyName', error: 'La razón social es requerida' });
            reasons.push('companyName');
            if (isValid) companyNameRef.current?.focus();
            isValid = false;
        }
        if (!state.position?.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'position', error: 'El cargo es requerido' });
            reasons.push('position');
            isValid = false;
        }
        if (reasons.length) {
            console.debug('[validación] Paso 2 (Trabajador) inválido ->', reasons);
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    // Paso 3: datos de sesión
    if (step === 3) {
        if (!state.documentTypeUser) {
            dispatch({ type: 'SET_ERROR', field: 'documentTypeUser', error: 'El tipo de documento es requerido' });
            isValid = false;
        }
        if (!state.identification.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'identification', error: 'La identificación es requerida' });
            if (isValid) identificationRef.current?.focus();
            isValid = false;
        }
        if (!state.password.trim()) {
            dispatch({ type: 'SET_ERROR', field: 'password', error: 'La contraseña es requerida' });
            if (isValid) passwordRef.current?.focus();
            isValid = false;
        } else {
            const strongPwd = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/;
            if (!strongPwd.test(state.password)) {
                dispatch({
                    type: 'SET_ERROR',
                    field: 'password',
                    error: 'La contraseña debe tener mínimo 10 caracteres, incluir 1 mayúscula, 1 número y 1 símbolo',
                });
                if (isValid) passwordRef.current?.focus();
                isValid = false;
            }
        }
        if (state.password !== state.confirmPassword) {
            dispatch({ type: 'SET_ERROR', field: 'confirmPassword', error: 'Las contraseñas no coinciden' });
            if (isValid) confirmPasswordRef.current?.focus();
            isValid = false;
        }
        console.log('state', state, 'paso', step);
        return isValid;
    }

    return isValid;
}

// Hook de validación: encapsula la lógica por paso y dispara errores en el estado
// Comentarios breves en español para mantener claridad y bajo acoplamiento
export function useRegisterValidation({ state, step, refs, dispatch }: UseRegisterValidationParams) {
    const validateStep = useCallback((): boolean => {
        dispatch({ type: 'CLEAR_ERRORS' });

        const isCompany = state.selectedUserType === 'empresa';
        const isWorker = state.selectedUserType === 'trabajador';
        const isIndependent = state.selectedUserType === 'independiente';
        const isPensioner = state.selectedUserType === 'pensionado';

        // Delegar a función específica de empresa
        if (isCompany) {
            return validateCompanyStep(step, state, refs, dispatch);
        }

        // Delegar a función específica de trabajador
        if (isWorker) {
            return validateWorkerStep(step, state, refs, dispatch);
        }

        // Validación para independiente/pensionado
        const { firstNameRef, lastNameRef, emailRef, identificationRef, passwordRef, confirmPasswordRef } = refs;
        let isValid = true;

        // Paso 1: datos personales
        if (step === 1) {
            if (!state.firstName.trim()) {
                dispatch({ type: 'SET_ERROR', field: 'firstName', error: 'El nombre es requerido' });
                firstNameRef.current?.focus();
                isValid = false;
            }
            if (!state.lastName.trim()) {
                dispatch({ type: 'SET_ERROR', field: 'lastName', error: 'El apellido es requerido' });
                if (isValid) lastNameRef.current?.focus();
                isValid = false;
            }
            if (!state.email.trim()) {
                dispatch({ type: 'SET_ERROR', field: 'email', error: 'El email es requerido' });
                if (isValid) emailRef.current?.focus();
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
                dispatch({ type: 'SET_ERROR', field: 'email', error: 'Email inválido' });
                if (isValid) emailRef.current?.focus();
                isValid = false;
            }
            if (!state.city) {
                dispatch({ type: 'SET_ERROR', field: 'city', error: 'La ciudad es requerida' });
                isValid = false;
            }
            if (isIndependent || isPensioner) {
                if (!state.contributionRate) {
                    dispatch({ type: 'SET_ERROR', field: 'contributionRate', error: 'Selecciona la tasa de contribución' });
                    isValid = false;
                }
            }
            console.log('state', state, 'paso', step);
            return isValid;
        }

        // Paso 2: datos de sesión
        if (step === 2) {
            if (!state.documentTypeUser) {
                dispatch({ type: 'SET_ERROR', field: 'documentTypeUser', error: 'El tipo de documento es requerido' });
                isValid = false;
            }
            if (!state.identification.trim()) {
                dispatch({ type: 'SET_ERROR', field: 'identification', error: 'La identificación es requerida' });
                if (isValid) identificationRef.current?.focus();
                isValid = false;
            }
            if (!state.password.trim()) {
                dispatch({ type: 'SET_ERROR', field: 'password', error: 'La contraseña es requerida' });
                if (isValid) passwordRef.current?.focus();
                isValid = false;
            } else {
                const strongPwd = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/;
                if (!strongPwd.test(state.password)) {
                    dispatch({
                        type: 'SET_ERROR',
                        field: 'password',
                        error: 'La contraseña debe tener mínimo 10 caracteres, incluir 1 mayúscula, 1 número y 1 símbolo',
                    });
                    if (isValid) passwordRef.current?.focus();
                    isValid = false;
                }
            }
            if (state.password !== state.confirmPassword) {
                dispatch({ type: 'SET_ERROR', field: 'confirmPassword', error: 'Las contraseñas no coinciden' });
                if (isValid) confirmPasswordRef.current?.focus();
                isValid = false;
            }
            console.log('state', state, 'paso', step);
            return isValid;
        }

        return isValid;
    }, [dispatch, refs, state, step]);

    return { validateStep };
}
