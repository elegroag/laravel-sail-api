import * as React from "react"
import * as CheckboxPrimitive from "@radix-ui/react-checkbox"
import { CheckIcon, MinusIcon } from "lucide-react"

import { cn } from "@/lib/utils"

// Componente Checkbox accesible, con soporte para estado indeterminate
// y estilos tailwind consistentes con el sistema de UI.
const Checkbox = React.forwardRef<
  React.ElementRef<typeof CheckboxPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof CheckboxPrimitive.Root>
>(({ className, "aria-label": ariaLabel, ...props }, ref) => {
  return (
    <CheckboxPrimitive.Root
      ref={ref}
      data-slot="checkbox"
      aria-label={ariaLabel}
      className={cn(
        // Tamaño/base
        "peer h-4 w-4 shrink-0 rounded-sm",
        // Bordes y fondo por defecto
        "border border-input bg-background",
        // Estados checked/indeterminate
        "data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground",
        "data-[state=indeterminate]:bg-primary data-[state=indeterminate]:text-primary-foreground",
        // Focus y accesibilidad
        "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 ring-offset-background",
        // Deshabilitado y errores
        "disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive",
        // Transición/sombra
        "transition-colors duration-150 ease-out",
        className
      )}
      {...props}
    >
      <CheckboxPrimitive.Indicator
        data-slot="checkbox-indicator"
        className="flex items-center justify-center text-current"
      >
        {/* Muestra check cuando está checked y un guión cuando es indeterminate */}
        <CheckIcon className="h-3.5 w-3.5 data-[state=indeterminate]:hidden" />
        <MinusIcon className="h-3.5 w-3.5 hidden data-[state=indeterminate]:block" />
      </CheckboxPrimitive.Indicator>
    </CheckboxPrimitive.Root>
  )
})

Checkbox.displayName = "Checkbox"

export { Checkbox }
