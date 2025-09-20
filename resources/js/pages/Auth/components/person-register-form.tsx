import React, { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ChevronLeft } from "lucide-react"
import type { DocumentTypeOption } from "@/types/auth"
import type { RegisterValues } from "@/types/register"

// Componente especializado para registro de personas (2 pasos)
// Paso 1: Datos personales, Paso 2: Datos de sesión

type Props = {
  subtitle?: string
  userTypeLabel: string
  values: RegisterValues
  errors: Record<string, string>
  isSubmitting: boolean
  documentTypes: DocumentTypeOption[]
  cityOptions: DocumentTypeOption[]
  onBack: () => void
  onChange: (field: keyof RegisterValues, value: string) => void
  onSubmit: (e: React.FormEvent) => void
  step?: number
  onNextStep?: () => void
  onPrevStep?: () => void
  firstNameRef?: React.Ref<HTMLInputElement>
  lastNameRef?: React.Ref<HTMLInputElement>
  emailRef?: React.Ref<HTMLInputElement>
  phoneRef?: React.Ref<HTMLInputElement>
  identificationRef?: React.Ref<HTMLInputElement>
  passwordRef?: React.Ref<HTMLInputElement>
  confirmPasswordRef?: React.Ref<HTMLInputElement>
}

export default function PersonRegisterForm({
  subtitle,
  userTypeLabel,
  values,
  errors,
  isSubmitting,
  documentTypes,
  cityOptions,
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
}: Props){
  // Pista visual de contraseña: requisitos básicos
  const pwd = values.password || ""
  const pwdReqs = {
    length: pwd.length >= 10,
    upper: /[A-Z]/.test(pwd),
    number: /\d/.test(pwd),
    symbol: /[^A-Za-z0-9]/.test(pwd),
  }

  const [showPassword, setShowPassword] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)

  const suggestStrongPassword = () => {
    const uppers = "ABCDEFGHJKLMNPQRSTUVWXYZ"
    const lowers = "abcdefghijkmnopqrstuvwxyz"
    const numbers = "23456789"
    const symbols = "!@#$%^&*()-_=+[]{};:,.?";
    const pick = (set: string, n: number) => Array.from({ length: n }, () => set[Math.floor(Math.random() * set.length)]).join("")
    let base = pick(uppers, 2) + pick(numbers, 2) + pick(symbols, 2) + pick(lowers, 6)
    const arr = base.split("")
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]]
    }
    const suggestion = arr.join("")
    onChange("password", suggestion)
    onChange("confirmPassword", suggestion)
    setShowPassword(true)
    setShowConfirm(true)
  }

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

      <form onSubmit={onSubmit} className="space-y-3">
        {/* Paso 1: Datos personales */}
        {step === 1 && (
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
                  placeholder="Nombre"
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
                  placeholder="Apellido"
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
                placeholder="Correo"
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
                placeholder="Teléfono"
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
            <div className="flex gap-3 mt-4">
              <Button type="button" variant="secondary" onClick={onPrevStep}>
                Volver
              </Button>
              <Button type="button" onClick={onNextStep} className="flex-1">
                Siguiente: Datos de sesión
              </Button>
            </div>
          </>
        )}

        {/* Paso 2: Datos sesión */}
        {step === 2 && (
          <>
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
                <div className="relative">
                  <Input
                    id="password"
                    ref={passwordRef}
                    type={showPassword ? "text" : "password"}
                    value={values.password}
                    onChange={(e) => onChange("password", e.target.value)}
                    placeholder="Mínimo 10 caracteres, 1 mayúscula, 1 número y 1 símbolo"
                    className={`in-b-form mt-1 pr-20 ${errors.password ? "border-red-500" : ""}`}
                    aria-invalid={!!errors.password}
                    aria-describedby={errors.password ? "password-error" : undefined}
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword((v) => !v)}
                    className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-600 hover:text-gray-800 bg-white/60 border border-gray-200 px-2 py-1 rounded"
                    aria-label={showPassword ? "Ocultar contraseña" : "Mostrar contraseña"}
                  >
                    {showPassword ? "Ocultar" : "Mostrar"}
                  </button>
                </div>
                {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password}</p>}
              </div>
              <div>
                <Label htmlFor="confirmPassword" className="text-sm font-medium text-gray-700">
                  Confirmar *
                </Label>
                <div className="relative">
                  <Input
                    id="confirmPassword"
                    ref={confirmPasswordRef}
                    type={showConfirm ? "text" : "password"}
                    value={values.confirmPassword}
                    onChange={(e) => onChange("confirmPassword", e.target.value)}
                    placeholder="Repite la contraseña"
                    className={`in-b-form mt-1 pr-20 ${errors.confirmPassword ? "border-red-500" : ""}`}
                    aria-invalid={!!errors.confirmPassword}
                    aria-describedby={errors.confirmPassword ? "confirmPassword-error" : undefined}
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirm((v) => !v)}
                    className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-600 hover:text-gray-800 bg-white/60 border border-gray-200 px-2 py-1 rounded"
                    aria-label={showConfirm ? "Ocultar confirmación" : "Mostrar confirmación"}
                  >
                    {showConfirm ? "Ocultar" : "Mostrar"}
                  </button>
                </div>
                {errors.confirmPassword && <p className="text-red-500 text-xs mt-1">{errors.confirmPassword}</p>}
              </div>

              {/* Pista visual compacta */}
              <div className="w-full">
                <div className="mt-1 grid grid-cols-2 gap-x-4 gap-y-0.5 text-[10px] leading-tight">
                  <div className={pwdReqs.length ? "text-emerald-600" : "text-gray-500"}>
                    {pwdReqs.length ? "✔" : "•"} 10+ caracteres
                  </div>
                  <div className={pwdReqs.upper ? "text-emerald-600" : "text-gray-500"}>
                    {pwdReqs.upper ? "✔" : "•"} 1 mayúscula
                  </div>
                  <div className={pwdReqs.number ? "text-emerald-600" : "text-gray-500"}>
                    {pwdReqs.number ? "✔" : "•"} 1 número
                  </div>
                  <div className={pwdReqs.symbol ? "text-emerald-600" : "text-gray-500"}>
                    {pwdReqs.symbol ? "✔" : "•"} 1 símbolo
                  </div>
                </div>
                <div className="mt-1 text-[11px] text-gray-500 mt-4">
                  <span className="truncate">Sugerencia: usa una frase con símbolos y números.</span>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={suggestStrongPassword}
                    className="h-7 mt-2 px-2 py-0.5 text-[11px] border-gray-300 text-gray-700 hover:text-gray-900 text-white"
                  >
                    Sugerir
                  </Button>
                </div>
              </div>
            </div>

            <div className="flex gap-3 mt-4">
              <Button type="button" variant="secondary" onClick={onPrevStep}>
                Volver
              </Button>
              <Button
                type="submit"
                disabled={isSubmitting}
                className="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg"
              >
                {isSubmitting ? "Registrando..." : "Crear cuenta"}
              </Button>
            </div>
          </>
        )}
      </form>
    </>
  )
}
