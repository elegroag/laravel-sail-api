import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { DeliveryMethod, VerifyEmailProps } from '@/types/auth'

interface VerificationState {
    code: string[]
    error: string | null
    canResend: boolean
    resendTimer: number
    isVerified: boolean
    deliveryMethod: DeliveryMethod
}

const initialState: VerificationState = {
    code: ['', '', '', ''],
    error: null,
    canResend: false,
    resendTimer: 300,
    isVerified: false,
    deliveryMethod: 'email'
}

function formatCountdown(totalSeconds: number): string {
    const minutes = Math.floor(totalSeconds / 60)
    const seconds = totalSeconds % 60
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

export function useVerifyController({ documento, coddoc, tipo, errors, error, option_request }: VerifyEmailProps) {
    const code = ref(['', '', '', ''])
    const verificationError = ref<string | null>(null)
    const canResend = ref(false)
    const resendTimer = ref(300)
    const isVerified = ref(false)
    const deliveryMethod = ref<DeliveryMethod>('email')

    const isSubmitting = ref(false)
    const dialog = ref<{ message: string; type: 'success' | 'error' } | null>(null)

    const formattedCountdown = computed(() => formatCountdown(resendTimer.value))
    const deliveryChannelLabel = computed(() => deliveryMethod.value === 'email' ? 'correo electrónico' : 'WhatsApp')
    const verificationCode = computed(() => code.value.join(''))

    let timerInterval: ReturnType<typeof setInterval> | null = null

    const startTimer = () => {
        if (timerInterval) clearInterval(timerInterval)
        timerInterval = setInterval(() => {
            if (resendTimer.value > 0) {
                resendTimer.value--
            } else {
                canResend.value = true
                clearInterval(timerInterval!)
                timerInterval = null
            }
        }, 1000)
    }

    const handleInputChange = (index: number, value: string) => {
        if (!/^[0-9]{0,1}$/.test(value)) return

        const lastDigit = value.slice(-1)
        const newCode = [...code.value]
        newCode[index] = lastDigit
        code.value = newCode
        verificationError.value = null
    }

    const handleKeyDown = (index: number, event: KeyboardEvent) => {
        if (event.key === 'Backspace' && !code.value[index] && index > 0) {
            // Move focus back
        }
        if (event.key === 'ArrowLeft' && index > 0) {
            // Move focus left
        }
        if (event.key === 'ArrowRight' && index < code.value.length - 1) {
            // Move focus right
        }
    }

    const handlePaste = (event: ClipboardEvent) => {
        event.preventDefault()
        const clipboardData = event.clipboardData
        if (!clipboardData) return
        const sanitized = clipboardData.getData('text').replace(/\D/g, '').slice(0, 4)
        const newCode = ['', '', '', '']
        sanitized.split('').forEach((digit, index) => {
            if (index < 4) newCode[index] = digit
        })
        code.value = newCode
    }

    const handleDeliveryMethodChange = (method: DeliveryMethod) => {
        if (deliveryMethod.value === method) return
        deliveryMethod.value = method
        canResend.value = true
        resendTimer.value = 0
        code.value = ['', '', '', '']
        verificationError.value = null
    }

    const handleVerify = async (event: Event) => {
        event.preventDefault()

        if (verificationCode.value.length !== 4) {
            verificationError.value = 'Por favor ingresa el código completo de verificación.'
            dialog.value = { message: verificationError.value, type: 'error' }
            return
        }

        if (!documento || !coddoc || !tipo) {
            verificationError.value = 'Faltan datos para validar tu cuenta.'
            dialog.value = { message: verificationError.value, type: 'error' }
            return
        }

        isSubmitting.value = true

        const payload = {
            tipo,
            coddoc,
            documento,
            option_request: option_request || '',
            code_1: code.value[0] || '',
            code_2: code.value[1] || '',
            code_3: code.value[2] || '',
            code_4: code.value[3] || ''
        }

        router.post(route('verify.action'), payload, {
            preserveScroll: true,
            preserveUrl: true,
            onSuccess: () => {
                isVerified.value = true
            },
            onError: (errors) => {
                const firstError = Object.values(errors)[0]
                const errorMessage = Array.isArray(firstError) ? firstError[0] : String(firstError)
                verificationError.value = errorMessage || 'No fue posible validar el código.'
dialog.value = { message: verificationError.value as string, type: 'error' }
            },
            onFinish: () => {
                isSubmitting.value = false
                code.value = ['', '', '', '']
            }
        })
    }

    const handleResendCode = async () => {
        if (!canResend.value) return

        isSubmitting.value = true

        const payload = {
            documento,
            coddoc,
            tipo,
            delivery_method: deliveryMethod.value
        }

        router.post(route('verify.resend'), payload, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                canResend.value = false
                resendTimer.value = 300
                code.value = ['', '', '', '']
                dialog.value = { message: `Código reenviado exitosamente. Revisa tu ${deliveryChannelLabel.value}.`, type: 'success' }
                startTimer()
            },
            onError: (errs) => {
                const firstError = Object.values(errs)[0]
                const errMsg = Array.isArray(firstError) ? firstError[0] : String(firstError)
                dialog.value = { message: errMsg || 'No fue posible reenviar el código.', type: 'error' }
            },
            onFinish: () => {
                isSubmitting.value = false
            }
        })
    }

    onMounted(() => {
        startTimer()

        if (errors && Object.keys(errors).length > 0) {
            const first = Object.values(errors)[0]
            const message = Array.isArray(first) ? first[0] : String(first)
            dialog.value = { message, type: 'error' }
        }
        if (error) {
            dialog.value = { message: error, type: 'error' }
        }
    })

    const state = reactive({
        code,
        error: verificationError,
        canResend,
        resendTimer,
        isVerified,
        deliveryMethod
    })

    onUnmounted(() => {
        if (timerInterval) clearInterval(timerInterval)
    })

    return {
        state,
        formattedCountdown,
        deliveryChannelLabel,
        handleDeliveryMethodChange,
        handleInputChange,
        handleKeyDown,
        handlePaste,
        handleVerify,
        handleResendCode,
        processing: isSubmitting,
        dialog,
        setDialog: (d: typeof dialog.value) => { dialog.value = d }
    }
}