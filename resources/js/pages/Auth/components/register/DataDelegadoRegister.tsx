import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { ArrowLeft } from 'lucide-react'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { DataDelegado } from "@/types/register.d"

const DataDelegadoRegister: React.FC<DataDelegado> = ({
  values,
  errors,
  onChange,
  onNextStep,
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
    <div className="grid grid-cols-2 gap-4">
      <div>
        <Label htmlFor="firstName" className="text-sm font-medium text-gray-700">
          Nombre delegado *
        </Label>
        <Input
          id="firstName"
          ref={firstNameRef}
          type="text"
          value={values.firstName}
          onChange={(e) => onChange("firstName", e.target.value)}
          placeholder="Nombre delegado"
          className={`in-b-form mt-1 ${errors.firstName ? "border-red-500" : ""}`}
        />
        {errors.firstName && <p className="text-red-500 text-xs mt-1">{errors.firstName}</p>}
      </div>
      <div>
        <Label htmlFor="lastName" className="text-sm font-medium text-gray-700">
          Apellido delegado *
        </Label>
        <Input
          id="lastName"
          ref={lastNameRef}
          type="text"
          value={values.lastName}
          onChange={(e) => onChange("lastName", e.target.value)}
          placeholder="Apellido delegado"
          className={`in-b-form mt-1 ${errors.lastName ? "border-red-500" : ""}`}
        />
        {errors.lastName && <p className="text-red-500 text-xs mt-1">{errors.lastName}</p>}
      </div>
    </div>
    <div>
      <Label htmlFor="email" className="text-sm font-medium text-gray-700">
        Email delegado *
      </Label>
      <Input
        id="email"
        ref={emailRef}
        type="email"
        value={values.email}
        onChange={(e) => onChange("email", e.target.value)}
        placeholder="Correo delegado"
        className={`in-b-form mt-1 ${errors.email ? "border-red-500" : ""}`}
      />
      {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
    </div>
    <div className="grid grid-cols-2 gap-4">
      <div>
        <Label htmlFor="phone" className="text-sm font-medium text-gray-700">
          Celular delegado
        </Label>
        <Input
          id="phone"
          ref={phoneRef}
          type="number"
          value={values.phone}
          onChange={(e) => onChange("phone", e.target.value)}
          placeholder="Celular delegado"
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
      <Button type="button" variant="secondary" onClick={onPrevStep} className="bg-purple-100 hover:bg-purple-200 text-purple-900 border-purple-300 px-3">
        <ArrowLeft className="h-4 w-4" />
      </Button>
      <Button type="button" onClick={onNextStep} className="flex-1">
        Siguiente: Datos de sesión
      </Button>
    </div>
    </>
  )
}

export default DataDelegadoRegister
