import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { DataRepresentative } from "@/types/register.d"

const DataRepresentanteRegister: React.FC<DataRepresentative> = ({
  values,
  errors,
  onChange,
  onNextStep,
  onPrevStep,
  documentTypes,
}) => {
  return (
    <>
      <div className="grid grid-cols-2 gap-4">
        <div className="col-span-2">
          <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
            Tipo de documento representante *
          </Label>
          <Select
            value={values.documentTypeRep}
            onValueChange={(v) => onChange("documentTypeRep", v)}
            >
            <SelectTrigger
              className={`in-b-form mt-1 ${errors.documentTypeRep ? "border-red-500" : ""}`}>
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
          {errors.documentTypeRep && <p className="text-red-500 text-xs mt-1">{errors.documentTypeRep}</p>}
        </div>

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
            type="number"
            value={values.repIdentification}
            onChange={(e) => onChange("repIdentification", e.target.value)}
            placeholder="Número de documento"
            className={`in-b-form mt-1 ${errors.repIdentification ? 'border-red-500' : ''}`}
          />
          {errors.repIdentification && <p className="text-red-500 text-xs mt-1">{errors.repIdentification}</p>}
        </div>
        <div>
          <Label htmlFor="repPhone" className="text-sm font-medium text-gray-700">Teléfono *</Label>
          <Input
            id="repPhone"
            type="number"
            value={values.repPhone}
            onChange={(e) => onChange("repPhone", e.target.value)}
            placeholder="Teléfono personal"
            className={`in-b-form mt-1 ${errors.repPhone ? 'border-red-500' : ''}`}
          />
          {errors.repPhone && <p className="text-red-500 text-xs mt-1">{errors.repPhone}</p>}
        </div>
         <div className="col-span-2">
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

      </div>
      <div className="flex gap-3 mt-4">
        <Button type="button" variant="secondary" onClick={onPrevStep}>Volver</Button>
        <Button type="button" onClick={onNextStep} className="flex-1">Siguiente: Datos de sesión</Button>
      </div>
    </>
  )
}

export default DataRepresentanteRegister
