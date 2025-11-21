import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout"
import AuthWelcome from "@/pages/Auth/components/auth-welcome"
import LoginForm from "@/pages/Auth/components/login-form"
import AuthUserTypeStep from "@/pages/Auth/components/auth-user-type-step"
import { userTypes } from "@/constants/auth"
import type { LoginProps, UserType } from "@/types/auth"
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes"
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert"
import useLoginController from "./controllers/use-login-controller";

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
    alertMessage,
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
      <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
        <AuthBackgroundShapes />

        <div className="max-w-md mx-auto w-full">
          {!selectedUserType ? (
            <AuthUserTypeStep
              title="Iniciar sesión portal"
              subtitle="en línea"
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

         {/* Alert de error */}
          {alertMessage && (
            <div className="mt-4">
              <Alert variant="destructive" className="w-100 mx-auto border-red-200" >
                <AlertTitle>Error</AlertTitle>
                <AlertDescription className="text-gray-500">{alertMessage}</AlertDescription>
              </Alert>
            </div>
          )}
      </div>
    </AuthLayout>
  )
}

