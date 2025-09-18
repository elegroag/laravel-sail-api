import type React from "react"
import TextLink from "@/components/text-link"
import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Building2, GraduationCap, Briefcase, Users, Home, HardHat } from "lucide-react"
import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout"
import AuthWelcome from "@/components/auth-welcome"
import AuthUserTypeSelector from "@/components/auth-user-type-selector"
import LoginForm from "@/components/login-form"

type UserType = "empresa" | "independiente" | "facultativo" | "particular" | "domestico" | "trabajador"

interface UserTypeOption {
  id: UserType
  label: string
  icon: React.ReactNode
}

const userTypes: UserTypeOption[] = [
  { id: "empresa", label: "Empresa aportante", icon: <Building2 className="w-8 h-8 text-blue-500" /> },
  { id: "independiente", label: "Independiente aportante", icon: <GraduationCap className="w-8 h-8 text-green-500" /> },
  { id: "facultativo", label: "Facultativo", icon: <Briefcase className="w-8 h-8 text-purple-500" /> },
  { id: "particular", label: "Particular", icon: <Users className="w-8 h-8 text-orange-500" /> },
  { id: "domestico", label: "Servicio doméstico", icon: <Home className="w-8 h-8 text-red-500" /> },
  { id: "trabajador", label: "Trabajador", icon: <HardHat className="w-8 h-8 text-yellow-500" /> },
]

const documentTypes = [
  { value: "cc", label: "Cédula de Ciudadanía" },
  { value: "ce", label: "Cédula de Extranjería" },
  { value: "nit", label: "NIT" },
  { value: "pasaporte", label: "Pasaporte" },
]

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
        tagline="To Value Aims"
        description="Value Aims is an organization that provides valuable aims to people, homes, organizations and anybody that requires services such as at tabletten to volunteer inn value packed social duurzaam sign spla movement"
        backHref={route('register')}
        backText="Crear cuenta"
      />

      {/* Right Panel - Login Form */}
      <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
        <div className="absolute top-6 right-6 w-16 h-16 bg-gradient-to-br from-emerald-200 to-teal-300 rounded-2xl opacity-70"></div>
        <div className="absolute bottom-6 right-12 w-8 h-8 bg-gradient-to-tr from-emerald-300 to-green-400 rounded-lg opacity-50"></div>
        <div className="absolute top-1/3 left-6 w-12 h-12 bg-gradient-to-bl from-teal-200 to-emerald-200 rounded-full opacity-40"></div>

        <div className="max-w-md mx-auto w-full">
          {!selectedUserType ? (
            <>
              <AuthUserTypeSelector
                title="Iniciar sesión portal"
                subtitle="en línea"
                logoSrc={imageLogo}
                logoAlt="Comfaca Logo"
                userTypes={userTypes}
                onSelect={(id) => handleUserTypeSelect(id)}
              />

              <Button
                className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium mb-6 shadow-lg"
                disabled
              >
                Continuar
              </Button>

              <div className="flex justify-center space-x-8 text-sm">
                <button className="text-gray-500 hover:text-emerald-600 flex items-center">
                  <span className="mr-1">?</span>
                  Olvidé mi clave
                </button>
                
                <TextLink href={route('register')} 
                className="text-gray-500 hover:text-emerald-600 flex items-center">
                  <span className="mr-1">🔑</span>
                  Crear cuenta
                </TextLink>
              </div>
            </>
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