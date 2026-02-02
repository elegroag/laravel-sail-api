import { TipoFuncionario } from '@/constants/auth';
import { useRegisterValidation } from '@/pages/Auth/hooks/useRegisterValidation';
import type { FormAction, FormState, LoginProps, RegisterPayload, UserType } from '@/types/auth';
import { router } from '@inertiajs/react';
import type React from 'react';
import { useEffect, useMemo, useReducer, useRef, useState } from 'react';

const initialState: FormState = {
    selectedUserType: null,
    documentType: '',
    documentTypeUser: '',
    documentTypeRep: '',
    identification: '',
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    companyName: '',
    companyNit: '',
    address: '',
    city: '',
    societyType: '',
    companyCategory: '',
    userRole: '',
    position: '',
    repName: '',
    repIdentification: '',
    repEmail: '',
    repPhone: '',
    contributionRate: '',
    errors: {},
    isSubmitting: false,
    isSuccess: false,
};

function formReducer(state: FormState, action: FormAction): FormState {
    switch (action.type) {
        case 'SET_USER_TYPE':
            return { ...state, selectedUserType: action.payload };
        case 'SET_FIELD':
            return {
                ...state,
                [action.field]: action.value,
                errors: { ...state.errors, [action.field]: '' },
            };
        case 'SET_ERROR':
            return {
                ...state,
                errors: { ...state.errors, [action.field]: action.error },
            };
        case 'CLEAR_ERRORS':
            return { ...state, errors: {} };
        case 'SET_SUBMITTING':
            return { ...state, isSubmitting: action.payload };
        case 'RESET_FORM':
            return initialState;
        default:
            return state;
    }
}

