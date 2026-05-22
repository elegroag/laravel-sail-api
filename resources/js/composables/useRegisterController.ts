import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { TipoFuncionario } from '@/constants/auth'
import type { FormState, UserType } from '@/types/auth'

type FormAction =
    | { type: 'SET_USER_TYPE'; payload: UserType }
    | { type: 'SET_FIELD'; field: keyof FormState; value: string }
    | { type: 'SET_ERROR'; field: string; error: string }
    | { type: 'CLEAR_ERRORS' }
    | { type: 'SET_SUBMITTING'; payload: boolean }
    | { type: 'RESET_FORM' }
    | { type: 'SET_SUCCESS'; payload: boolean }
    | { type: 'CLEAR_ERROR'; field: string }

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
    isSuccess: false
}

function formReducer(state: FormState, action: FormAction): FormState {
    switch (action.type) {
        case 'SET_USER_TYPE':
            return { ...state, selectedUserType: action.payload }
        case 'SET_FIELD':
            return {
                ...state,
                [action.field]: action.value,
                errors: { ...state.errors, [action.field]: '' }
            }
        case 'SET_ERROR':
            return { ...state, errors: { ...state.errors, [action.field]: action.error } }
        case 'CLEAR_ERRORS':
            return { ...state, errors: {} }
        case 'CLEAR_ERROR': {
            const { [action.field]: _omit, ...rest } = state.errors
            return { ...state, errors: rest }
        }
        case 'SET_SUBMITTING':
            return { ...state, isSubmitting: action.payload }
        case 'RESET_FORM':
            return initialState
        case 'SET_SUCCESS':
            return { ...state, isSuccess: action.payload }
        default:
            return state
    }
}

