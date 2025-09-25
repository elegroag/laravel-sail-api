// Components
import { useEffect, useReducer, useRef, useMemo, useState } from 'react'
import { router, useForm } from '@inertiajs/react'
import { LoaderCircle, CheckCircle, Mail, MessageCircle } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AuthLayout from '@/layouts/auth-layout'
import AuthWelcome from './components/auth-welcome'

type VerificationState = {
  code: string[]
  error: string | null
  canResend: boolean
  resendTimer: number
  isVerified: boolean
  deliveryMethod: DeliveryMethod
}

type VerificationAction =
  | { type: 'SET_CODE_DIGIT'; index: number; value: string }
  | { type: 'SET_ERROR'; error: string | null }
  | { type: 'SET_CAN_RESEND'; canResend: boolean }
  | { type: 'SET_RESEND_TIMER'; timer: number }
  | { type: 'SET_VERIFIED'; verified: boolean }
  | { type: 'SET_DELIVERY_METHOD'; method: DeliveryMethod }
  | { type: 'RESET_CODE' }

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

type DeliveryMethod = 'email' | 'whatsapp'

const DELIVERY_OPTIONS: Array<{
  id: DeliveryMethod
  label: string
  description: string
  icon: typeof Mail
}> = [
  {
    id: 'email',
    label: 'Correo electrónico',
    description: 'Recibirás el código en tu bandeja de entrada asociada.',
    icon: Mail,
  },
  {
    id: 'whatsapp',
    label: 'WhatsApp',
    description: 'Enviaremos el código a tu número registrado por WhatsApp.',
    icon: MessageCircle,
  },
]

function verificationReducer(state: VerificationState, action: VerificationAction): VerificationState {
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

type VerifyEmailProps = {
  documento?: string
  coddoc?: string
  tipo?: string
  status?: string
  token?: string
}

export default function VerifyEmail({ documento, coddoc, tipo, token, status }: VerifyEmailProps) {
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

  // Ya no sobreescribimos 'token' con el código; 'token' mantiene el JWT temporal del backend

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
      const response = await fetch(route('verify.resend'), {
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

  if (state.isVerified) {
    return (
      <AuthLayout title="Cuenta verificada" description="Tu correo ya fue verificado correctamente.">
        <div className="flex flex-col items-center justify-center space-y-6 text-center py-16">
          <CheckCircle className="h-16 w-16 text-emerald-600" />
          <h1 className="text-3xl font-semibold">¡Email verificado!</h1>
          <p className="text-muted-foreground max-w-md">
            Tu correo ha sido confirmado exitosamente. Ahora puedes iniciar sesión y continuar con tu experiencia en la
            plataforma.
          </p>
          <Button onClick={() => router.visit(route('login'))} className="px-8">
            Ir al inicio de sesión
          </Button>
        </div>
      </AuthLayout>
    )
  }

  return (
    <AuthLayout
      title="Verificación de correo electrónico"
      description="Ingresa el código enviado a tu correo para validar tu cuenta."
    >
      <AuthWelcome
        title="Verificación de correo"
        tagline="Confirma tu identidad"
        description="Te enviamos un código de 4 dígitos para asegurar que eres el propietario del correo registrado."
        backHref={route('login')}
        backText="¿Ya tienes cuenta? Inicia sesión"
      />

      {status === 'verification-link-sent' && (
        <div className="mb-4 text-center text-sm font-medium text-emerald-600">
          Reenviamos un nuevo código de verificación a tu correo electrónico.
        </div>
      )}
      <div className="lg:w-1/2 p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px]">
      <form onSubmit={handleVerify} className="mt-8 space-y-6">
        <div className="text-center space-y-2">
          <VerificationChannelIcon className="h-12 w-12 text-emerald-600 mx-auto" />
          <p className="text-sm text-muted-foreground">
            {state.deliveryMethod === 'email'
              ? 'Ingresa el código de verificación que enviamos a tu correo electrónico. Si no lo encuentras, revisa tu bandeja de spam o correo no deseado.'
              : 'Ingresa el código de verificación que enviamos a tu WhatsApp. Revisa la conversación con nuestro número verificado.'}
          </p>
        </div>

        <div className="space-y-3">
          <p className="text-sm font-medium text-center text-muted-foreground">
            Selecciona dónde deseas recibir el código ({deliveryChannelLabel}).
          </p>
          <div className="grid gap-3 sm:grid-cols-2">
            {DELIVERY_OPTIONS.map(({ id, label, description, icon: Icon }) => {
              const isActive = state.deliveryMethod === id
              return (
                <button
                  key={id}
                  type="button"
                  onClick={() => handleDeliveryMethodChange(id)}
                  className={`rounded-lg border px-4 py-3 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 ${
                    isActive ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm' : 'border-muted'
                  }`}
                >
                  <div className="flex items-center gap-3">
                    <span className={`inline-flex items-center justify-center rounded-full border p-2 ${isActive ? 'border-emerald-400 bg-white text-emerald-600' : 'border-muted text-muted-foreground'}`}>
                      <Icon className="h-5 w-5" />
                    </span>
                    <div>
                      <p className="text-sm font-semibold">{label}</p>
                      <p className="text-xs text-muted-foreground">{description}</p>
                    </div>
                  </div>
                </button>
              )
            })}
          </div>
        </div>

        <div className="flex justify-center gap-3">
          {state.code.map((digit, index) => (
            <Input
              key={`digit-${index}`}
              ref={(element) => {
                inputRefs.current[index] = element
              }}
              type="text"
              inputMode="numeric"
              maxLength={1}
              value={digit}
              onChange={(event) => handleInputChange(index, event.target.value)}
              onKeyDown={(event) => handleKeyDown(index, event)}
              onPaste={index === 0 ? handlePaste : undefined}
              className="w-14 h-14 text-center text-xl font-semibold"
              placeholder="0"
            />
          ))}
        </div>

        {state.error && <p className="text-center text-sm text-red-600">{state.error}</p>}

        <div className="space-y-4">
          <Button type="submit" disabled={processing} className="w-full">
            {processing ? (
              <span className="flex items-center justify-center gap-2">
                <LoaderCircle className="h-4 w-4 animate-spin" />
                Validando código...
              </span>
            ) : (
              'Verificar código'
            )}
          </Button>

          <p className="text-center text-sm text-muted-foreground">
            ¿No recibiste el código?
            {state.canResend ? (
              <button
                type="button"
                onClick={handleResend}
                className="ml-1 font-medium text-emerald-600 hover:text-emerald-700"
                disabled={processing || isResending}
              >
                Reenviar código
              </button>
            ) : (
              <span className="ml-1 text-sm">Podrás volver a solicitarlo en {formattedCountdown}</span>
            )}
          </p>
        </div>
      </form>
    </div>
    </AuthLayout>
  )
}
