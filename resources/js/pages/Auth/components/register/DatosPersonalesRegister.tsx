import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { AlertCircle, ArrowLeft, Building2, Loader2 } from 'lucide-react'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import type { 
  PropsPersonRegisterForm, 
  DataPersonRegister, 
  DataEmpresaRegister
} from "@/types/register.d"
import SessionRegister from "./SessionRegister"
import HeaderRegister from "./HeaderRegister"



const DatosPersonalesRegister: React.FC<DataPersonRegister> = ({
  values,
  errors,
  onChange,
  onNextStep,
  firstNameRef,
  lastNameRef,
  emailRef,
  phoneRef,
  identificationRef,
  documentTypes = [],
  cityOptions,
  isIndependentType,
  isPensionerType,
  isWorkerType,
  onBack
}) => {
  return (
    <>
    {isWorkerType && (
      <div className="grid grid-cols-2 gap-4">
        <div>
          <Label htmlFor="documentTypeUser" className="text-sm font-medium text-gray-700">
            Tipo de documento *
          </Label>
          <Select
            key={`doc-user-${values.documentTypeUser || 'empty'}`}
            value={values.documentTypeUser || undefined}
            onValueChange={(v) => onChange("documentTypeUser", v)}
          >
            <SelectTrigger className={`in-b-form mt-1 ${errors.documentTypeUser ? "border-red-500" : ""}`}>
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
          {errors.documentTypeUser && <p className="text-red-500 text-xs mt-1">{errors.documentTypeUser}</p>}
        </div>
        <div>
          <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
            Número de documento *
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
    )}
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
        Celular
      </Label>
      <Input
        id="phone"
        ref={phoneRef}
        type="number"
        value={values.phone}
        onChange={(e) => onChange("phone", e.target.value)}
        placeholder="Celular"
        className="in-b-form mt-1"
      />
    </div>
    <div>
      <Label htmlFor="city" className="text-sm font-medium text-gray-700">
        Ciudad
      </Label>
      <Select value={values.city || undefined} onValueChange={(v) => onChange("city", v)}>
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
    {(isIndependentType || isPensionerType) && (
      <div>
        <Label className="text-sm font-medium text-gray-700">
          {isIndependentType ? 'Contribución (Independiente)' : 'Contribución (Pensionado)'} *
        </Label>
        <RadioGroup
          value={values.contributionRate}
          onValueChange={(v: string) => onChange('contributionRate', v)}
          className="mt-2 gap-3"
        >
          {isIndependentType && (
            <div className="flex flex-col gap-2 text-sm">
              <label className="inline-flex items-center gap-2 text-gray-800 select-none">
                <RadioGroupItem value="2" />
                <span>2%</span>
              </label>
              <label className="inline-flex items-center gap-2 text-gray-800 select-none">
                <RadioGroupItem value="0.6" />
                <span>0.6%</span>
              </label>
            </div>
          )}
          {isPensionerType && (
            <div className="flex flex-col gap-2 text-sm">
              <label className="inline-flex items-center gap-2 text-gray-800 select-none">
                <RadioGroupItem value="0" />
                <span>0%</span>
              </label>
              <label className="inline-flex items-center gap-2 text-gray-800 select-none">
                <RadioGroupItem value="2" />
                <span>2%</span>
              </label>
              <label className="inline-flex items-center gap-2 text-gray-800 select-none">
                <RadioGroupItem value="0.6" />
                <span>0.6%</span>
              </label>
            </div>
          )}
        </RadioGroup>
        {errors.contributionRate && (
          <p className="text-red-500 text-xs mt-1">{errors.contributionRate}</p>
        )}
      </div>
    )}
    <div className="flex gap-3 mt-4">
      <Button type="button" variant="secondary" onClick={onBack} className="bg-purple-100 hover:bg-purple-200 text-purple-900 border-purple-300 px-3">
        <ArrowLeft className="h-4 w-4" />
      </Button>
      <Button type="button" onClick={onNextStep} className="flex-1">
        {isWorkerType ? 'Siguiente: Seleccionar empresa' : 'Siguiente: Datos de sesión'}
      </Button>
    </div>
  </>
  )
}