export function useRegisterController(Coddoc: Record<string, string> = {}, Tipsoc: Record<string, string> = {}, Codciu: Record<string, string> = {}, errors: Record<string, string> = {}) {
    const state = reactive<FormState>({ ...initialState })
    const step = ref(1)
    const dialog = ref<{ message: string; type: 'success' | 'error'; showLoginButton?: boolean } | null>(null)

    const documentTypeOptions = computed(() =>
        Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label }))
    )
    const cityOptions = computed(() =>
        Object.entries(Codciu || {}).map(([value, label]) => ({ value, label }))
    )
    const societyOptions = computed(() =>
        Object.entries(Tipsoc || {}).map(([value, label]) => ({ value, label }))
    )
    const companyCategoryOptions = [
        { value: 'N', label: 'NATURAL' },
        { value: 'J', label: 'JURIDICA' }
    ]

    const dispatch = (action: FormAction) => {
        formReducer(state, action)
    }

    const handleUserTypeSelect = (userType: UserType) => {
        state.selectedUserType = userType
        state.contributionRate = ''
        step.value = 1
    }

    const handleBack = () => {
        if (state.selectedUserType === 'empresa' && step.value === 2) {
            step.value = 1
        } else {
            resetForm()
            step.value = 1
        }
    }

    const resetForm = () => {
        Object.assign(state, initialState)
    }

    const validateStep = (): boolean => {
        state.errors = {}

        const isCompany = state.selectedUserType === 'empresa'
        const isWorker = state.selectedUserType === 'trabajador'
        const isIndependent = state.selectedUserType === 'independiente'
        const isPensioner = state.selectedUserType === 'pensionado'

        if (isCompany) {
            return validateCompanyStep()
        }
        if (isWorker) {
            return validateWorkerStep()
        }

        return validatePersonStep()
    }

    function validateCompanyStep(): boolean {
        if (step.value === 1) {
            if (!state.companyName?.trim()) {
                state.errors = { ...state.errors, companyName: 'El nombre de la empresa es requerido' }
            }
            if (!state.companyNit?.trim()) {
                state.errors = { ...state.errors, companyNit: 'El NIT de la empresa es requerido' }
            }
            if (!state.documentType) {
                state.errors = { ...state.errors, documentType: 'El tipo de documento es requerido' }
            }
            if (!state.societyType) {
                state.errors = { ...state.errors, societyType: 'El tipo de sociedad es requerido' }
            }
            if (!state.companyCategory) {
                state.errors = { ...state.errors, companyCategory: 'La categoría es requerida' }
            }
            return Object.keys(state.errors).length === 0
        }

        if (step.value === 2 && state.companyCategory === 'J') {
            if (!state.userRole) {
                state.errors = { ...state.errors, userRole: 'Debes indicar si eres representante o delegado' }
            }
            return Object.keys(state.errors).length === 0
        }

        if (step.value === 3) {
            if (!state.repName?.trim()) {
                state.errors = { ...state.errors, repName: 'El nombre del representante es requerido' }
            }
            if (!state.repIdentification?.trim()) {
                state.errors = { ...state.errors, repIdentification: 'La identificación es requerida' }
            }
            if (!state.repEmail?.trim()) {
                state.errors = { ...state.errors, repEmail: 'El email es requerido' }
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.repEmail)) {
                state.errors = { ...state.errors, repEmail: 'Email inválido' }
            }
            if (!state.repPhone?.trim()) {
                state.errors = { ...state.errors, repPhone: 'El celular es requerido' }
            }
            return Object.keys(state.errors).length === 0
        }

        if (step.value === 4 && state.userRole === 'delegado') {
            if (!state.firstName?.trim()) {
                state.errors = { ...state.errors, firstName: 'El nombre es requerido' }
            }
            if (!state.lastName?.trim()) {
                state.errors = { ...state.errors, lastName: 'El apellido es requerido' }
            }
            if (!state.email?.trim()) {
                state.errors = { ...state.errors, email: 'El email es requerido' }
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
                state.errors = { ...state.errors, email: 'Email inválido' }
            }
            if (!state.city) {
                state.errors = { ...state.errors, city: 'La ciudad es requerida' }
            }
            return Object.keys(state.errors).length === 0
        }

        return true
    }

    function validateWorkerStep(): boolean {
        if (step.value === 1) {
            if (!state.firstName?.trim()) {
                state.errors = { ...state.errors, firstName: 'El nombre es requerido' }
            }
            if (!state.lastName?.trim()) {
                state.errors = { ...state.errors, lastName: 'El apellido es requerido' }
            }
            if (!state.email?.trim()) {
                state.errors = { ...state.errors, email: 'El email es requerido' }
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
                state.errors = { ...state.errors, email: 'Email inválido' }
            }
            if (!state.city) {
                state.errors = { ...state.errors, city: 'La ciudad es requerida' }
            }
            return Object.keys(state.errors).length === 0
        }

        if (step.value === 2) {
            if (!state.companyNit?.trim()) {
                state.errors = { ...state.errors, companyNit: 'El NIT es requerido' }
            }
            if (!state.companyName?.trim()) {
                state.errors = { ...state.errors, companyName: 'La razón social es requerida' }
            }
            if (!state.position?.trim()) {
                state.errors = { ...state.errors, position: 'El cargo es requerido' }
            }
            return Object.keys(state.errors).length === 0
        }

        return true
    }

    function validatePersonStep(): boolean {
        if (step.value === 1) {
            if (!state.firstName?.trim()) {
                state.errors = { ...state.errors, firstName: 'El nombre es requerido' }
            }
            if (!state.lastName?.trim()) {
                state.errors = { ...state.errors, lastName: 'El apellido es requerido' }
            }
            if (!state.email?.trim()) {
                state.errors = { ...state.errors, email: 'El email es requerido' }
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
                state.errors = { ...state.errors, email: 'Email inválido' }
            }
            if (!state.city) {
                state.errors = { ...state.errors, city: 'La ciudad es requerida' }
            }
            if ((state.selectedUserType === 'independiente' || state.selectedUserType === 'pensionado') && !state.contributionRate) {
                state.errors = { ...state.errors, contributionRate: 'Selecciona la tasa de contribución' }
            }
            return Object.keys(state.errors).length === 0
        }

        return true
    }

    const handleNextStep = () => {
        const isCompany = state.selectedUserType === 'empresa'
        const isWorker = state.selectedUserType === 'trabajador'

        if (isCompany && state.companyCategory === 'N' && step.value === 1) {
            step.value = 3
            return
        }

        const maxSteps = isCompany ? (state.userRole === 'delegado' ? 5 : 4) : isWorker ? 3 : 2
        if (validateStep()) {
            step.value = Math.min(step.value + 1, maxSteps)
        }
    }

    const handlePrevStep = () => {
        step.value = Math.max(step.value - 1, 1)
    }

    const buildRegisterPayload = () => {
        const tipoValue = TipoFuncionario[state.selectedUserType as keyof typeof TipoFuncionario]
        const isCompany = state.selectedUserType === 'empresa'
        const isWorker = state.selectedUserType === 'trabajador'

        const payload: Record<string, any> = {
            selected_user_type: state.selectedUserType,
            tipo: tipoValue,
            coddoc: state.documentTypeUser,
            documento: state.identification,
            password: state.password
        }

        if (isCompany) {
            payload.tipdoc = state.documentType
            payload.razsoc = state.companyName
            payload.nit = state.companyNit
            payload.tipsoc = state.societyType
            payload.tipper = state.companyCategory
            payload.nombre = state.repName
            payload.email = state.repEmail
            payload.telefono = state.repPhone
            payload.codciu = state.city
            payload.rep_nombre = state.repName
            payload.rep_documento = state.repIdentification
            payload.rep_email = state.repEmail
            payload.rep_telefono = state.repPhone
            payload.rep_coddoc = state.documentTypeRep
        } else {
            payload.nombre = `${state.firstName} ${state.lastName}`.trim()
            payload.email = state.email
            payload.telefono = state.phone
            payload.codciu = state.city
            payload.first_name = state.firstName
            payload.last_name = state.lastName
        }

        if (isCompany) {
            payload.is_delegado = state.userRole === 'delegado'
            payload.cargo = state.userRole === 'delegado' ? state.position : undefined
        }

        if (isWorker && state.position) {
            payload.cargo = state.position
        }

        if ((state.selectedUserType === 'independiente' || state.selectedUserType === 'pensionado') && state.contributionRate) {
            payload.contribution_rate = state.contributionRate
        }

        return payload
    }

    const resolveRegisterRouteName = (tipo: string) => {
        switch (tipo) {
            case 'E': return 'register.empresa'
            case 'T': return 'register.trabajador'
            case 'P': return 'register.particular'
            case 'I': return 'register.independiente'
            case 'O': return 'register.pensionado'
            case 'F': return 'register.facultativo'
            default: return 'register'
        }
    }

    const handleRegister = async (e: Event) => {
        e.preventDefault()
        if (!validateStep()) return

        state.isSubmitting = true

        try {
            const payload = buildRegisterPayload()
            const routeName = resolveRegisterRouteName(payload.tipo)

            router.post(route(routeName), payload, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: (page: any) => {
                    dialog.value = { message: '¡Registro exitoso! Serás redirigido al login.', type: 'success' }
                    resetForm()
                    step.value = 1
                    const successData = page.props?.data
                    if (successData) {
                        setTimeout(() => {
                            router.visit(route('verify.show', {
                                tipo: successData.tipo,
                                coddoc: successData.coddoc,
                                documento: successData.documento,
                                option_request: 'register'
                            }))
                        }, 1000)
                    }
                },
                onError: (errs) => {
                    const firstError = Object.values(errs)[0]
                    const errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError)
                    const showLoginButton = errorMessage.includes('ya existe')
                    dialog.value = { message: errorMessage, type: 'error', showLoginButton }
                },
                onFinish: () => {
                    state.isSubmitting = false
                }
            })
        } catch (error: any) {
            dialog.value = { message: error.message || 'No fue posible completar el registro.', type: 'error' }
        }
    }

    return {
        state,
        step,
        dialog,
        setDialog: (d: typeof dialog.value) => { dialog.value = d },
        dispatch,
        documentTypeOptions,
        cityOptions,
        societyOptions,
        companyCategoryOptions,
        events: {
            handleBack,
            handleNextStep,
            handlePrevStep,
            handleRegister,
            handleUserTypeSelect
        },
        validateStep
    }
}