import React from "react"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { RegisterValues } from "@/types/register"

interface AccountResponsibleSelectProps {
  value: string
  onChange: (field: keyof RegisterValues, value: string) => void;
  error?: string
  isJuridica?: boolean
  onNextStep?: () => void
  onPrevStep?: () => void
  userRole?: string
}

const AccountResponsibleSelect: React.FC<AccountResponsibleSelectProps> = ({
  value,
  onChange,
  error,
  isJuridica = false,
  onNextStep,
  onPrevStep,
  userRole
}) => {
  return (
    <div className="space-y-4">
      <Label htmlFor="userRole" className="text-sm font-medium text-gray-700">
        ¿Eres representante o delegado? *
      </Label>
      <Select value={value} onValueChange={(value) => onChange("userRole", value)}>
        <SelectTrigger className={`in-b-form mt-1 ${error ? "border-red-500" : ""}`}>
          <SelectValue placeholder="Selecciona" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="representante">Representante legal</SelectItem>
          {isJuridica && (
            <SelectItem value="delegado">Delegado de la empresa</SelectItem>
          )}
        </SelectContent>
      </Select>
      {error && <p className="text-red-500 text-xs mt-1">{error}</p>}

      <div className="flex gap-3 mt-6">
        <button
          type="button"
          onClick={onPrevStep}
          className="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Volver a datos de empresa
        </button>
        <button
          type="button"
          onClick={onNextStep}
          className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          {userRole === 'delegado' ? 'Siguiente: Datos del delegado' : 'Siguiente: Datos del representante'}
        </button>
      </div>
    </div>
  )
}

export default AccountResponsibleSelect