const useRegisterController = ({ Coddoc, Tipsoc, Codciu, errors }: LoginProps) => {
    const [state, dispatch] = useReducer(formReducer, initialState);
    const [step, setStep] = useState(1);
    const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);

    const firstNameRef = useRef<HTMLInputElement>(null);
    const lastNameRef = useRef<HTMLInputElement>(null);
    const emailRef = useRef<HTMLInputElement>(null);
    const phoneRef = useRef<HTMLInputElement>(null);
    const identificationRef = useRef<HTMLInputElement>(null);
    const passwordRef = useRef<HTMLInputElement>(null);
    const confirmPasswordRef = useRef<HTMLInputElement>(null);
    const companyNameRef = useRef<HTMLInputElement>(null);
    const companyNitRef = useRef<HTMLInputElement>(null);
    const addressRef = useRef<HTMLInputElement>(null);

    const documentTypeOptions = useMemo(() => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label })), [Coddoc]);

    // Opciones de ciudades mapeadas desde Codciu
    const cityOptions = useMemo(() => Object.entries(Codciu || {}).map(([value, label]) => ({ value, label })), [Codciu]);

    // Opciones de tipos de sociedad desde Tipsoc
    const societyOptions = useMemo(() => Object.entries(Tipsoc || {}).map(([value, label]) => ({ value, label })), [Tipsoc]);

    // Opciones de categoría de empresa (Natural/Jurídica)
    const companyCategoryOptions = useMemo(
        () => [
            { value: 'N', label: 'NATURAL' },
            { value: 'J', label: 'JURIDICA' },
        ],
        [],
    );

    useEffect(() => {
        if (state.selectedUserType && firstNameRef.current) {
            firstNameRef.current.focus();
        }
    }, [state.selectedUserType]);

    // Hook de validación extraído para reducir lógica en el componente
    const { validateStep } = useRegisterValidation({
        state,
        step,
        dispatch,
        refs: {
            firstNameRef,
            lastNameRef,
            emailRef,
            identificationRef,
            passwordRef,
            confirmPasswordRef,
            companyNameRef,
            companyNitRef,
        },
    });

    useEffect(() => {
        if (!errors || Object.keys(errors).length === 0) {
            return;
        }

        dispatch({ type: 'CLEAR_ERRORS' });

        Object.entries(errors).forEach(([field, message]) => {
            dispatch({ type: 'SET_ERROR', field, error: message });
        });

        const detalleErrores = Object.entries(errors)
            .map(([field, message]) => `${field}: ${message}`)
            .join(' | ');

        setToast({
            message: `Se detectaron errores en el registro. ${detalleErrores}`,
            type: 'error',
        });
    }, [errors, dispatch]);

    const handleUserTypeSelect = (userType: UserType) => {
        dispatch({ type: 'SET_USER_TYPE', payload: userType });
        // Reiniciar tasa de contribución al cambiar entre tipos para evitar selecciones previas
        dispatch({ type: 'SET_FIELD', field: 'contributionRate', value: '' });
        setStep(1);
    };

    const handleBack = () => {
        if (state.selectedUserType === 'empresa' && step === 2) {
            setStep(1);
        } else {
            dispatch({ type: 'RESET_FORM' });
            setStep(1);
        }
    };

    // Navegación entre pasos usando validación
    const handleNextStep = () => {
        const isCompany = state.selectedUserType === 'empresa';
        const isWorker = state.selectedUserType === 'trabajador';
        const maxSteps = isCompany ? (state.userRole === 'delegado' ? 5 : 4) : isWorker ? 3 : 2;
        if (validateStep()) {
            setStep((prev) => Math.min(prev + 1, maxSteps));
        }
    };

    const handlePrevStep = () => {
        setStep((prev) => Math.max(prev - 1, 1));
    };

    type RegisterRouteName =
        | 'api.register'
        | 'api.register.empresa'
        | 'api.register.trabajador'
        | 'api.register.particular'
        | 'api.register.independiente'
        | 'api.register.pensionado'
        | 'api.register.facultativo'
        | 'api.register.domestico';

    const splitNombre = (fullName: string) => {
        const cleaned = (fullName || '').trim().replace(/\s+/g, ' ');
        if (!cleaned) {
            return { first: '', last: '' };
        }
        const parts = cleaned.split(' ');
        if (parts.length === 1) {
            return { first: parts[0], last: '' };
        }
        return {
            first: parts.slice(0, -1).join(' '),
            last: parts[parts.length - 1],
        };
    };

    const validatePhoneLength = (phone: string | number, fieldName: string): number => {
        const digits = String(phone).replace(/\D/g, '');
        if (digits.length < 6 || digits.length > 10) {
            throw new Error(`El campo ${fieldName} debe tener entre 6 y 10 dígitos.`);
        }
        return Number(digits);
    };

    const buildRegisterPayload = (): RegisterPayload => {
        const tipoValue = TipoFuncionario[state.selectedUserType as keyof typeof TipoFuncionario];

        const isCompany = state.selectedUserType === 'empresa';
        const isWorker = state.selectedUserType === 'trabajador';
        const isIndependent = state.selectedUserType === 'independiente';
        const isPensioner = state.selectedUserType === 'pensionado';

        // Mapeo de campos a las propiedades esperadas por el backend
        const payload: RegisterPayload = {
            selected_user_type: state.selectedUserType,
            tipo: tipoValue,
            // Sesión
            coddoc: state.documentTypeUser,
            documento: state.identification,
            password: state.password,
            // Empresa (si aplica)
            tipdoc: state.documentType || undefined,
            razsoc: state.companyName || undefined,
            nit: state.companyNit || undefined,
            tipsoc: state.societyType || undefined,
            tipper: state.companyCategory || undefined,
            // Personales
            nombre: '',
            email: '',
            telefono: 0,
            codciu: 0,
            first_name: '',
            last_name: '',
            rep_nombre: '',
            rep_documento: '',
            rep_email: '',
            rep_telefono: 0,
            rep_coddoc: '',
            cargo: '',
        };

        // Resolver datos base del usuario según tipo (empresa vs persona)
        if (isCompany) {
            // Para empresa: si es delegado, los datos del usuario vienen en firstName/lastName/email/phone/city
            // Si es representante, los datos vienen en repName/repEmail/repPhone y city
            if (state.userRole === 'delegado') {
                payload.nombre = `${state.firstName} ${state.lastName}`.trim();
                payload.email = state.email;
                payload.telefono = validatePhoneLength(state.phone, 'teléfono');
                payload.codciu = Number(state.city);

                payload.first_name = state.firstName;
                payload.last_name = state.lastName;
            } else {
                payload.nombre = (state.repName || '').trim();
                payload.email = state.repEmail;
                payload.telefono = validatePhoneLength(state.repPhone, 'teléfono');
                payload.codciu = Number(state.city);

                const { first, last } = splitNombre(state.repName);
                payload.first_name = first;
                payload.last_name = last;
            }
        } else {
            // Para particulares/trabajador/independiente/etc.
            payload.nombre = `${state.firstName} ${state.lastName}`.trim();
            payload.email = state.email;
            payload.telefono = validatePhoneLength(state.phone, 'teléfono');
            payload.codciu = Number(state.city);

            payload.first_name = state.firstName;
            payload.last_name = state.lastName;
        }

        // Delegado/Representante (empresa)
        if (isCompany) {
            payload.is_delegado = state.userRole === 'delegado';
            payload.cargo = state.userRole === 'delegado' ? state.position : undefined;
            // En ambos casos (delegado o representante) debemos enviar los datos del representante.
            // - Si el responsable es delegado: rep_* corresponde al representante legal.
            // - Si el responsable es representante: rep_* corresponde al mismo representante (responsable de la cuenta).
            payload.rep_nombre = state.repName || undefined;
            payload.rep_documento = state.repIdentification || undefined;
            payload.rep_email = state.repEmail || undefined;
            payload.rep_telefono = validatePhoneLength(state.repPhone, 'teléfono del representante') || undefined;
            payload.rep_coddoc = state.documentTypeRep || undefined;
        }

        // Trabajador: también enviar cargo si fue diligenciado
        if (isWorker && state.position) {
            payload.cargo = state.position;
        }

        // Independiente / Pensionado: tasa de contribución
        if ((isIndependent || isPensioner) && state.contributionRate) {
            // Nombre del campo genérico para tasa de contribución
            payload.contribution_rate = state.contributionRate;
        }

        // Tipper (tipo persona) para independientes/pensionados si no se envía desde empresa
        if (!isCompany && !payload.tipper) {
            payload.tipper = 'N'; // Natural por defecto para personas
        }

        return payload;
    };

    const resolveRegisterRouteName = (tipo: RegisterPayload['tipo']): RegisterRouteName => {
        switch (tipo) {
            case 'E':
                return 'api.register.empresa';
            case 'T':
                return 'api.register.trabajador';
            case 'P':
                return 'api.register.particular';
            case 'I':
                return 'api.register.independiente';
            case 'O':
                return 'api.register.pensionado';
            case 'F':
                return 'api.register.facultativo';
            case 'S':
                return 'api.register.domestico';
            default:
                return 'api.register';
        }
    };

    const postRegister = async (routeName: RegisterRouteName, payload: RegisterPayload) => {
        const response = await fetch(route(routeName), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(payload),
        });

        const responseJson: unknown = await response.json();

        return {
            response,
            responseJson,
        };
    };

    type RegisterApiSuccessData = {
        tipo: string;
        coddoc: string;
        documento: string | number;
    };

    type RegisterApiResponse = {
        success?: boolean;
        message?: unknown;
        data?: RegisterApiSuccessData;
        errors?: unknown;
    };

    const isRegisterApiResponse = (value: unknown): value is RegisterApiResponse => {
        return typeof value === 'object' && value !== null;
    };

    const handleRegisterSuccess = (responseJson: RegisterApiResponse) => {
        setToast({ message: '¡Registro exitoso! Serás redirigido al login.', type: 'success' });
        dispatch({ type: 'RESET_FORM' });
        setStep(1);
        setTimeout(() => {
            router.visit(
                route('verify.show', {
                    tipo: responseJson.data?.tipo,
                    coddoc: responseJson.data?.coddoc,
                    documento: responseJson.data?.documento,
                    option_request: 'register',
                }),
            );
        }, 1000);
    };

    const handleRegisterFailure = (responseJson: RegisterApiResponse) => {
        console.error('Error al registrar:', responseJson);
        setToast({
            message: typeof responseJson?.message === 'string' ? responseJson.message : 'No fue posible completar el registro.',
            type: 'error',
        });
    };

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!validateStep()) {
            return;
        }
        dispatch({ type: 'SET_SUBMITTING', payload: true });
        try {
            const payload = buildRegisterPayload();

            console.debug('Payload de registro (previo a envío):', payload);

            const registerRouteName = resolveRegisterRouteName(payload.tipo);
            const { response, responseJson } = await postRegister(registerRouteName, payload);

            const safeJson: RegisterApiResponse = isRegisterApiResponse(responseJson) ? responseJson : {};

            if (response.ok && safeJson?.success) {
                handleRegisterSuccess(safeJson);
                return;
            }

            handleRegisterFailure(safeJson);
        } catch (error) {
            const message = error instanceof Error ? error.message : 'No fue posible completar el registro. Intenta nuevamente.';
            setToast({ message, type: 'error' });
        } finally {
            dispatch({ type: 'SET_SUBMITTING', payload: false });
        }
    };

    return {
        dispatch,
        state,
        toast,
        setToast,
        step,
        validateStep,
        domRef: {
            firstNameRef,
            lastNameRef,
            emailRef,
            phoneRef,
            identificationRef,
            passwordRef,
            confirmPasswordRef,
            companyNameRef,
            companyNitRef,
            addressRef,
        },
        events: {
            handleBack,
            handleNextStep,
            handlePrevStep,
            handleRegister,
            handleUserTypeSelect,
        },
        collections: {
            documentTypeOptions,
            cityOptions,
            societyOptions,
            companyCategoryOptions,
        },
    };
};

export default useRegisterController;
