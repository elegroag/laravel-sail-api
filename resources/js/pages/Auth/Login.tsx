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
    Coddoc,
    Tipsoc,
    Codciu,
    Detadoc
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
    Coddoc,
    Tipsoc,
    Codciu,
    Detadoc
  });
    

  return (
    <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
      {/* Left Panel - Welcome Section */}
      <AuthWelcome
        title="BIENVENIDO"
        tagline="Comfaca En Línea"
        description="Bienvenido a Comfaca En Línea, el portal en línea de la Comfaca. Aquí podrás gestionar tus servicios y contratar nuevos servicios de manera segura y cómoda."
        backHref={route('register')}
        backText="Crear cuenta"
      />

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
              documentTypes={documentTypeOptions}
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
                <AlertDescription>{alertMessage}</AlertDescription>
              </Alert>
            </div>
          )}
      </div>
    </AuthLayout>
  )
}

