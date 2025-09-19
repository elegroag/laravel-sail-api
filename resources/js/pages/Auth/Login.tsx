import type React from "react"
import { useState } from "react"
import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout"
import AuthWelcome from "@/pages/Auth/components/auth-welcome"
import LoginForm from "@/pages/Auth/components/login-form"
import AuthUserTypeStep from "@/pages/Auth/components/auth-user-type-step"
import { userTypes, documentTypes } from "@/constants/auth"
import type { UserType } from "@/types/auth"
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes"

// Tipos y constantes centralizados importados

export default function Login()
{
  const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null)
  const [documentType, setDocumentType] = useState("")
  const [identification, setIdentification] = useState("")
  const [password, setPassword] = useState("")

  const handleUserTypeSelect = (userType: UserType) => {
    setSelectedUserType(userType)
  }

  const handleBack = () => {
    setSelectedUserType(null)
    setDocumentType("")
    setIdentification("")
    setPassword("")
  }

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault()

    console.log("[v0] Login attempt:", {
      selectedUserType,
      documentType,
      identification: identification.substring(0, 3) + "***", // Mask for security
      passwordLength: password.length,
    })

    // Simulate login process
    alert(`Iniciando sesión como ${userTypes.find((ut) => ut.id === selectedUserType)?.label}...`)
  }

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
              onForgotPassword={() => alert("Recuperación de clave próximamente")}
              continueDisabled
              registerHref={route('register')}
            />
          ) : (
            // Componente LoginForm extraído y reutilizable
            <LoginForm
              userTypes={userTypes}
              documentTypes={documentTypes}
              selectedUserType={selectedUserType}
              documentType={documentType}
              identification={identification}
              password={password}
              onBack={handleBack}
              onDocumentTypeChange={setDocumentType}
              onIdentificationChange={setIdentification}
              onPasswordChange={setPassword}
              onSubmit={handleLogin}
            />
          )}
        </div>
      </div>
    </AuthLayout>
  )
}
