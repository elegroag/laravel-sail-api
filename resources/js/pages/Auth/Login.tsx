import imageLogo from "@/assets/comfaca-logo.png";
import AuthLayout from "@/layouts/AuthLayoutTemplate"
import AuthWelcome from "@/pages/Auth/components/generic/AuthWelcome"
import LoginForm from "@/pages/Auth/components/login/LoginForm"
import AuthUserTypeStep from "@/pages/Auth/components/generic/AuthUserTypeStep"
import LoadingAnimated from "@/components/loading-animated"
import { userTypes } from "@/constants/auth"
import type { LoginProps, UserType } from "@/types/auth"
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes"
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { usePage } from "@inertiajs/react"
import { useCallback, useEffect, useState } from "react"
import useLoginController from "./hooks/useLoginController";

const PROMO_COOLDOWN_MS = 10 * 60 * 1000;

function promoBannerStorageKey(bannerId?: number): string {
  return `promo_banner_last_shown_at_${bannerId ?? 'default'}`;
}

function shouldShowPromoBanner(bannerId?: number): boolean {
  try {
    const last = Number(localStorage.getItem(promoBannerStorageKey(bannerId)) || 0);
    return !last || Date.now() - last >= PROMO_COOLDOWN_MS;
  } catch {
    return true;
  }
}

function markPromoBannerShown(bannerId?: number): void {
  try {
    localStorage.setItem(promoBannerStorageKey(bannerId), String(Date.now()));
  } catch {
    // ignore quota / private mode
  }
}

export default function Login({
    errors,
    promoBanner = null,
}: LoginProps)
{
  const { props: pageProps } = usePage<{ recaptcha_site_key?: string }>();
  const recaptchaSiteKey = pageProps.recaptcha_site_key;
  const [promoOpen, setPromoOpen] = useState(false);

  useEffect(() => {
    if (!promoBanner || (!promoBanner.image_url && !promoBanner.content_html)) {
      return;
    }

    if (shouldShowPromoBanner(promoBanner.id)) {
      markPromoBannerShown(promoBanner.id);
      setPromoOpen(true);
    }
  }, [promoBanner]);

  const handlePromoOpenChange = useCallback((open: boolean) => {
    if (!open) {
      markPromoBannerShown(promoBanner?.id);
    }
    setPromoOpen(open);
  }, [promoBanner?.id]);

  const {
    documentTypeOptions,
    selectedUserType,
    handleUserTypeSelect,
    handleBack,
    handleLogin,
    processing,
    dialog,
    setDialog,
    documentType,
    identification,
    password,
    captchaToken,
    setCaptchaToken,
    setDocumentType,
    setIdentification,
    setPassword,
  } = useLoginController({
    errors
  });


  return (
    <AuthLayout title="Inicio de sesión COMFACA EN LÍNEA" description="Bienvenido a Comfaca En Línea, el portal en línea de la Comfaca. Aquí podrás gestionar tus servicios y contratar nuevos servicios de manera segura y cómoda.">
      {/* Left Panel - Login Form */}
      <div className="lg:w-1/2 p-6 flex flex-col justify-center relative">
        <AuthBackgroundShapes />

        <div className="relative z-10 max-w-md mx-auto w-full">
          {!selectedUserType ? (
            <AuthUserTypeStep
              title="Iniciar sesión"
              logoSrc={imageLogo}
              logoAlt="Comfaca Logo"
              userTypes={userTypes}
              onSelect={(id) => handleUserTypeSelect(id as UserType)}
              onForgotPassword={route('password.request')}
              continueDisabled
              registerHref={route('register')}
            />
          ) : (
            <LoginForm
              userTypes={userTypes}
              documentTypeOptions={documentTypeOptions}
              selectedUserType={selectedUserType}
              documentType={documentType}
              identification={identification}
              password={password}
              recaptchaSiteKey={recaptchaSiteKey}
              captchaToken={captchaToken}
              onCaptchaChange={setCaptchaToken}
              onBack={handleBack}
              onDocumentTypeChange={setDocumentType}
              onIdentificationChange={setIdentification}
              onPasswordChange={setPassword}
              onSubmit={handleLogin}
              processing={processing}
            />
          )}
        </div>
      </div>

      {/* Right Panel - Welcome Section */}
      <div id="welcome" className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
        <AuthWelcome
          title="BIENVENIDO"
          tagline="Comfaca En Línea"
          description={
            <>
              <p>
                Bienvenido a Comfaca En Línea, el portal virtual de la Caja de Compensación Familiar del Caquetá – COMFACA, dispuesto para facilitar la gestión de los procesos de afiliación de aportantes, trabajadores dependientes, independientes y pensionados, bajo criterios de eficiencia, seguridad y confiabilidad.
              </p>
              <p>
                Mediante esta plataforma podrá reportar novedades, consultar la información de sus afiliados, realizar trámites administrativos y acceder a los servicios institucionales que ofrece la Caja, contribuyendo a la optimización del tiempo y a la reducción de desplazamientos físicos.
              </p>
              <p>
                Cree su cuenta y acceda de manera segura a los beneficios y servicios dispuestos por COMFACA.
              </p>
            </>
          }
          backHref={route('register')}
          backText="Crear cuenta"
        />
      </div>

      {/* Loading animado durante la autenticación */}
      <LoadingAnimated show={processing} />

      {/* Dialog promocional configurable desde Cajas */}
      <Dialog open={promoOpen} onOpenChange={handlePromoOpenChange}>
        <DialogContent
          className={[
            'gap-2 overflow-y-auto border-zinc-800 bg-zinc-950 text-zinc-100 dark:bg-zinc-950',
            // Tamaño mínimo 350px; mobile con márgenes seguros
            'top-[50%] left-[50%] min-w-[350px] w-[calc(100vw-1rem)] max-w-[calc(100vw-1rem)] max-h-[min(92dvh,92vh)] translate-x-[-50%] translate-y-[-50%] p-2',
            // Desktop+: se adapta al contenido / tamaño natural de la imagen
            'sm:w-fit sm:min-w-[350px] sm:max-w-[min(100vw-2rem,96vw)] sm:max-h-[95vh] sm:p-2',
          ].join(' ')}
        >
          <DialogHeader className="sr-only">
            <DialogTitle>Información</DialogTitle>
          </DialogHeader>
          <div className="space-y-2">
            {promoBanner?.image_url ? (
              <img
                src={promoBanner.image_url}
                alt="Banner promocional"
                className="mx-auto block h-auto w-full max-w-full rounded-md object-contain sm:w-auto sm:max-w-none"
              />
            ) : null}
            {promoBanner?.content_html ? (
              <div
                className="prose prose-sm prose-invert max-w-none px-1 text-zinc-200"
                dangerouslySetInnerHTML={{ __html: promoBanner.content_html }}
              />
            ) : null}
          </div>
          <DialogFooter className="sm:justify-end">
            <Button
              variant="outline"
              className="w-full border-zinc-700 bg-zinc-900 text-zinc-100 hover:bg-zinc-800 hover:text-white sm:w-auto"
              onClick={() => handlePromoOpenChange(false)}
            >
              Cerrar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Modal dialog para mensajes */}
      <Dialog open={dialog !== null} onOpenChange={(open) => !open && setDialog(null)}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle className={dialog?.type === 'success' ? 'text-emerald-600' : 'text-red-600'}>
              {dialog?.type === 'success' ? 'Éxito' : 'Error de Autenticación'}
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
