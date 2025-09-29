import { ChevronLeft, Info } from "lucide-react"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import { userTypes } from "@/constants/auth"

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
    <>
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

        {/* Botón para mostrar diálogo con información de opciones de ingreso */}
        <div className="mt-4">
          <Dialog>
            <DialogTrigger asChild>
              <Button variant="outline" className="border-white/30 text-white bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 hover:bg-white/10 hover:text-white">
                <Info className="w-4 h-4" />
                Ver opciones de ingreso
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Opciones de ingreso</DialogTitle>
                <DialogDescription>
                  Selecciona el tipo de usuario que mejor te represente para continuar con el proceso.
                </DialogDescription>
              </DialogHeader>
              <div className="grid gap-3">
                {userTypes.map((ut) => (
                  <div key={ut.id} className="flex items-start gap-3">
                    <div className="shrink-0">{ut.icon}</div>
                    <div>
                      <p className="font-medium leading-tight">{ut.label}</p>
                      <p className="text-muted-foreground text-sm">
                        {(
                          {
                            empresa: "Empresas que realizan aportes como empleadoras.",
                            independiente: "Personas que realizan aportes de forma independiente.",
                            facultativo: "Usuarios con vinculación facultativa a servicios.",
                            particular: "Personas naturales interesadas en servicios particulares.",
                            domestico: "Empleadores o trabajadores del servicio doméstico.",
                            trabajador: "Trabajadores afiliados por su empresa.",
                            pensionado: "Personas pensionadas afiliadas a la caja.",
                          } as Record<string, string>
                        )[ut.id] || "Tipo de usuario disponible para el ingreso al portal."}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>
    </>
  )
}
