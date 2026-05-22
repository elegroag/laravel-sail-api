import { ref, computed, onMounted } from 'vue'

type TipoAfiliado = 'T' | 'P' | 'O' | 'F' | 'I' | 'E' | 'S' | ''

interface Option {
    value: string
    label: string
}

export const tipoAfiliadoOptions: Option[] = [
    { value: 'E', label: 'Empresa o Empleador' },
    { value: 'T', label: 'Trabajador' },
    { value: 'I', label: 'Trabajador Independiente' },
    { value: 'P', label: 'Particular' },
    { value: 'O', label: 'Pensionado' },
    { value: 'F', label: 'Facultativo' }
]

export function useNotyEmailController(errors?: { message?: string }) {
    const alertMessage = ref<string | null>(null)
    const successMessage = ref<string | null>(null)
    const processing = ref(false)

    const coddoc = ref<Record<string, string>>({})

    const tipoAfiliado = ref<TipoAfiliado>('T')
    const documentType = ref('')
    const documento = ref('')
    const nombre = ref('')
    const telefono = ref('')
    const email = ref('')
    const novedad = ref('')

    const documentTypeOptions = computed<Option[]>(() =>
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
                const firstDoc = Object.keys(coddoc.value)[0]
                if (!documentType.value && firstDoc) {
                    documentType.value = firstDoc
                }
            } else {
                alertMessage.value = responseJson?.message || 'No fue posible cargar los parámetros.'
            }
        } catch (error) {
            alertMessage.value = 'No fue posible cargar los parámetros.'
        }
    }

    const handleSubmit = async (e: Event) => {
        e.preventDefault()
        processing.value = true
        alertMessage.value = null
        successMessage.value = null

        try {
            const response = await fetch(route('web.cambio_correo'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    tipo: tipoAfiliado.value,
                    coddoc: documentType.value,
                    documento: documento.value,
                    email: email.value,
                    telefono: telefono.value,
                    novedad: novedad.value
                })
            })

            const data = await response.json()

            if (response.ok && data?.success) {
                successMessage.value = data.msj || 'Tu solicitud de cambio de correo fue registrada correctamente. Nuestro equipo revisará la información.'
                telefono.value = ''
                novedad.value = ''
            } else {
                alertMessage.value = data?.msj || data?.message || 'No fue posible enviar la solicitud.'
            }
        } catch {
            alertMessage.value = 'Ocurrió un error al enviar la solicitud.'
        } finally {
            processing.value = false
        }
    }

    const handleNewRequest = () => {
        successMessage.value = null
        alertMessage.value = null
        tipoAfiliado.value = 'T'
        const firstDoc = Object.keys(coddoc.value)[0]
        documentType.value = firstDoc || ''
        documento.value = ''
        nombre.value = ''
        telefono.value = ''
        email.value = ''
        novedad.value = ''
    }

    onMounted(() => {
        if (errors?.message) {
            alertMessage.value = errors.message
        }
        loadParams()
    })

    return {
        alertMessage,
        successMessage,
        processing,
        tipoAfiliado,
        setTipoAfiliado: (v: TipoAfiliado) => { tipoAfiliado.value = v },
        documentType,
        setDocumentType: (v: string) => { documentType.value = v },
        documento,
        setDocumento: (v: string) => { documento.value = v },
        nombre,
        setNombre: (v: string) => { nombre.value = v },
        telefono,
        setTelefono: (v: string) => { telefono.value = v },
        email,
        setEmail: (v: string) => { email.value = v },
        novedad,
        setNovedad: (v: string) => { novedad.value = v },
        documentTypeOptions,
        tipoAfiliadoOptions,
        handleSubmit,
        handleNewRequest
    }
}