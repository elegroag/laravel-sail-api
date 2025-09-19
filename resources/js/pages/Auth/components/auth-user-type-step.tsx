import type React from "react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import AuthUserTypeSelector from "@/pages/Auth/components/auth-user-type-selector"

// Componente presentacional que agrupa el selector de tipo de usuario,
// el botón de continuar y los enlaces de ayuda/registro.
// Sigue SRP (SOLID): solo se encarga de la vista de este paso.

export interface UserTypeOption {
  id: string
  label: string
  icon: React.ReactNode
}

interface Props {
  title: string
  subtitle: string
  logoSrc: string
  logoAlt: string
  userTypes: UserTypeOption[]
  onSelect: (id: string) => void
  onForgotPassword?: string
  continueDisabled?: boolean
  registerHref: string
}

const AuthUserTypeStep: React.FC<Props> = ({
  title,
  subtitle,
  logoSrc,
  logoAlt,
  userTypes,
  onSelect,
  onForgotPassword,
  continueDisabled = true,
  registerHref,
}) => {
  return (
    <>
      <AuthUserTypeSelector
        title={title}
        subtitle={subtitle}
        logoSrc={logoSrc}
        logoAlt={logoAlt}
        userTypes={userTypes}
        onSelect={onSelect}
      />

      <Button
        className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium mb-6 shadow-lg"
        disabled={continueDisabled}
      >
        Continuar
      </Button>

      <div className="flex justify-center space-x-8 text-sm">
        <TextLink href={onForgotPassword} className="text-gray-500 hover:text-emerald-600 flex items-center">
          <span className="mr-1">?</span>
          Olvidé mi clave
        </TextLink>

        <TextLink href={registerHref} className="text-gray-500 hover:text-emerald-600 flex items-center">
          <span className="mr-1">🔑</span>
          Crear cuenta
        </TextLink>
      </div>
    </>
  )
}

export default AuthUserTypeStep
