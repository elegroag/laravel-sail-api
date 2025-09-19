import { ChevronLeft } from "lucide-react"
import TextLink from "@/components/text-link"

// Componente de bienvenida reutilizable para pantallas de autenticación
// Principio de Responsabilidad Única: encapsula solo la sección visual de bienvenida
// Abierto/Cerrado: configurable vía props sin modificar su implementación

type AuthWelcomeProps = {
  title: string
  tagline?: string
  description?: string
  backHref: string
  backText: string
}

export default function AuthWelcome({
  title,
  tagline,
  description,
  backHref,
  backText,
}: AuthWelcomeProps) {
  return (
    <div className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
      {/* Elementos decorativos */}
      <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full -translate-y-16 translate-x-16 opacity-60" />
      <div className="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full translate-y-12 -translate-x-12 opacity-40" />
      <div className="absolute top-1/2 left-0 w-16 h-16 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full -translate-x-8 opacity-30" />

      <div className="relative z-10">
        <h1 className="text-4xl font-bold mb-2">{title}</h1>
        <div className="w-16 h-0.5 bg-white mb-6" />

        {tagline ? (
          <p className="text-emerald-100 text-lg mb-6">{tagline}</p>
        ) : null}

        {description ? (
          <p className="text-emerald-100 text-sm leading-relaxed mb-6">{description}</p>
        ) : null}

        <TextLink href={backHref} className="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm">
          <ChevronLeft className="w-4 h-4 mr-1" />
          {backText}
        </TextLink>
      </div>
    </div>
  )
}
