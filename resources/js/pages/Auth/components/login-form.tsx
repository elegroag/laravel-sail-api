import type React from "react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ChevronLeft } from "lucide-react"
import { DocumentTypeOption } from "@/types/auth"
import UserTypeDescription from "@/components/auth/user-type-description"

// Componente reutilizable para el formulario de login según el tipo de usuario seleccionado
// Principio SRP: Este componente solo se encarga de mostrar el formulario y manejar los callbacks recibidos por props
interface LoginFormProps {
  userTypes: { id: string; label: string }[]
  documentTypeOptions: DocumentTypeOption[]
  selectedUserType: string | null
  documentType: string
  identification: string
  password: string,
  processing: boolean,
  onBack: () => void
  onDocumentTypeChange: (value: string) => void
  onIdentificationChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onSubmit: (e: React.FormEvent) => void
}

// Patrón: Presentational Component
const LoginForm: React.FC<LoginFormProps> = ({
  userTypes,
  documentTypeOptions,
  selectedUserType,
  documentType,
  identification,
  password,
  onBack,
  onDocumentTypeChange,
  onIdentificationChange,
  onPasswordChange,
  onSubmit,
  processing,
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
          {selectedUserType && (
            <UserTypeDescription userTypeId={selectedUserType} />
          )}
        </div>
      </div>

      <form onSubmit={onSubmit} className="space-y-3 items-center justify-center">
        <div className="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 justify-center">
          <div className="w-80 mx-auto pt-3">
            <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
              Tipo de documento
            </Label>
            <Select value={documentType} onValueChange={onDocumentTypeChange}>
              <SelectTrigger className="in-b-form mt-1">
                <SelectValue placeholder="Selecciona el tipo de documento" />
              </SelectTrigger>
              <SelectContent>
                {documentTypeOptions.map((doc) => (
                  <SelectItem key={doc.value} value={doc.value}>
                    {doc.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="w-80 mx-auto pt-3">
            <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
              Número de identificación
            </Label>
            <Input
              id="identification"
              type="number"
              value={identification}
              onChange={(e) => onIdentificationChange(e.target.value)}
              placeholder="Ingresa tu número de identificación"
              className="in-b-form mt-1"
              required
            />
          </div>

          <div className="w-80 mx-auto pt-3 pb-4">
            <Label htmlFor="password" className="text-sm font-medium text-gray-700">
              Clave
            </Label>
            <Input
              id="password"
              type="password"
              value={password}
              onChange={(e) => onPasswordChange(e.target.value)}
              placeholder="Ingresa tu clave"
              className="in-b-form mt-1"
              required
            />
          </div>

          <div className="flex items-center justify-center">
          <Button
            type="submit"
            className="w-50 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg"
            disabled={!documentType || !identification || !password || processing}
          >
            {processing ? 'Iniciando sesión...' : 'Iniciar sesión'}
          </Button>
          </div>
        </div>
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
        <TextLink
          href={route('mercurio.noty_cambio_correo')}
          className="text-gray-500 hover:text-emerald-600 flex items-center"
        >
          <span className="mr-1">📧</span>
          Solicitar cambio de correo
        </TextLink>
      </div>
    </>
  )
}

export default LoginForm
