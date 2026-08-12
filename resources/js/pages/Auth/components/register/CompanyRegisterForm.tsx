import React, { useEffect, useState } from "react"
import type { DocumentTypeOption } from "@/types/auth"
import type {
  PropsCompanyRegisterForm,
} from "@/types/register.d"
import HeaderRegister from "./HeaderRegister"
import { 
  DataCompanyRegister, 
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
  identificationRef,
  passwordRef,
  confirmPasswordRef,
  companyNameRef,
  companyNitRef,
}: PropsCompanyRegisterForm){
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

  // Persona Natural (N): todos menos NIT y NUIP
  // Persona Jurídica (J): solo NIT y forzar selección
  const isNatural = values.companyCategory === 'N'
  const isJuridica = values.companyCategory === 'J'

  const isNitOption = (opt: DocumentTypeOption) =>
    opt.label.toLowerCase().includes('nit') ||
    opt.value.toLowerCase() === 'nit' ||
    opt.value === '3'

  const isNuipOption = (opt: DocumentTypeOption) =>
    opt.label.toLowerCase().includes('nuip') || opt.value.toLowerCase() === 'nu'

  const isRepresentativeForbidden = (opt: DocumentTypeOption) =>
    isNitOption(opt) || isNuipOption(opt)

  const representativeDocumentTypes = React.useMemo(
    () => (documentTypes || []).filter((opt) => !isRepresentativeForbidden(opt)),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [documentTypes],
  )

  const companyDocumentTypes = React.useMemo(() => {
    if (isJuridica) {
      const nit = (documentTypes || []).find(isNitOption)
      return nit ? [nit] : documentTypes || []
    }
    return (documentTypes || []).filter((opt) => !isNitOption(opt) && !isNuipOption(opt))
  }, [documentTypes, isJuridica])

  useEffect(() => {
    // Siempre representante legal: no hay flujo de delegado
    if (values.userRole !== 'representante') {
      onChange('userRole', 'representante')
    }

    if (isJuridica) {
      const nit = (documentTypes || []).find(isNitOption)
      if (nit && values.documentType !== nit.value) {
        onChange('documentType', nit.value)
      }
    } else if (isNatural) {
      const currentDoc = (documentTypes || []).find(
        (o) => o.value === values.documentType,
      )
      if (currentDoc && (isNitOption(currentDoc) || isNuipOption(currentDoc))) {
        onChange('documentType', '')
      }
    }

    const repCurrent = (documentTypes || []).find(
      (o) => o.value === values.documentTypeRep,
    )
    if (repCurrent && isRepresentativeForbidden(repCurrent)) {
      onChange('documentTypeRep', '')
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [values.companyCategory])

  return (
    <>
      <HeaderRegister
        subtitle={subtitle}
        userTypeLabel={userTypeLabel}
        onBack={onBack}
      />

      <form onSubmit={onSubmit} className="space-y-3">
        {step === 1 && (
          <DataCompanyRegister
            values={values}
            categoryOptions={categoryOptions}
            documentTypes={companyDocumentTypes}
            societyOptions={societyOptions}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            isJuridicaRepresentative={isJuridica}
            companyNameRef={companyNameRef}
            companyNitRef={companyNitRef}
          />
        )}

        {step === 2 && (
           <DataRepresentanteRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onNextStep={onNextStep}
            onPrevStep={onPrevStep}
            documentTypes={representativeDocumentTypes}
          />
        )}

        {step === 3 && (
          <SessionRegister
            values={values}
            errors={errors}
            onChange={onChange}
            onPrevStep={onPrevStep}
            isJuridicaRepresentative={isJuridica}
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
