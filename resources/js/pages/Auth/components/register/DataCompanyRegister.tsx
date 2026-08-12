import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { DataCompany } from "@/types/register.d"

const DataCompanyRegister: React.FC<DataCompany> = (
  {
    values,
    categoryOptions,
    documentTypes,
    societyOptions,
    errors,
    onChange,
    onNextStep,
    isJuridicaRepresentative: _isJuridicaRepresentative,
    companyNameRef,
    companyNitRef,
  }
) => {
return (
    <>
      <div className="flex justify-between gap-4">
        <div className="w-60">
          <Label htmlFor="companyCategory" className="text-sm font-medium text-gray-700">
            Tipo persona *
          </Label>
          <Select value={values.companyCategory || undefined} onValueChange={(v) => onChange("companyCategory", v)}>
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
            key={`doc-type-${values.documentType || 'empty'}`}
            value={values.documentType || undefined}
            onValueChange={(v) => onChange("documentType", v)}
            >
            <SelectTrigger
              className={`in-b-form mt-1 ${errors.documentType ? "border-red-500" : ""}`}>
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
          placeholder={values.companyCategory === 'N' ? 'Cédula del representante' : 'NIT de la empresa'}
          className={`in-b-form mt-1 ${errors.companyNit ? "border-red-500" : ""}`}
        />
        {values.companyCategory === 'N' && (
          <p className="text-xs text-slate-500 mt-1">
            Para persona natural, este número corresponde a la misma cédula del representante legal.
          </p>
        )}
        {values.companyCategory === 'J' && (
          <p className="text-xs text-slate-500 mt-1">
            Para persona jurídica, el NIT no puede ser la misma cédula del representante legal.
          </p>
        )}
        {errors.companyNit && <p className="text-red-500 text-xs mt-1">{errors.companyNit}</p>}
      </div>
      <div>
        <Label htmlFor="societyType" className="text-sm font-medium text-gray-700">
          Tipo de sociedad *
        </Label>
        <Select value={values.societyType || undefined} onValueChange={(v) => onChange("societyType", v)}>
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
      <Button type="button" className="w-full mt-4" onClick={onNextStep}>
        Siguiente: Datos representante
      </Button>
    </>
  )
}

export default DataCompanyRegister
