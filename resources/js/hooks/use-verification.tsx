// Components
import { useEffect, useReducer, useRef, useMemo, useState } from 'react'
import { router, useForm } from '@inertiajs/react'
import type { DeliveryMethod,
    VerificationAction,
    VerificationState,
    VerifyEmailProps } from '@/types/auth'
import { MessageCircle, Mail } from 'lucide-react'

const initialState: VerificationState = {
    code: ['', '', '', ''],
    error: null,
    canResend: false,
    resendTimer: 300,
    isVerified: false,
    deliveryMethod: 'email',
}

const formatCountdown = (totalSeconds: number): string => {
    const minutes = Math.floor(totalSeconds / 60)
    const seconds = totalSeconds % 60
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}


const verificationReducer = (state: VerificationState, action: VerificationAction): VerificationState => {
    switch (action.type) {
        case 'SET_CODE_DIGIT': {
        const nextCode = [...state.code]
        nextCode[action.index] = action.value
        return { ...state, code: nextCode, error: null }
        }
        case 'SET_ERROR':
        return { ...state, error: action.error }
        case 'SET_CAN_RESEND':
        return { ...state, canResend: action.canResend }
        case 'SET_RESEND_TIMER':
        return { ...state, resendTimer: action.timer }
        case 'SET_VERIFIED':
        return { ...state, isVerified: action.verified }
        case 'SET_DELIVERY_METHOD':
        return { ...state, deliveryMethod: action.method }
        case 'RESET_CODE':
        return { ...state, code: ['', '', '', ''], error: null }
        default:
        return state
    }
}


