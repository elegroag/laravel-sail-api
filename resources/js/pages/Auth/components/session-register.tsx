import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"

import type {
    DataSession
  } from "@/types/register.d"

const SessionRegister: React.FC<DataSession> = ({
    values,
    errors,
    onChange,
    onPrevStep,
    isJuridicaRepresentative,
    filteredDocumentTypes,
    identificationRef,
    passwordRef,
    showPassword,
    setShowPassword,
    confirmPasswordRef,
    showConfirm,
    setShowConfirm,
    pwdReqs,
    suggestStrongPassword,
    isSubmitting
  }) => {
    return (
      <>
      <div className="grid grid-cols-1 gap-4">
        <div>
          <Label htmlFor="documentTypeUser" className="text-sm font-medium text-gray-700">
            Tipo de documento usuario *
          </Label>
          <Select value={values.documentTypeUser} onValueChange={(v) => onChange("documentTypeUser", v)}>
            <SelectTrigger className={`in-b-form mt-1 ${errors.documentTypeUser ? "border-red-500" : ""} ${isJuridicaRepresentative ? 'bg-gray-50 text-gray-600' : ''}`}>
              <SelectValue placeholder="Selecciona" />
            </SelectTrigger>
            <SelectContent>
              {filteredDocumentTypes.map((doc) => (
                <SelectItem key={doc.value} value={doc.value}>
                  {doc.label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          {errors.documentTypeUser && <p className="text-red-500 text-xs mt-1">{errors.documentTypeUser}</p>}
        </div>
      </div>
      <div className="grid grid-cols-2 gap-4 pb-3">
        <div>
          <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
            Número documento usuario *
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
          <div className="mt-1 grid grid-cols-2 gap-x-4 gap-y-0.5 text-[11px] leading-tight">
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
          <div className="mt-1 text-[12px] text-gray-400 mt-4">
            <span className="truncate">Sugerencia: usa una frase con símbolos y números.</span>
            <Button
              type="button"
              variant="outline"
              onClick={suggestStrongPassword}
              className="h-7 mt-2 px-2 py-0.5 text-[11px] bg-gray-400 border-0 text-gray-700 hover:bg-gray-500 hover:text-gray-200 text-white"
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
    )
  }

  export default SessionRegister;
