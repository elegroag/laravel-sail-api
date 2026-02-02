import React, { useEffect, useState } from "react"
import type { DocumentTypeOption } from "@/types/auth"
import type {
  PropsCompanyRegisterForm,
} from "@/types/register.d"
import HeaderRegister from "./HeaderRegister"
import AccountResponsibleSelect from "./AccountResponsibleSelect"
import { 
  DataCompanyRegister, 
  DataDelegadoRegister, 
  DataRepresentanteRegister, 
  SessionRegister 
} from "./index"

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

        {/* Paso 2: Selección responsable de la cuenta */}
        {step === 2 && isJuridica && (
            <AccountResponsibleSelect
              value={values.userRole}
              onChange={onChange}
              error={errors.userRole}
              isJuridica={isJuridica}
              onNextStep={onNextStep}
              onPrevStep={onPrevStep}
              userRole={values.userRole}
            />
        )}

        {/* Paso 3: Datos representante */}
        {step === 3 && (
           <DataRepresentanteRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            onPrevStep={onPrevStep}
            documentTypes={documentTypes}
          />
        )}

        {/* Paso 4 (solo delegado): Datos del delegado */}
        {step === 4 && values.userRole === 'delegado' && (
          <DataDelegadoRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            onPrevStep={onPrevStep}
            documentTypes={documentTypes}
            cityOptions={cityOptions}
            firstNameRef={firstNameRef}
            lastNameRef={lastNameRef}
            emailRef={emailRef}
            phoneRef={phoneRef}
          />
        )}

        {/* Paso sesión: paso 4 (no delegado) o paso 5 (delegado) */}
        {((values.userRole !== 'delegado' && step === 4) || (values.userRole === 'delegado' && step === 5)) && (
          <SessionRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onPrevStep={onPrevStep}
            isJuridicaRepresentative={isJuridicaRepresentative}
            documentTypes={documentTypes}
            cityOptions={cityOptions}
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