const DatosEmpresaRegister: React.FC<DataEmpresaRegister> = ({
  values,
  errors,
  onChange,
  onNextStep,
  onPrevStep,
  companies,
  isLoadingCompanies,
  companiesError
}) => {
  const canContinue = !isLoadingCompanies && !companiesError && companies.length > 0

  const handleSelectCompany = (nit: string) => {
    const company = companies.find((c) => c.nit === nit)
    onChange('companyNit', nit)
    onChange('companyName', company?.razsoc ?? '')
  }

  return (
    <>
      {isLoadingCompanies && (
        <div className="flex flex-col items-center justify-center gap-3 py-10 text-gray-600" role="status" aria-live="polite">
          <Loader2 className="h-8 w-8 animate-spin text-emerald-600" />
          <p className="text-sm">Consultando las empresas en las que se encuentra activo...</p>
        </div>
      )}

      {!isLoadingCompanies && companiesError && (
        <div className="flex gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
          <AlertCircle className="h-5 w-5 shrink-0" />
          <p className="whitespace-pre-line">{companiesError}</p>
        </div>
      )}

      {canContinue && (
        <>
          <div>
            <Label className="text-sm font-medium text-gray-700">Empresa con la que desea ingresar *</Label>
            <RadioGroup
              value={values.companyNit}
              onValueChange={handleSelectCompany}
              className="mt-2 gap-2"
            >
              {companies.map((company) => (
                <label
                  key={company.nit}
                  className={`flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors ${
                    values.companyNit === company.nit ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:bg-gray-50'
                  }`}
                >
                  <RadioGroupItem value={company.nit} />
                  <Building2 className="h-5 w-5 shrink-0 text-emerald-600" />
                  <span className="flex flex-col">
                    <span className="text-sm font-medium text-gray-800">{company.razsoc}</span>
                    <span className="text-xs text-gray-500">NIT: {company.nit}</span>
                  </span>
                </label>
              ))}
            </RadioGroup>
            {errors.companyNit && <p className="text-red-500 text-xs mt-1">{errors.companyNit}</p>}
          </div>
          <div>
            <Label htmlFor="position" className="text-sm font-medium text-gray-700">Cargo (opcional)</Label>
            <Input
              id="position"
              type="text"
              value={values.position}
              onChange={(e) => onChange('position', e.target.value)}
              placeholder="Cargo que ejerce"
              className="in-b-form mt-1"
            />
          </div>
        </>
      )}

      <div className="flex gap-3 mt-4">
        <Button
          type="button"
          variant="secondary"
          onClick={onPrevStep}
          disabled={isLoadingCompanies}
          className="bg-purple-100 hover:bg-purple-200 text-purple-900 border-purple-300 px-3"
        >
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <Button type="button" onClick={onNextStep} disabled={!canContinue} className="flex-1">
          Siguiente: Datos de sesión
        </Button>
      </div>
    </>
  )
}

export default function PersonRegisterForm({
  subtitle,
  userTypeLabel,
  values,
  errors,
  isSubmitting,
  documentTypes,
  cityOptions,
  isWorkerType = false,
  isIndependentType = false,
  isPensionerType = false,
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
  workerCompanies = [],
  isLoadingCompanies = false,
  companiesError = null,
}: PropsPersonRegisterForm){
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

  const isJuridicaRepresentative = false;

  return (
    <>
      <HeaderRegister
        subtitle={subtitle}
        userTypeLabel={userTypeLabel}
        onBack={onBack}
      />

      <form onSubmit={onSubmit} className="space-y-3">
        {/* Paso 1: Datos personales */}
        {step === 1 && (
          <DatosPersonalesRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            firstNameRef={firstNameRef}
            lastNameRef={lastNameRef}
            emailRef={emailRef}
            phoneRef={phoneRef}
            identificationRef={identificationRef}
            documentTypes={documentTypes}
            cityOptions={cityOptions}
            isIndependentType={isIndependentType}
            isPensionerType={isPensionerType}
            isWorkerType={isWorkerType}
            onBack={onBack}
          />
        )}

        {/* Paso 2 (Trabajador): selección de empresa activa */}
        {isWorkerType && step === 2 && (
          <DatosEmpresaRegister 
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            onPrevStep={onPrevStep}
            companies={workerCompanies}
            isLoadingCompanies={isLoadingCompanies}
            companiesError={companiesError}
          />
        )}

        {/* Paso 2 (otros) o Paso 3 (Trabajador): Datos sesión */}
        {((!isWorkerType && step === 2) || (isWorkerType && step === 3)) && (
          <SessionRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onPrevStep={onPrevStep}
            isJuridicaRepresentative={isJuridicaRepresentative}
            documentTypes={documentTypes}
            cityOptions={cityOptions}
            hideIdentityFields={isWorkerType}
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
