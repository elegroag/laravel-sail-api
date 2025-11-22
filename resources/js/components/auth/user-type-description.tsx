import type React from "react"
import { userTypes } from "@/constants/auth"
import type { UserType } from "@/types/auth"

// Mapa centralizado de descripciones por tipo de usuario
const USER_TYPE_DESCRIPTIONS: Record<string, string> = {
  empresa:
    "Persona natural o jurídica con trabajadores a su cargo, obligada a realizar aportes y afiliarse al Sistema del Subsidio Familiar, así como a afiliar a sus trabajadores.",
  trabajador:
    "Son todos los trabajadores de carácter permanente que prestan sus servicios personales a un empleador, incluidos los trabajadores domésticos y veteranos.",
  independiente:
    "Persona natural sin vínculo laboral que de forma voluntaria realiza aportes y se afilia al Sistema del Subsidio Familiar para acceder a sus servicios.",
  pensionado:
    "Persona pensionada que de forma voluntaria realiza aportes y se afilia al Sistema del Subsidio Familiar para acceder a los servicios.",
  facultativo:
    "Son personas que, no encontrándose dentro de las categorías anteriores, pueden tener acceso a los servicios de las Cajas de Compensación Familiar por disposición de la Ley o en desarrollo de Convenios celebrados por las mismas.",
  particular:
    "Personas no afiliadas al Sistema del Subsidio Familiar que acceden a servicios de la Caja de Compensación Familiar.",
}

export type UserTypeDescriptionProps = {
  /** id interno del tipo de usuario (empresa, trabajador, etc.) */
  userTypeId?: UserType | string | null
  /** etiqueta mostrada (Empresa o Empleador, Trabajador Dependiente, etc.) */
  userTypeLabel?: string | null
  /** clases CSS extra para personalizar estilos */
  className?: string
  /** texto por defecto cuando no se encuentra descripción */
  fallbackText?: string
}

const DEFAULT_FALLBACK = "Tipo de usuario disponible para el ingreso al portal."

const resolveUserTypeId = (
  userTypeId?: UserType | string | null,
  userTypeLabel?: string | null,
): string | null => {
  if (userTypeId && USER_TYPE_DESCRIPTIONS[userTypeId]) {
    return userTypeId as string
  }

  if (userTypeLabel) {
    const match = userTypes.find((u) => u.label === userTypeLabel)
    if (match && USER_TYPE_DESCRIPTIONS[match.id]) {
      return match.id
    }
  }

  return null
}

const UserTypeDescription: React.FC<UserTypeDescriptionProps> = ({
  userTypeId,
  userTypeLabel,
  className,
  fallbackText = DEFAULT_FALLBACK,
}) => {
  const resolvedId = resolveUserTypeId(userTypeId, userTypeLabel)
  const text = resolvedId ? USER_TYPE_DESCRIPTIONS[resolvedId] : fallbackText

  return (
    <p className={className ?? "text-xs text-gray-500 mt-1"}>
      {text}
    </p>
  )
}

export default UserTypeDescription
