import React, { useEffect, useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { DocumentTypeOption } from "@/types/auth"
import type { 
  PropsCompanyRegisterForm, 
  DataCompany, 
  DataRepresentative, 
  DataDelegado, 
} from "@/types/register.d"
import SessionRegister from "./session-register"
import HeaderRegister from "./header-register"

const DataCompanyRegister: React.FC<DataCompany> = ( 
  {
    values,
    categoryOptions,
    documentTypes,
    societyOptions,
    errors,
    onChange,
    onNextStep,
    isJuridicaRepresentative,
    companyNameRef,
    companyNitRef,
    addressRef,
  }
) => {
return (
    <>
      <div className="flex justify-between gap-4">
        <div className="w-60">
          <Label htmlFor="companyCategory" className="text-sm font-medium text-gray-700">
            Tipo persona *
          </Label>
          <Select value={values.companyCategory} onValueChange={(v) => onChange("companyCategory", v)}>
            <SelectTrigger className={`in-b-form mt-1 ${errors.companyCategory ? "border-red-500" : ""}`}>
              <SelectValue placeholder="Selecciona tipo" />
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
        <div className="w-100">
          <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
            Tipo de documento empresa *
          </Label>
          <Select 
            value={values.documentType} 
            onValueChange={(v) => onChange("documentType", v)} 
            disabled={isJuridicaRepresentative}
            >
            <SelectTrigger 
              className={`in-b-form mt-1 ${errors.documentType ? "border-red-500" : ""} ${isJuridicaRepresentative ? 'bg-gray-50 text-gray-600' : ''}`}>
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
      </div>
      <div>
        <Label htmlFor="companyName" className="text-sm font-medium text-gray-700">
          Razón social *
        </Label>
        <Input
          id="companyName"
          ref={companyNameRef}
          type="text"
          value={values.companyName}
          onChange={(e) => onChange("companyName", e.target.value)}
          placeholder="Razón social de tu empresa"
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
      <Button type="button" className="w-full mt-4" onClick={onNextStep}>
        Siguiente: Datos representante
      </Button>
    </>
  )
}

const DataRepresentaRegister: React.FC<DataRepresentative> = ({
  values,
  errors,
  onChange,
  onNextStep,
  isJuridica,
  isNatural,
  onPrevStep,
  cityOptions,
  firstNameRef,
  lastNameRef,
  emailRef,
  phoneRef,
})=> {
  return ( 
  <>
    <div>
      <Label htmlFor="userRole" className="text-sm font-medium text-gray-700">
        ¿Eres representante o delegado? *
      </Label>
      <Select value={values.userRole} onValueChange={(v) => onChange("userRole", v)} disabled={isNatural}>
        <SelectTrigger className={`in-b-form mt-1 ${errors.userRole ? "border-red-500" : ""} ${isNatural ? 'bg-gray-50 text-gray-600' : ''}`}>
          <SelectValue placeholder="Selecciona" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="representante">Representante legal</SelectItem>
          {isJuridica && (
            <SelectItem value="delegado">Delegado de la empresa</SelectItem>
          )}
        </SelectContent>
      </Select>
      {errors.userRole && <p className="text-red-500 text-xs mt-1">{errors.userRole}</p>}
    </div>
    {values.userRole === 'delegado' && (
      <div>
        <Label htmlFor="position" className="text-sm font-medium text-gray-700">
          Cargo u ocupación dentro de la empresa *
        </Label>
        <Input
          id="position"
          type="text"
          value={values.position}
          onChange={(e) => onChange("position", e.target.value)}
          placeholder="Ej: Coordinador de Talento Humano"
          className={`in-b-form mt-1 ${errors.position ? "border-red-500" : ""}`}
        />
        {errors.position && <p className="text-red-500 text-xs mt-1">{errors.position}</p>}
      </div>
    )}
    <div className="grid grid-cols-2 gap-4">
      <div>
        <Label htmlFor="firstName" className="text-sm font-medium text-gray-700">
          {values.userRole === 'delegado' ? 'Nombre delegado *' : 'Nombre representante *'}
        </Label>
        <Input
          id="firstName"
          ref={firstNameRef}
          type="text"
          value={values.firstName}
          onChange={(e) => onChange("firstName", e.target.value)}
          placeholder={values.userRole === 'delegado' ? 'Nombre delegado' : 'Nombre representante'}
          className={`in-b-form mt-1 ${errors.firstName ? "border-red-500" : ""}`}
        />
        {errors.firstName && <p className="text-red-500 text-xs mt-1">{errors.firstName}</p>}
      </div>
      <div>
        <Label htmlFor="lastName" className="text-sm font-medium text-gray-700">
          {values.userRole === 'delegado' ? 'Apellido delegado *' : 'Apellido representante *'}
        </Label>
        <Input
          id="lastName"
          ref={lastNameRef}
          type="text"
          value={values.lastName}
          onChange={(e) => onChange("lastName", e.target.value)}
          placeholder={values.userRole === 'delegado' ? 'Apellido delegado' : 'Apellido representante'}
          className={`in-b-form mt-1 ${errors.lastName ? "border-red-500" : ""}`}
        />
        {errors.lastName && <p className="text-red-500 text-xs mt-1">{errors.lastName}</p>}
      </div>
    </div>
    <div>
      <Label htmlFor="email" className="text-sm font-medium text-gray-700">
        {values.userRole === 'delegado' ? 'Email delegado *' : 'Email representante *'}
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
    <div className="grid grid-cols-2 gap-4">
      <div>
        <Label htmlFor="phone" className="text-sm font-medium text-gray-700">
          {values.userRole === 'delegado' ? 'Teléfono delegado' : 'Teléfono representante'}
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
          Ciudad laboral
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
    </div>
    <div className="flex gap-3 mt-4">
      <Button type="button" variant="secondary" onClick={onPrevStep}>
        Volver a datos de empresa
      </Button>
      <Button type="button" onClick={onNextStep} className="flex-1">
        {values.userRole === 'delegado' ? 'Siguiente: Datos del representante' : 'Siguiente: Datos de sesión'}
      </Button>
    </div>
    </>
  )
}

const DataDelegadoRegister: React.FC<DataDelegado> = ({
  values,
  errors,
  onChange,
  onNextStep,
  onPrevStep
}) => {
  return (
    <>
      <div className="grid grid-cols-2 gap-4">
        <div className="col-span-2">
          <Label htmlFor="repName" className="text-sm font-medium text-gray-700">Nombre del representante *</Label>
          <Input
            id="repName"
            type="text"
            value={values.repName}
            onChange={(e) => onChange("repName", e.target.value)}
            placeholder="Nombre y apellido del representante"
            className={`in-b-form mt-1 ${errors.repName ? 'border-red-500' : ''}`}
          />
          {errors.repName && <p className="text-red-500 text-xs mt-1">{errors.repName}</p>}
        </div>
        <div>
          <Label htmlFor="repIdentification" className="text-sm font-medium text-gray-700">Identificación *</Label>
          <Input
            id="repIdentification"
            type="text"
            value={values.repIdentification}
            onChange={(e) => onChange("repIdentification", e.target.value)}
            placeholder="Número de documento"
            className={`in-b-form mt-1 ${errors.repIdentification ? 'border-red-500' : ''}`}
          />
          {errors.repIdentification && <p className="text-red-500 text-xs mt-1">{errors.repIdentification}</p>}
        </div>
        <div>
          <Label htmlFor="repEmail" className="text-sm font-medium text-gray-700">Email *</Label>
          <Input
            id="repEmail"
            type="email"
            value={values.repEmail}
            onChange={(e) => onChange("repEmail", e.target.value)}
            placeholder="Correo del representante"
            className={`in-b-form mt-1 ${errors.repEmail ? 'border-red-500' : ''}`}
          />
          {errors.repEmail && <p className="text-red-500 text-xs mt-1">{errors.repEmail}</p>}
        </div>
        <div>
          <Label htmlFor="repPhone" className="text-sm font-medium text-gray-700">Teléfono *</Label>
          <Input
            id="repPhone"
            type="tel"
            value={values.repPhone}
            onChange={(e) => onChange("repPhone", e.target.value)}
            placeholder="Teléfono personal"
            className={`in-b-form mt-1 ${errors.repPhone ? 'border-red-500' : ''}`}
          />
          {errors.repPhone && <p className="text-red-500 text-xs mt-1">{errors.repPhone}</p>}
        </div>
      </div>
      <div className="flex gap-3 mt-4">
        <Button type="button" variant="secondary" onClick={onPrevStep}>Volver</Button>
        <Button type="button" onClick={onNextStep} className="flex-1">Siguiente: Datos de sesión</Button>
      </div>
    </>
  )
}

export default function CompanyRegisterForm({
  subtitle,
  userTypeLabel,
  values,
  errors,
  isSubmitting,
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
}: PropsCompanyRegisterForm){
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
    const base = pick(uppers, 2) + pick(numbers, 2) + pick(symbols, 2) + pick(lowers, 6)
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

  // --- Reglas de documento según categoría de empresa ---
  // Persona Natural (N): todos menos NIT
  // Persona Jurídica (J): solo NIT y forzar selección
  const isNatural = values.companyCategory === 'N'
  const isJuridica = values.companyCategory === 'J'
  const isDelegate = values.userRole === 'delegado'
  const isJuridicaRepresentative = isJuridica && !isDelegate
  const isJuridicaDelegate = isJuridica && isDelegate

  const isNitOption = (opt: DocumentTypeOption) =>
    opt.label.toLowerCase().includes('nit') || opt.value.toLowerCase() === 'nit'

  const filteredDocumentTypes = (documentTypes || []).filter((opt) =>
    // Jurídica + representante: solo NIT
    isJuridicaRepresentative ? isNitOption(opt)
    // Jurídica + delegado o Natural: todo menos NIT
    : !isNitOption(opt)
  )

  // Forzar selección cuando es Jurídica y limpiar cuando Natural tenga NIT
  useEffect(() => {
    if (isJuridicaRepresentative) {
      // Forzar NIT
      const nit = (documentTypes || []).find(isNitOption)
      if (nit && values.documentType !== nit.value) {
        onChange('documentType', nit.value)
      }
    } else if (isNatural || isJuridicaDelegate) {
      // Limpiar si quedó NIT seleccionado
      const isNitSelected = (documentTypes || []).some(
        (o) => isNitOption(o) && o.value === values.documentType
      )
      if (isNitSelected) {
        onChange('documentType', '')
      }
      // Si es persona natural, no puede haber delegado: forzar representante
      if (isNatural && values.userRole !== 'representante') {
        onChange('userRole', 'representante')
      }
    }
    // Solo dependencias necesarias para evitar bucles
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [values.companyCategory, values.userRole])

  return (
    <>
      <HeaderRegister
        subtitle={subtitle}
        userTypeLabel={userTypeLabel}
        onBack={onBack}
      />

      <form onSubmit={onSubmit} className="space-y-3">
        {/* Paso 1: Datos empresa */}
        {step === 1 && (
          <DataCompanyRegister 
            values={values}
            categoryOptions={categoryOptions}
            documentTypes={documentTypes}
            societyOptions={societyOptions}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            isJuridicaRepresentative={isJuridicaRepresentative}
            companyNameRef={companyNameRef}
            companyNitRef={companyNitRef}
            addressRef={addressRef}
          />
        )}

        {/* Paso 2: Datos representante */}
        {step === 2 && (
          <DataRepresentaRegister 
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            isJuridica={isJuridica}
            isNatural={isNatural}
            onPrevStep={onPrevStep}
            cityOptions={cityOptions}
            firstNameRef={firstNameRef}
            lastNameRef={lastNameRef}
            emailRef={emailRef}
            phoneRef={phoneRef}
          />
        )}

        {/* Paso 3 (solo delegado): Datos del representante */}
        {step === 3 && values.userRole === 'delegado' && (
          <DataDelegadoRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            onPrevStep={onPrevStep}
          />
        )}

        {/* Paso sesión: paso 3 (no delegado) o paso 4 (delegado) */}
        {((values.userRole !== 'delegado' && step === 3) || (values.userRole === 'delegado' && step === 4)) && (
          <SessionRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onPrevStep={onPrevStep}
            isJuridicaRepresentative={isJuridicaRepresentative}
            filteredDocumentTypes={filteredDocumentTypes}
            identificationRef={identificationRef}
            passwordRef={passwordRef}
            showPassword={showPassword}
            setShowPassword={setShowPassword}
            confirmPasswordRef={confirmPasswordRef}
            showConfirm={showConfirm}
            setShowConfirm={setShowConfirm}
            pwdReqs={pwdReqs}
            suggestStrongPassword={suggestStrongPassword}
            isSubmitting={isSubmitting}
          />
        )}
      </form>
    </>
  )
}
