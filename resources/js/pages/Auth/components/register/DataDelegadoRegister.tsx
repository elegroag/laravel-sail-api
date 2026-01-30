import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { DataRepresentative } from "@/types/register.d"

const DataDelegadoRegister: React.FC<DataRepresentative> = ({
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
          type="number"
          value={values.phone}
          onChange={(e) => onChange("phone", e.target.value)}
          placeholder="Teléfono representante"
          className="in-b-form mt-1 tel"
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

export default DataDelegadoRegister
