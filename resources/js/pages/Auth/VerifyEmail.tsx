import { router } from '@inertiajs/react'
import { LoaderCircle, CheckCircle, Mail, MessageCircle } from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AuthLayout from '@/layouts/auth-layout'
import AuthWelcome from './components/auth-welcome'
import type { DeliveryMethod, VerifyEmailProps } from '@/types/auth'
import useVerifyController from '@/pages/Auth/controllers/use-verify-controller'

export const DeliveryOptions: Array<{
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


export default function VerifyEmail({ documento, coddoc, tipo, token, status, errors }: VerifyEmailProps) {
    const {
        state,
        inputRefs,
        formattedCountdown,
        deliveryChannelLabel,
        VerificationChannelIcon,
        handleInputChange,
        handleDeliveryMethodChange,
        handleKeyDown,
        handlePaste,
        handleVerify,
        handleResend,
        isResending,
        processing,
        toast,
        setToast
    } = useVerifyController({
        token,
        documento,
        coddoc,
        tipo,
        errors
    })


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
      <div id="welcome" className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
      <AuthWelcome
        title="Verificación de correo"
        tagline="Confirma tu identidad"
        description="Te enviamos un código de 4 dígitos para asegurar que eres el propietario del correo registrado."
        backHref={route('login')}
        backText="¿Ya tienes cuenta? Inicia sesión"
      />
      </div>
      {status === 'verification-link-sent' && (
        <div className="mb-4 text-center text-sm font-medium text-emerald-600">
          Reenviamos un nuevo código de verificación a tu correo electrónico.
        </div>
      )}
      <div className="lg:w-1/2 p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px]">
      {errors && Object.keys(errors).length > 0 && (
        <div className="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-red-700 text-sm">
          <p className="font-medium">No fue posible validar tu información:</p>
          <ul className="mt-1 list-disc pl-5">
            {Object.values(errors).map((err, idx) => (
              <li key={idx}>{err}</li>
            ))}
          </ul>
        </div>
      )}
      <form onSubmit={handleVerify} className="mt-4 space-y-6">
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
            {DeliveryOptions.map(({ id, label, description, icon: Icon }) => {
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
    {toast && (
      <div
        className={`fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`}
      >
        {toast.message}
        <button
          type="button"
          className="ml-3 underline text-white/90 hover:text-white"
          onClick={() => setToast(null)}
        >
          Cerrar
        </button>
      </div>
    )}
    </AuthLayout>
  )
}
