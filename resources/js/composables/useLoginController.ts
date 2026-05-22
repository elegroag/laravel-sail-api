import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { TipoFuncionario } from '@/constants/auth'
import type { DocumentTypeOption, UserType } from '@/types/auth'

export function useLoginController(errors?: Record<string, string>) {
    const selectedUserType = ref<UserType | null>(null)
    const documentType = ref('')
    const identification = ref('')
    const password = ref('')
    const processing = ref(false)
    const coddoc = ref<Record<string, string>>({})
    const dialog = ref<{ message: string; type: 'success' | 'error' } | null>(null)

    const documentTypeOptions = computed<DocumentTypeOption[]>(() =>
        Object.entries(coddoc.value || {}).map(([value, label]) => ({ value, label }))
    )

    const loadParams = async () => {
        try {
            const response = await fetch(route('login.params'), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            const responseJson = await response.json()
            if (response.ok) {
                coddoc.value = responseJson?.Coddoc ?? {}
                const first = Object.keys(coddoc.value)[0]
                if (!documentType.value && first) {
                    documentType.value = first
                }
            } else {
                dialog.value = { message: responseJson?.message || 'Error al cargar parámetros.', type: 'error' }
            }
        } catch (error) {
            dialog.value = { message: 'No fue posible cargar los parámetros de login.', type: 'error' }
        }
    }

    const handleUserTypeSelect = (userType: UserType) => {
        selectedUserType.value = userType
    }

    const handleBack = () => {
        selectedUserType.value = null
        documentType.value = ''
        identification.value = ''
        password.value = ''
        dialog.value = null
    }

    const handleLogin = async (e: Event) => {
        e.preventDefault()
        processing.value = true
        dialog.value = null

        const tipoValue = TipoFuncionario[selectedUserType.value as keyof typeof TipoFuncionario]

        router.post(
            route('login.authenticate'),
            {
                documentType: documentType.value,
                password: password.value,
                identification: identification.value ? parseInt(identification.value) : null,
                tipo: tipoValue
            },
            {
                onSuccess: () => {},
                onError: () => {
                    dialog.value = { message: 'No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.', type: 'error' }
                },
                onFinish: () => {
                    processing.value = false
                }
            }
        )
    }

    onMounted(() => {
        if (errors?.message) {
            dialog.value = { message: errors.message, type: 'error' }
        }
        loadParams()
    })

    return {
        documentTypeOptions,
        selectedUserType,
        handleUserTypeSelect,
        handleBack,
        handleLogin,
        processing,
        dialog,
        setDialog: (d: typeof dialog.value) => { dialog.value = d },
        documentType,
        identification,
        password,
        setDocumentType: (v: string) => { documentType.value = v },
        setIdentification: (v: string) => { identification.value = v },
        setPassword: (v: string) => { password.value = v }
    }
}