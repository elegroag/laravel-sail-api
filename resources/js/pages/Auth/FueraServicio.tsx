import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/pages/Auth/components/auth-welcome";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert";

interface FueraServicioProps {
  msj?: string;
}

export default function FueraServicio({ msj }: FueraServicioProps) {
  const message =
    msj ||
    "El sistema se encuentra en estado de actualización y mantenimiento.";

  return (
    <AuthLayout
      title="Plataforma fuera de servicio"
      description="La plataforma Comfaca En Línea se encuentra temporalmente fuera de servicio por labores de mantenimiento."
    >
      {/* Panel izquierdo - Bienvenida */}
      <div
        id="welcome"
        className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden"
      >
        <AuthWelcome
          title="FUERA DE SERVICIO"
          tagline="Comfaca En Línea"
          description={
            <>
              <p>
                En este momento la plataforma Comfaca En Línea se encuentra en
                proceso de actualización y mantenimiento para brindarte una
                experiencia más segura y confiable.
              </p>
              <p>
                Durante este periodo no podrás realizar trámites ni consultar
                información a través del portal. Te invitamos a intentarlo
                nuevamente más tarde.
              </p>
              <p>
                Agradecemos tu comprensión y estaremos restableciendo el
                servicio lo antes posible.
              </p>
            </>
          }
          backHref={route("login")}
          backText="Volver al inicio de sesión"
        />
      </div>

      {/* Panel derecho - Mensaje de fuera de servicio */}
      <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
        <AuthBackgroundShapes />

        <div className="max-w-md mx-auto w-full">
          <Alert className="border-amber-200 bg-amber-50">
            <AlertTitle className="font-semibold text-amber-800">
              Plataforma temporalmente fuera de servicio
            </AlertTitle>
            <AlertDescription className="mt-2 text-amber-900 text-sm leading-relaxed">
              <span
                dangerouslySetInnerHTML={{
                  __html: message,
                }}
              />
            </AlertDescription>
          </Alert>
        </div>
      </div>
    </AuthLayout>
  );
}
