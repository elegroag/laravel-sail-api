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
import useLoginController from "./hooks/useLoginController";

export default function Login({
    errors
}: LoginProps)
{
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
    setDocumentType,
    setIdentification,
    setPassword,
  } = useLoginController({
    errors
  });


  return (
    <AuthLayout title="Inicio de sesión COMFACA EN LÍNEA" description="Bienvenido a Comfaca En Línea, el portal en línea de la Comfaca. Aquí podrás gestionar tus servicios y contratar nuevos servicios de manera segura y cómoda.">
      {/* Left Panel - Welcome Section */}
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

      {/* Right Panel - Login Form */}
      <div className="lg:w-1/2 p-6 flex flex-col justify-center relative">
        <AuthBackgroundShapes />

        <div className="max-w-md mx-auto w-full">
          {!selectedUserType ? (
            <AuthUserTypeStep
              title="Iniciar sesión portal"
              subtitle="Comfaca en línea"
              logoSrc={imageLogo}
              logoAlt="Comfaca Logo"
              userTypes={userTypes}
              onSelect={(id) => handleUserTypeSelect(id as UserType)}
              onForgotPassword={route('password.request')}
              continueDisabled
              registerHref={route('register')}
            />
          ) : (
            // Componente LoginForm extraído y reutilizable
            <LoginForm
              userTypes={userTypes}
              documentTypeOptions={documentTypeOptions}
              selectedUserType={selectedUserType}
              documentType={documentType}
              identification={identification}
              password={password}
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

      {/* Loading animado durante la autenticación */}
      <LoadingAnimated show={processing} />

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

