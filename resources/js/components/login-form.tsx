import type React from "react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ChevronLeft } from "lucide-react"

// Componente reutilizable para el formulario de login según el tipo de usuario seleccionado
// Principio SRP: Este componente solo se encarga de mostrar el formulario y manejar los callbacks recibidos por props
interface LoginFormProps {
  userTypes: { id: string; label: string }[]
  documentTypes: { value: string; label: string }[]
  selectedUserType: string | null
  documentType: string
  identification: string
  password: string
  onBack: () => void
  onDocumentTypeChange: (value: string) => void
  onIdentificationChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onSubmit: (e: React.FormEvent) => void
}

// Patrón: Presentational Component
const LoginForm: React.FC<LoginFormProps> = ({
  userTypes,
  documentTypes,
  selectedUserType,
  documentType,
  identification,
  password,
  onBack,
  onDocumentTypeChange,
  onIdentificationChange,
  onPasswordChange,
  onSubmit,
}) => {
  // Sección UI del formulario
  return (
    <>
      <div className="flex items-center mb-6">
        <button onClick={onBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
          <ChevronLeft className="w-5 h-5 text-gray-600" />
        </button>
        <div>
          <h2 className="text-xl font-semibold text-gray-800">
            {userTypes.find((ut) => ut.id === selectedUserType)?.label}
          </h2>
          <p className="text-sm text-gray-600">Ingresa tus credenciales</p>
        </div>
      </div>

      <form onSubmit={onSubmit} className="space-y-6">
        <div>
          <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
            Tipo de documento
          </Label>
          <Select value={documentType} onValueChange={onDocumentTypeChange}>
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
            onChange={(e) => onIdentificationChange(e.target.value)}
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
            onChange={(e) => onPasswordChange(e.target.value)}
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
        <TextLink href={route('password.request')} className="text-gray-500 hover:text-emerald-600 flex items-center" >
          <span className="mr-1">?</span>
          Olvidé mi clave
        </TextLink>
        <TextLink href={route('register')} className="text-gray-500 hover:text-emerald-600 flex items-center">
          <span className="mr-1">🔑</span>
          Crear cuenta
        </TextLink>
      </div>
    </>
  )
}

export default LoginForm
