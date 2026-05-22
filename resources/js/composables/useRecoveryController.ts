import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { TipoFuncionario } from '@/constants/auth'
import type { DeliveryMethod, UserType } from '@/types/auth'

interface FormBasicRecovery {
    documentType: string
    identification: string
    email: string
    errors: Record<string, string>
    isSubmitting: boolean
    isSuccess: boolean
    delivery_method: 'email' | 'whatsapp'
    whatsapp: string
}

type FormActionRecovery =
    | { type: 'SET_FIELD'; field: keyof FormBasicRecovery; value: string }
    | { type: 'SET_ERROR'; field: string; error: string }
    | { type: 'CLEAR_ERRORS' }
    | { type: 'SET_SUBMITTING'; payload: boolean }
    | { type: 'RESET_FORM' }
    | { type: 'SET_SUCCESS'; payload: boolean }
    | { type: 'CLEAR_ERROR'; field: keyof FormBasicRecovery['errors'] }

const initialFormState: FormBasicRecovery = {
    documentType: '',
    identification: '',
    email: '',
    errors: {},
    isSubmitting: false,
    isSuccess: false,
    delivery_method: 'email',
    whatsapp: ''
}

function formReducer(state: FormBasicRecovery, action: FormActionRecovery): FormBasicRecovery {
    switch (action.type) {
        case 'SET_FIELD':
            return { ...state, [action.field]: action.value }
        case 'SET_ERROR':
            return { ...state, errors: { ...state.errors, [action.field]: action.error } }
        case 'CLEAR_ERROR': {
            const { [action.field]: _omit, ...rest } = state.errors
            return { ...state, errors: rest }
        }
        case 'CLEAR_ERRORS':
            return { ...state, errors: {} }
        case 'SET_SUBMITTING':
            return { ...state, isSubmitting: action.payload }
        case 'SET_SUCCESS':
            return { ...state, isSuccess: action.payload }
        case 'RESET_FORM':
            return initialFormState
        default:
            return state
    }
}

export function useRecoveryController(coddoc: Record<string, string>) {
    const selectedUserType = computed<UserType | null>(() => null)
    const userTypeRef = computed<UserType | null>(() => null)

    const formState = reactive<FormBasicRecovery>({ ...initialFormState })
    const formRef = reactive({
        documento: '',
        coddoc: '',
        tipo: '',
        email: '',
        whatsapp: '',
        delivery_method: 'email' as DeliveryMethod
    })

    const documentTypeOptions = computed(() =>
        Object.entries(coddoc || {}).map(([value, label]) => ({ value, label }))
    )

    const handleUserTypeSelect = (userType: UserType) => {
        formState.documentType = ''
        formState.identification = ''
        formState.email = ''
        formState.whatsapp = ''
        formState.errors = {}
    }

    const handleBack = () => {
        formState.documentType = ''
        formState.identification = ''
        formState.email = ''
        formState.whatsapp = ''
        formState.errors = {}
    }

    const validateField = (field: keyof FormBasicRecovery['errors'], value: string): boolean => {
        switch (field) {
            case 'documentType':
                if (!value) {
                    formState.errors = { ...formState.errors, [field]: 'Selecciona un tipo de documento' }
                    return false
                }
                break
            case 'identification':
                if (!value) {
                    formState.errors = { ...formState.errors, [field]: 'Ingresa tu número de identificación' }
                    return false
                }
                if (value.length < 6) {
                    formState.errors = { ...formState.errors, [field]: 'El número de identificación debe tener al menos 6 caracteres' }
                    return false
                }
                break
            case 'email': {
                if (formRef.delivery_method !== 'email') break
                if (!value) {
                    formState.errors = { ...formState.errors, [field]: 'Ingresa tu correo electrónico' }
                    return false
                }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    formState.errors = { ...formState.errors, [field]: 'Ingresa un correo electrónico válido' }
                    return false
                }
                break
            }
            case 'whatsapp': {
                if (formRef.delivery_method !== 'whatsapp') break
                if (!value) {
                    formState.errors = { ...formState.errors, [field]: 'Ingresa tu WhatsApp' }
                    return false
                }
                if (!/^\+?\d{10,15}$/.test(value)) {
                    formState.errors = { ...formState.errors, [field]: 'Ingresa un número de WhatsApp válido' }
                    return false
                }
                break
            }
        }
        const { [field]: _omit, ...rest } = formState.errors
        formState.errors = rest
        return true
    }

    const handleFieldChange = (field: keyof FormBasicRecovery, value: string) => {
        ;(formState as any)[field] = value

        switch (field) {
            case 'documentType':
                formRef.coddoc = value
                break
            case 'identification':
                formRef.documento = value
                break
            case 'email':
                formRef.email = value
                break
            case 'whatsapp':
                formRef.whatsapp = value
                break
            case 'delivery_method':
                formRef.delivery_method = value as DeliveryMethod
                break
        }

        if (formState.errors[field as keyof FormBasicRecovery['errors']]) {
            validateField(field as keyof FormBasicRecovery['errors'], value)
        }
    }

    const handleSubmit = async (e: Event) => {
        e.preventDefault()

        const isDocumentTypeValid = validateField('documentType', formRef.coddoc)
        const isIdentificationValid = validateField('identification', formRef.documento)
        const isEmailValid = validateField('email', formRef.email)
        const isWhatsappValid = validateField('whatsapp', formRef.whatsapp)

        const requiresEmail = formRef.delivery_method === 'email'
        const requiresWhatsapp = formRef.delivery_method === 'whatsapp'

        const hasErrors =
            !isDocumentTypeValid || !isIdentificationValid ||
            (requiresEmail && !isEmailValid) ||
            (requiresWhatsapp && !isWhatsappValid)

        if (hasErrors) return

        formState.isSubmitting = true

        try {
            router.post(route('api.recovery_send'), formRef, {
                onSuccess: () => {
                    formState.isSuccess = true
                    formState.errors = {}
                },
                onError: (errors) => {
                    const firstError = Object.values(errors)[0]
                    const errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError)
                    formState.errors = { ...formState.errors, general: errorMessage || 'Error al enviar.' }
                },
                onFinish: () => {
                    formState.isSubmitting = false
                }
            })
        } catch {
            formState.errors = { ...formState.errors, general: 'No fue posible completar el envío.' }
        } finally {
            formState.isSubmitting = false
        }
    }

    return {
        formState,
        selectedUserType,
        userTypeRef,
        documentTypeOptions,
        handleUserTypeSelect,
        handleBack,
        handleFieldChange,
        handleSubmit
    }
}