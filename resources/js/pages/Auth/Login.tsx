import TextLink from "@/components/text-link"
import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Building2, GraduationCap, Briefcase, Users, Home, HardHat, ChevronLeft } from "lucide-react"
import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout"

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

interface LoginProps {
  status?: string;
  canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) 
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
      <div className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
        <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full -translate-y-16 translate-x-16 opacity-60"></div>
        <div className="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full translate-y-12 -translate-x-12 opacity-40"></div>
        <div className="absolute top-1/2 left-0 w-16 h-16 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full -translate-x-8 opacity-30"></div>

        <div className="relative z-10">
          <h1 className="text-4xl font-bold mb-2">BIENVENIDO</h1>
          <div className="w-16 h-0.5 bg-white mb-6"></div>
          <p className="text-emerald-100 text-lg mb-6">To Value Aims</p>
          <p className="text-emerald-100 text-sm leading-relaxed">
            Value Aims is an organization that provides valuable aims to people, homes, organizations and anybody
            that requires services such as at tabletten to volunteer inn value packed social duurzaam sign spla
            movement
          </p>
        </div>
      </div>

      {/* Right Panel - Login Form */}
      <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
        <div className="absolute top-6 right-6 w-16 h-16 bg-gradient-to-br from-emerald-200 to-teal-300 rounded-2xl opacity-70"></div>
        <div className="absolute bottom-6 right-12 w-8 h-8 bg-gradient-to-tr from-emerald-300 to-green-400 rounded-lg opacity-50"></div>
        <div className="absolute top-1/3 left-6 w-12 h-12 bg-gradient-to-bl from-teal-200 to-emerald-200 rounded-full opacity-40"></div>

        <div className="max-w-md mx-auto w-full">
          {!selectedUserType ? (
            <>
              <div className="mb-6 flex justify-center">
                <img
                  src={imageLogo}
                  alt="Comfaca Logo"
                  width={180}
                  height={60}
                  className="opacity-90"
                />
              </div>
              <h2 className="text-2xl font-semibold text-gray-800 mb-2 text-center">Iniciar sesión portal</h2>
              <p className="text-2xl font-semibold text-gray-800 mb-8 text-center">en línea</p>

              <div className="grid grid-cols-3 gap-4 mb-8">
                {userTypes.map((userType) => (
                  <button
                    key={userType.id}
                    onClick={() => handleUserTypeSelect(userType.id)}
                    className="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-emerald-500 hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 transition-all duration-200 group"
                  >
                    <div className="text-emerald-600 group-hover:text-emerald-700 mb-2">{userType.icon}</div>
                    <span className="text-xs text-gray-600 text-center font-medium">{userType.label}</span>
                  </button>
                ))}
              </div>

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
            <>
              <div className="flex items-center mb-6">
                <button onClick={handleBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                  <ChevronLeft className="w-5 h-5 text-gray-600" />
                </button>
                <div>
                  <h2 className="text-xl font-semibold text-gray-800">
                    {userTypes.find((ut) => ut.id === selectedUserType)?.label}
                  </h2>
                  <p className="text-sm text-gray-600">Ingresa tus credenciales</p>
                </div>
              </div>

              <form onSubmit={handleLogin} className="space-y-6">
                <div>
                  <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
                    Tipo de documento
                  </Label>
                  <Select value={documentType} onValueChange={setDocumentType}>
                    <SelectTrigger className="mt-1">
                      <SelectValue placeholder="Selecciona el tipo de documento" />
                    </SelectTrigger>
                    <SelectContent>
                      {documentTypes.map((doc) => (
                        <SelectItem key={doc.value} value={doc.value}>
                          {doc.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
                    Número de identificación
                  </Label>
                  <Input
                    id="identification"
                    type="text"
                    value={identification}
                    onChange={(e) => setIdentification(e.target.value)}
                    placeholder="Ingresa tu número de identificación"
                    className="mt-1"
                    required
                  />
                </div>

                <div>
                  <Label htmlFor="password" className="text-sm font-medium text-gray-700">
                    Clave
                  </Label>
                  <Input
                    id="password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Ingresa tu clave"
                    className="mt-1"
                    required
                  />
                </div>

                <Button
                  type="submit"
                  className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg"
                  disabled={!documentType || !identification || !password}
                >
                  Iniciar sesión
                </Button>
              </form>

              <div className="flex justify-center space-x-8 text-sm mt-6">
                
                <TextLink href={route('password.request')} 
                  className="text-gray-500 hover:text-emerald-600 flex items-center" >
                  <span className="mr-1">?</span>
                  Olvidé mi clave
                </TextLink>
                
                <TextLink href={route('register')} 
                  className="text-gray-500 hover:text-emerald-600 flex items-center">
                  <span className="mr-1">🔑</span>
                  Crear cuenta
                </TextLink>
              </div>
            </>
          )}
        </div>
      </div>
    </AuthLayout>
  )
}