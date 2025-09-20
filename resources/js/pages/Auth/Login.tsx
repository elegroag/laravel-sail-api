import type React from "react"
import { useState, useMemo } from "react"
import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout"
import AuthWelcome from "@/pages/Auth/components/auth-welcome"
import LoginForm from "@/pages/Auth/components/login-form"
import AuthUserTypeStep from "@/pages/Auth/components/auth-user-type-step"
import { userTypes, TipoFuncionario } from "@/constants/auth"
import type { LoginProps, UserType } from "@/types/auth"
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes"
import { router } from '@inertiajs/react';
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert"

export default function Login({
    Coddoc,
    Tipsoc,
    Codciu,
    Detadoc
}: LoginProps)
{
  const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null)
  const [documentType, setDocumentType] = useState("")
  const [identification, setIdentification] = useState("")
  const [password, setPassword] = useState("")
  const [processing, setProcessing] = useState(false);
  // Estado para mostrar mensajes de error en un Alert
  const [alertMessage, setAlertMessage] = useState<string | null>(null)

  // Mapea Coddoc ({ [codigo]: descripcion }) a opciones { value, label } esperadas por LoginForm
  // Uso de useMemo para cumplir con buenas prácticas de rendimiento
  const documentTypeOptions = useMemo(
    () => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label })),
    [Coddoc]
  )

  const handleUserTypeSelect = (userType: UserType) => {
    setSelectedUserType(userType)
  }

  const handleBack = () => {
    setSelectedUserType(null)
    setDocumentType("")
    setIdentification("")
    setPassword("")
    // Limpiar alertas al volver atrás
    setAlertMessage(null)
  }

  const handleLogin = async (e: React.FormEvent) => {
      e.preventDefault();
      setProcessing(true);
      // Reiniciar cualquier alerta previa antes de intentar login
      setAlertMessage(null)
      try {
        const tipoValue = TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario];

        const response = await fetch(route('api.authenticate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
              documentType,
              password,
              identification: identification ? parseInt(identification) : null,
              tipo: tipoValue,
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            router.visit('/web/login');
        } else {
            // Mostrar mensaje proveniente de la API si está disponible
            const msg = (typeof data?.message === 'string' && data.message.trim().length > 0)
              ? data.message
              : 'Ocurrió un error al iniciar sesión. Intenta nuevamente.'
            const detail = data?.errors;
            setAlertMessage(msg + (detail ? '\n' + JSON.stringify(detail) : ''));
            if (data?.errors) {
              console.error(data.errors);
            } else {
              console.error('Error desconocido:', data);
            }
        }
      } catch (error) {
          // Captura de excepciones de red/u otras y alerta genérica
          console.error('Error al iniciar sesión:', error);
          setAlertMessage('No fue posible conectar con el servidor. Intenta nuevamente.');
      } finally {
          setProcessing(false);
      }
  };

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

