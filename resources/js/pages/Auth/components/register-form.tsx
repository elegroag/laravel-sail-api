import type React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ChevronLeft } from "lucide-react"
import { DocumentTypeOption } from "@/types/auth"

// Formulario de registro reutilizable con soporte para pasos
// Si userTypeLabel es "empresa", el registro es en 2 pasos: primero datos de empresa, luego representante

export type RegisterValues = {
  documentType: string
  identification: string
  firstName: string
  lastName: string
  email: string
  phone: string
  password: string
  confirmPassword: string
  companyName: string
  companyNit: string
  address: string
  city: string
  societyType: string
  companyCategory: string
}

type RegisterFormProps = {
  subtitle?: string
  userTypeLabel: string
  values: RegisterValues
  errors: Record<string, string>
  isSubmitting: boolean
  isCompanyType: boolean
  documentTypes: DocumentTypeOption[]
  societyOptions: DocumentTypeOption[]
  cityOptions: DocumentTypeOption[]
  categoryOptions: DocumentTypeOption[]
  onBack: () => void
  onChange: (field: keyof RegisterValues, value: string) => void
  onSubmit: (e: React.FormEvent) => void
  // Nuevo: paso actual y handlers para navegación
  step?: number
  onNextStep?: () => void
  onPrevStep?: () => void
  // Refs opcionales para manejo de foco desde el padre
  firstNameRef?: React.Ref<HTMLInputElement>
  lastNameRef?: React.Ref<HTMLInputElement>
  emailRef?: React.Ref<HTMLInputElement>
  phoneRef?: React.Ref<HTMLInputElement>
  identificationRef?: React.Ref<HTMLInputElement>
  passwordRef?: React.Ref<HTMLInputElement>
  confirmPasswordRef?: React.Ref<HTMLInputElement>
  companyNameRef?: React.Ref<HTMLInputElement>
  companyNitRef?: React.Ref<HTMLInputElement>
  addressRef?: React.Ref<HTMLInputElement>
}

