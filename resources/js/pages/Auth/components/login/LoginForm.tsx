import type React from "react"
import { useState } from "react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { DocumentTypeOption } from "@/types/auth"
import HeaderLogin from "./HeaderLogin"

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
  const [showPassword, setShowPassword] = useState(false)

  return (
    <>
      <HeaderLogin
        userTypes={userTypes}
        selectedUserType={selectedUserType}
        onBack={onBack}
      />

      <form onSubmit={onSubmit} className="space-y-3">
        <div className="w-full max-w-sm mx-auto">
          <div className="w-full pt-3">
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

          <div className="w-full pt-3">
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

          <div className="w-full pt-3 pb-4">
            <Label htmlFor="password" className="text-sm font-medium text-gray-700">
              Clave
            </Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                value={password}
                onChange={(e) => onPasswordChange(e.target.value)}
                placeholder="Ingresa tu clave"
                className="in-b-form mt-1 pr-10"
                required
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute inset-y-0 right-0 flex items-center pr-3 mt-1 text-gray-500 hover:text-gray-700"
                aria-label={showPassword ? "Ocultar clave" : "Mostrar clave"}
              >
                {showPassword ? (
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                ) : (
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                )}
              </button>
            </div>
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
      </div>
    </>
  )
}

export default LoginForm
