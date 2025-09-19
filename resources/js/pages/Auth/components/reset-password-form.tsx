import type React from "react"
import { ChevronLeft, Mail } from "lucide-react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { userTypes, documentTypes } from "@/constants/auth"
import type { UserType } from "@/types/auth"

// Tipo mínimo del estado necesario en este formulario
type ResetFormState = {
  documentType: string
  identification: string
  email: string
  errors: Record<string, string>
  isSubmitting: boolean
}

interface Props {
  selectedUserType: UserType
  formState: ResetFormState
  onBack: () => void
  onFieldChange: (field: "documentType" | "identification" | "email", value: string) => void
  onSubmit: (e: React.FormEvent) => void
  documentTypeRef: React.RefObject<HTMLButtonElement | null>
  identificationRef: React.RefObject<HTMLInputElement | null>
  emailRef: React.RefObject<HTMLInputElement | null>
  loginHref: string
}

// Componente presentacional del formulario de "Recuperar clave"
// SRP: renderiza UI; la lógica y estado se manejan en el padre
const ResetPasswordForm: React.FC<Props> = ({
  selectedUserType,
  formState,
  onBack,
  onFieldChange,
  onSubmit,
  documentTypeRef,
  identificationRef,
  emailRef,
  loginHref,
}) => {
  const selectedLabel = userTypes.find((ut) => ut.id === selectedUserType)?.label

  return (
    <>
      <div className="flex items-center mb-6">
        <button onClick={onBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
          <ChevronLeft className="w-5 h-5 text-gray-600" />
        </button>
        <div>
          <h2 className="text-xl font-semibold text-gray-800">{selectedLabel}</h2>
          <p className="text-sm text-gray-600">Recuperar clave</p>
        </div>
      </div>

      <form onSubmit={onSubmit} className="space-y-6">
        <div>
          <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
            Tipo de documento *
          </Label>
          <Select
            value={formState.documentType}
            onValueChange={(value) => onFieldChange("documentType", value)}
          >
            <SelectTrigger
              ref={documentTypeRef}
              className={`in-b-form mt-1 ${formState.errors.documentType ? "border-red-500" : ""}`}
            >
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
          {formState.errors.documentType && (
            <p className="text-red-500 text-xs mt-1">{formState.errors.documentType}</p>
          )}
        </div>

        <div>
          <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
            Número de identificación *
          </Label>
          <Input
            ref={identificationRef}
            id="identification"
            type="text"
            value={formState.identification}
            onChange={(e) => onFieldChange("identification", e.target.value)}
            placeholder="Ingresa tu número de identificación"
            className={`in-b-form mt-1 ${formState.errors.identification ? "border-red-500" : ""}`}
          />
          {formState.errors.identification && (
            <p className="text-red-500 text-xs mt-1">{formState.errors.identification}</p>
          )}
        </div>

        <div>
          <Label htmlFor="email" className="text-sm font-medium text-gray-700">
            Correo electrónico *
          </Label>
          <Input
            ref={emailRef}
            id="email"
            type="email"
            value={formState.email}
            onChange={(e) => onFieldChange("email", e.target.value)}
            placeholder="Ingresa tu correo electrónico"
            className={`in-b-form mt-1 ${formState.errors.email ? "border-red-500" : ""}`}
          />
          {formState.errors.email && (
            <p className="text-red-500 text-xs mt-1">{formState.errors.email}</p>
          )}
        </div>

        <Button
          type="submit"
          className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg disabled:opacity-50"
          disabled={formState.isSubmitting}
        >
          {formState.isSubmitting ? (
            <div className="flex items-center justify-center">
              <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
              Enviando...
            </div>
          ) : (
            <div className="flex items-center justify-center">
              <Mail className="w-4 h-4 mr-2" />
              Enviar instrucciones
            </div>
          )}
        </Button>
      </form>

      <div className="flex justify-center text-sm mt-6">
        <TextLink href={loginHref} className="text-gray-500 hover:text-emerald-600 flex items-center">
          <ChevronLeft className="w-4 h-4 mr-1" />
          Volver al inicio de sesión
        </TextLink>
      </div>
    </>
  )
}

export default ResetPasswordForm