export default function useVerification({
    token,
    documento,
    coddoc,
    tipo,
    status,
    errors,
}: VerifyEmailProps) {

    const inputRefs = useRef<Array<HTMLInputElement | null>>([])
    const [state, dispatch] = useReducer(verificationReducer, initialState)
    const [isResending, setIsResending] = useState(false)
    const formattedCountdown = useMemo(() => formatCountdown(state.resendTimer), [state.resendTimer])

    const deliveryChannelLabel = useMemo(
      () => (state.deliveryMethod === 'email' ? 'correo electrónico' : 'WhatsApp'),
      [state.deliveryMethod]
    )
    const VerificationChannelIcon = useMemo(() => (state.deliveryMethod === 'email' ? Mail : MessageCircle), [state.deliveryMethod])

    const { data, setData, post, processing } = useForm({
      token: token ?? '',
      documento: documento ?? '',
      coddoc: coddoc ?? '',
      tipo: tipo ?? '',
      tipafi: '',
      id: '',
      delivery_method: 'email' as DeliveryMethod,
      // Enviar el código en campos separados para el backend
      code_1: '',
      code_2: '',
      code_3: '',
      code_4: '',
    })

    // Sincroniza el medio seleccionado con el formulario para el backend
    useEffect(() => {
      setData('delivery_method', state.deliveryMethod)
    }, [setData, state.deliveryMethod])

    useEffect(() => {
      // Control del temporizador de reenvío para habilitar el botón tras 5 minutos
      if (!state.canResend && state.resendTimer > 0) {
        const timer = setTimeout(() => {
          dispatch({ type: 'SET_RESEND_TIMER', timer: state.resendTimer - 1 })
        }, 1000)
        return () => clearTimeout(timer)
      }

      if (state.resendTimer === 0) {
        dispatch({ type: 'SET_CAN_RESEND', canResend: true })
      }
    }, [state.canResend, state.resendTimer])

    // Mostrar errores iniciales provenientes de props (Inertia)
    useEffect(() => {
      if (errors && Object.keys(errors).length > 0) {
        const first = Object.values(errors)[0]
        const message = Array.isArray(first) ? first[0] : String(first)
        dispatch({ type: 'SET_ERROR', error: message })
      }
    }, [errors])

    const handleInputChange = (index: number, value: string) => {
      if (!/^[0-9]{0,1}$/.test(value)) {
        return
      }

      const lastDigit = value.slice(-1)
      dispatch({ type: 'SET_CODE_DIGIT', index, value: lastDigit })

      if (lastDigit && index < state.code.length - 1) {
        inputRefs.current[index + 1]?.focus()
      }
    }

    const handleKeyDown = (index: number, event: React.KeyboardEvent<HTMLInputElement>) => {
      if (event.key === 'Backspace' && !state.code[index] && index > 0) {
        inputRefs.current[index - 1]?.focus()
      }

      if (event.key === 'ArrowLeft' && index > 0) {
        inputRefs.current[index - 1]?.focus()
      }

      if (event.key === 'ArrowRight' && index < state.code.length - 1) {
        inputRefs.current[index + 1]?.focus()
      }
    }

    const handlePaste = (event: React.ClipboardEvent<HTMLInputElement>) => {
      event.preventDefault()
      const sanitized = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, state.code.length)

      sanitized.split('').forEach((digit, index) => {
        dispatch({ type: 'SET_CODE_DIGIT', index, value: digit })
      })

      const nextIndex = sanitized.length < state.code.length ? sanitized.length : state.code.length - 1
      inputRefs.current[nextIndex]?.focus()
    }

    const verificationCode = useMemo(() => state.code.join(''), [state.code])


    const handleDeliveryMethodChange = (method: DeliveryMethod) => {
      if (state.deliveryMethod === method) {
        return
      }

      // Permite cambiar el canal y solicitar un nuevo código sin esperar el temporizador previo
      dispatch({ type: 'SET_DELIVERY_METHOD', method })
      dispatch({ type: 'SET_CAN_RESEND', canResend: true })
      dispatch({ type: 'SET_RESEND_TIMER', timer: 0 })
      dispatch({ type: 'RESET_CODE' })
      dispatch({ type: 'SET_ERROR', error: null })
    }

    const handleVerify = (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault()
      if (verificationCode.length !== state.code.length) {
        dispatch({ type: 'SET_ERROR', error: 'Por favor ingresa el código completo de verificación.' })
        return
      }

      if (!data.documento || !data.coddoc || !data.tipo) {
        dispatch({
          type: 'SET_ERROR',
          error: 'Faltan datos para validar tu cuenta. Regresa al proceso de registro e inténtalo nuevamente.',
        })
        return
      }

      // Cargar los dígitos en los campos esperados por el backend
      setData('code_1', state.code[0] ?? '')
      setData('code_2', state.code[1] ?? '')
      setData('code_3', state.code[2] ?? '')
      setData('code_4', state.code[3] ?? '')
      
      setData('token', data.token)
      setData('tipo', data.tipo)
      setData('coddoc', data.coddoc)
      setData('documento', data.documento)

      post(route('verify.action'), {
        preserveScroll: true,
        onSuccess: () => {
          dispatch({ type: 'SET_VERIFIED', verified: true })
          router.visit(route('login'))
        },
        onError: (errors) => {
          dispatch({
            type: 'SET_ERROR',
            error: Object.values(errors)[0] ?? 'No fue posible validar el código. Intenta nuevamente.',
          })
        },
        onFinish: () => {
          // No limpiar el token, solo el código de verificación si aplica
          dispatch({ type: 'RESET_CODE' })
        },
      })
    }

    const handleResend = async () => {
      if (isResending || !state.canResend) {
        return
      }

      setIsResending(true)

      try {
        const response = await fetch(route('api.verify_store'), {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            documento: data.documento,
            coddoc: data.coddoc,
            tipo: data.tipo,
            delivery_method: data.delivery_method,
          }),
        })

        let responseBody: { success: boolean } | null = null

        try {
          responseBody = await response.json()
        } catch (parseError) {
          console.error('No fue posible interpretar la respuesta al reenviar código:', parseError)
        }

        if (response.ok && responseBody?.success) {
          dispatch({ type: 'RESET_CODE' })
          dispatch({ type: 'SET_CAN_RESEND', canResend: false })
          dispatch({ type: 'SET_RESEND_TIMER', timer: 300 })
          dispatch({ type: 'SET_ERROR', error: null })
          inputRefs.current[0]?.focus()
          return
        }

        if (typeof responseBody === 'object' && responseBody !== null && 'errors' in responseBody) {
          const errorsMap = responseBody.errors as Record<string, string | string[]>
          const [firstErrorEntry] = Object.values(errorsMap)
          const normalizedError = Array.isArray(firstErrorEntry) ? firstErrorEntry[0] : firstErrorEntry
          dispatch({ type: 'SET_ERROR', error: normalizedError ?? 'No fue posible reenviar el código. Intenta nuevamente.' })
          return
        }

        dispatch({
          type: 'SET_ERROR',
          error:
            typeof responseBody === 'object' && responseBody !== null && 'msj' in responseBody
              ? String((responseBody as { msj?: unknown }).msj ?? 'No fue posible reenviar el código. Intenta nuevamente.')
              : 'No fue posible reenviar el código. Intenta nuevamente.',
        })
      } catch (error) {
        console.error('Error al reenviar código:', error)
        dispatch({
          type: 'SET_ERROR',
          error: 'No fue posible reenviar el código. Intenta nuevamente.',
        })
      } finally {
        setIsResending(false)
      }
    }

    const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null)

    useEffect(() => {
        if (state.error) {
            setToast({ message: state.error, type: 'error' })
        }
    }, [state.error])


    return {
        state,
        inputRefs,
        formattedCountdown,
        deliveryChannelLabel,
        VerificationChannelIcon,
        handleDeliveryMethodChange,
        handleInputChange,
        handleKeyDown,
        handlePaste,
        handleVerify,
        handleResend,
        isResending,
        processing,
        toast,
        setToast
    }
}
