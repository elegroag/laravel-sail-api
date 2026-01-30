import type React from "react"

// Selector reutilizable de tipos de usuario/funcionarios
// SRP: solo renderiza la UI de selección y emite el id seleccionado
// Abierto/Cerrado: configurable por props

export type UserTypeOption<T extends string = string> = {
  id: T
  label: string
  icon: React.ReactNode
}

type AuthUserTypeSelectorProps<T extends string = string> = {
  title: string
  subtitle?: string
  logoSrc?: string
  logoAlt?: string
  userTypes: UserTypeOption<T>[]
  onSelect: (id: T) => void
}

export default function AuthUserTypeSelector<T extends string = string>({
  title,
  subtitle,
  logoSrc,
  logoAlt,
  userTypes,
  onSelect,
}: AuthUserTypeSelectorProps<T>) {
  return (
    <>
      {logoSrc ? (
        <div className="mb-6 flex justify-center">
          <img src={logoSrc} alt={logoAlt ?? "Logo"} width={180} height={60} className="opacity-90" />
        </div>
      ) : null}

      <h2 className="text-2xl font-semibold text-gray-800 mb-2 text-center">{title}</h2>
      {subtitle ? (
        <p className="text-lg text-gray-600 mb-8 text-center">{subtitle}</p>
      ) : null}

      <div className="grid grid-cols-3 gap-4 mb-8">
        {userTypes.map((userType) => (
          <button
            key={userType.id}
            onClick={() => onSelect(userType.id)}
            className="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-emerald-500 hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 transition-all duration-200 group"
          >
            <div className="text-emerald-600 group-hover:text-emerald-700 mb-2">{userType.icon}</div>
            <span className="text-xs text-gray-600 text-center font-medium">{userType.label}</span>
          </button>
        ))}
      </div>
    </>
  )
}