export default function RegisterForm({
  subtitle,
  userTypeLabel,
  values,
  errors,
  isSubmitting,
  isCompanyType,
  documentTypes,
  societyOptions,
  cityOptions,
  categoryOptions,
  onBack,
  onChange,
  onSubmit,
  step = 1,
  onNextStep,
  onPrevStep,
  firstNameRef,
  lastNameRef,
  emailRef,
  phoneRef,
  identificationRef,
  passwordRef,
  confirmPasswordRef,
  companyNameRef,
  companyNitRef,
  addressRef,
}: RegisterFormProps) {

  return (
    <>
      <div className="flex items-center mb-6">
        <button onClick={onBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors" type="button">
          <ChevronLeft className="w-5 h-5 text-gray-600" />
        </button>
        <div>
          <h2 className="text-xl font-semibold text-gray-800">{userTypeLabel}</h2>
          <p className="text-sm text-gray-600">{subtitle ?? "Completa tu información"}</p>
        </div>
      </div>

      <form onSubmit={onSubmit} className="space-y-4">
        {/* Paso 1: Datos empresa */}
        {isCompanyType && step === 1 && (
          <>
            <div>
              <Label htmlFor="companyName" className="text-sm font-medium text-gray-700">
                Nombre de la empresa *
              </Label>
              <Input
                id="companyName"
                ref={companyNameRef}
                type="text"
                value={values.companyName}
                onChange={(e) => onChange("companyName", e.target.value)}
                placeholder="Nombre de tu empresa"
                className={`in-b-form mt-1 ${errors.companyName ? "border-red-500" : ""}`}
              />
              {errors.companyName && <p className="text-red-500 text-xs mt-1">{errors.companyName}</p>}
            </div>
            <div>
              <Label htmlFor="companyNit" className="text-sm font-medium text-gray-700">
                NIT de la empresa *
              </Label>
              <Input
                id="companyNit"
                ref={companyNitRef}
                type="number"
                value={values.companyNit}
                onChange={(e) => onChange("companyNit", e.target.value)}
                placeholder="NIT de la empresa"
                className={`in-b-form mt-1 ${errors.companyNit ? "border-red-500" : ""}`}
              />
              {errors.companyNit && <p className="text-red-500 text-xs mt-1">{errors.companyNit}</p>}
            </div>
            <div>
              <Label htmlFor="societyType" className="text-sm font-medium text-gray-700">
                Tipo de sociedad *
              </Label>
              <Select value={values.societyType} onValueChange={(v) => onChange("societyType", v)}>
                <SelectTrigger className={`in-b-form mt-1 ${errors.societyType ? "border-red-500" : ""}`}>
                  <SelectValue placeholder="Selecciona el tipo de sociedad" />
                </SelectTrigger>
                <SelectContent>
                  {societyOptions.map((opt) => (
                    <SelectItem key={opt.value} value={opt.value}>
                      {opt.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {errors.societyType && <p className="text-red-500 text-xs mt-1">{errors.societyType}</p>}
            </div>
            <div>
              <Label htmlFor="companyCategory" className="text-sm font-medium text-gray-700">
                Tipo persona comercial *
              </Label>
              <Select value={values.companyCategory} onValueChange={(v) => onChange("companyCategory", v)}>
                <SelectTrigger className={`in-b-form mt-1 ${errors.companyCategory ? "border-red-500" : ""}`}>
                  <SelectValue placeholder="Selecciona la categoría" />
                </SelectTrigger>
                <SelectContent>
                  {categoryOptions.map((opt) => (
                    <SelectItem key={opt.value} value={opt.value}>
                      {opt.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {errors.companyCategory && <p className="text-red-500 text-xs mt-1">{errors.companyCategory}</p>}
            </div>
            <div>
              <Label htmlFor="address" className="text-sm font-medium text-gray-700">
                Dirección
              </Label>
              <Input
                id="address"
                ref={addressRef}
                type="text"
                value={values.address}
                onChange={(e) => onChange("address", e.target.value)}
                placeholder="Dirección empresa"
                className="in-b-form mt-1"
              />
            </div>
            {!isCompanyType && (
              <div>
                <Label htmlFor="city" className="text-sm font-medium text-gray-700">
                  Ciudad
                </Label>
                <Select value={values.city} onValueChange={(v) => onChange("city", v)}>
                  <SelectTrigger className={`in-b-form mt-1 ${errors.city ? "border-red-500" : ""}`}>
                    <SelectValue placeholder="Selecciona la ciudad" />
                  </SelectTrigger>
                  <SelectContent>
                    {cityOptions.map((city) => (
                      <SelectItem key={city.value} value={city.value}>
                        {city.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.city && <p className="text-red-500 text-xs mt-1">{errors.city}</p>}
              </div>
            )}
            <Button type="button" className="w-full mt-4" onClick={onNextStep}>
              Siguiente: Datos representante
            </Button>
          </>
        )}

        {/* Paso 2: Datos representante/persona delegada */}
        {(!isCompanyType || step === 2) && (
          <>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <Label htmlFor="firstName" className="text-sm font-medium text-gray-700">
                  Nombre *
                </Label>
                <Input
                  id="firstName"
                  ref={firstNameRef}
                  type="text"
                  value={values.firstName}
                  onChange={(e) => onChange("firstName", e.target.value)}
                  placeholder="Nombre representante"
                  className={`in-b-form mt-1 ${errors.firstName ? "border-red-500" : ""}`}
                />
                {errors.firstName && <p className="text-red-500 text-xs mt-1">{errors.firstName}</p>}
              </div>
              <div>
                <Label htmlFor="lastName" className="text-sm font-medium text-gray-700">
                  Apellido *
                </Label>
                <Input
                  id="lastName"
                  ref={lastNameRef}
                  type="text"
                  value={values.lastName}
                  onChange={(e) => onChange("lastName", e.target.value)}
                  placeholder="Apellido representante"
                  className={`in-b-form mt-1 ${errors.lastName ? "border-red-500" : ""}`}
                />
                {errors.lastName && <p className="text-red-500 text-xs mt-1">{errors.lastName}</p>}
              </div>
            </div>
            <div>
              <Label htmlFor="email" className="text-sm font-medium text-gray-700">
                Email *
              </Label>
              <Input
                id="email"
                ref={emailRef}
                type="email"
                value={values.email}
                onChange={(e) => onChange("email", e.target.value)}
                placeholder="Correo representante"
                className={`in-b-form mt-1 ${errors.email ? "border-red-500" : ""}`}
              />
              {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
            </div>
            <div>
              <Label htmlFor="phone" className="text-sm font-medium text-gray-700">
                Teléfono
              </Label>
              <Input
                id="phone"
                ref={phoneRef}
                type="tel"
                value={values.phone}
                onChange={(e) => onChange("phone", e.target.value)}
                placeholder="Teléfono representante"
                className="in-b-form mt-1"
              />
            </div>
            <div>
              <Label htmlFor="city" className="text-sm font-medium text-gray-700">
                Ciudad
              </Label>
              <Select value={values.city} onValueChange={(v) => onChange("city", v)}>
                <SelectTrigger className={`in-b-form mt-1 ${errors.city ? "border-red-500" : ""}`}>
                  <SelectValue placeholder="Selecciona la ciudad" />
                </SelectTrigger>
                <SelectContent>
                  {cityOptions.map((city) => (
                    <SelectItem key={city.value} value={city.value}>
                      {city.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {errors.city && <p className="text-red-500 text-xs mt-1">{errors.city}</p>}
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
                  Tipo de documento *
                </Label>
                <Select value={values.documentType} onValueChange={(v) => onChange("documentType", v)}>
                  <SelectTrigger className={`in-b-form mt-1 ${errors.documentType ? "border-red-500" : ""}`}>
                    <SelectValue placeholder="Selecciona" />
                  </SelectTrigger>
                  <SelectContent>
                    {documentTypes.map((doc) => (
                      <SelectItem key={doc.value} value={doc.value}>
                        {doc.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                {errors.documentType && <p className="text-red-500 text-xs mt-1">{errors.documentType}</p>}
              </div>
              <div>
                <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
                  Número *
                </Label>
                <Input
                  id="identification"
                  ref={identificationRef}
                  type="number"
                  value={values.identification}
                  onChange={(e) => onChange("identification", e.target.value)}
                  placeholder="Número de documento"
                  className={`in-b-form mt-1 ${errors.identification ? "border-red-500" : ""}`}
                />
                {errors.identification && <p className="text-red-500 text-xs mt-1">{errors.identification}</p>}
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <Label htmlFor="password" className="text-sm font-medium text-gray-700">
                  Contraseña *
                </Label>
                <Input
                  id="password"
                  ref={passwordRef}
                  type="password"
                  value={values.password}
                  onChange={(e) => onChange("password", e.target.value)}
                  placeholder="Mínimo 6 caracteres"
                  className={`in-b-form mt-1 ${errors.password ? "border-red-500" : ""}`}
                />
                {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password}</p>}
              </div>
              <div>
                <Label htmlFor="confirmPassword" className="text-sm font-medium text-gray-700">
                  Confirmar *
                </Label>
                <Input
                  id="confirmPassword"
                  ref={confirmPasswordRef}
                  type="password"
                  value={values.confirmPassword}
                  onChange={(e) => onChange("confirmPassword", e.target.value)}
                  placeholder="Repite la contraseña"
                  className={`in-b-form mt-1 ${errors.confirmPassword ? "border-red-500" : ""}`}
                />
                {errors.confirmPassword && <p className="text-red-500 text-xs mt-1">{errors.confirmPassword}</p>}
              </div>
            </div>
            {isCompanyType && (
              <Button type="button" className="w-full mt-4" onClick={onPrevStep}>
                Volver a datos de empresa
              </Button>
            )}
            <Button
              type="submit"
              disabled={isSubmitting}
              className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg mt-6"
            >
              {isSubmitting ? "Registrando..." : "Crear cuenta"}
            </Button>
          </>
        )}
      </form>
    </>
  )
}
