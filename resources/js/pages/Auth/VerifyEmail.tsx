import { router } from '@inertiajs/react'
import { LoaderCircle, CheckCircle} from 'lucide-react'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AuthLayout from '@/layouts/AuthLayoutTemplate'
import AuthWelcome from './components/generic/AuthWelcome'
import LoadingAnimated from '@/components/loading-animated'
import type { VerifyEmailProps } from '@/types/auth'
import useVerifyController from '@/pages/Auth/hooks/useVerifyController'
import { DeliveryOptions } from '@/constants/auth'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'

export default function VerifyEmail({ documento, coddoc, tipo, option_request, token, status, errors }: VerifyEmailProps) {
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
        dialog,
        setDialog
    } = useVerifyController({
        token,
        documento,
        coddoc,
        tipo,
        errors,
        option_request
    })


  if (state.isVerified) {
    return (
      <AuthLayout title="Cuenta verificada" description="Tu correo ya fue verificado correctamente.">
        <div id="welcome" className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
          <AuthWelcome
            title="Verificación de correo"
            tagline="Confirma tu identidad"
            description="Te enviamos un código de 4 dígitos para asegurar que eres el propietario del correo registrado."
            backHref={route('login')}
            backText="¿Ya tienes cuenta? Inicia sesión"
          />
        </div>
      
        <div className="w-full lg:w-1/2 p-8 mx-auto flex flex-col items-center justify-center space-y-6 text-center min-h-[700px]">
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
              className="w-14 h-14 text-center text-xl font-semibold text-gray-700"
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
    
    {/* Loading animado durante verificación o reenvío */}
    <LoadingAnimated show={processing || isResending} />
    
    {/* Modal dialog para mensajes */}
    <Dialog open={dialog !== null} onOpenChange={(open) => !open && setDialog(null)}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle className={dialog?.type === 'success' ? 'text-emerald-600' : 'text-red-600'}>
            {dialog?.type === 'success' ? 'Verificación Exitosa' : 'Error de Verificación'}
          </DialogTitle>
        </DialogHeader>
        <div className="py-4">
          <p className="text-sm text-gray-700 whitespace-pre-line">{dialog?.message}</p>
        </div>
        <DialogFooter>
          <Button variant="outline" onClick={() => setDialog(null)}>
            Cerrar
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
    </AuthLayout>
  )
}
