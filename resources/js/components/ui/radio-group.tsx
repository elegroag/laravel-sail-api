import * as React from "react"
import { cn } from "@/lib/utils"

type RadioGroupContextType = {
  name: string
  value: string
  onValueChange: (v: string) => void
}

const RadioGroupContext = React.createContext<RadioGroupContextType | null>(null)

type RadioGroupProps = {
  value: string
  onValueChange: (v: string) => void
  name?: string
  className?: string
  children: React.ReactNode
}

// RadioGroup sin dependencias externas, con semántica accesible
const RadioGroup = React.forwardRef<HTMLDivElement, RadioGroupProps>(
  ({ value, onValueChange, name, className, children }, ref) => {
    const groupName = React.useId()
    const ctx = React.useMemo(
      () => ({ name: name ?? `rg-${groupName}`, value, onValueChange }),
      [groupName, name, value, onValueChange]
    )
    return (
      <RadioGroupContext.Provider value={ctx}>
        <div
          ref={ref}
          role="radiogroup"
          data-slot="radio-group"
          className={cn("grid gap-2", className)}
        >
          {children}
        </div>
      </RadioGroupContext.Provider>
    )
  }
)
RadioGroup.displayName = "RadioGroup"

type RadioGroupItemProps = {
  value: string
  disabled?: boolean
  className?: string
}

const RadioGroupItem = React.forwardRef<HTMLButtonElement, RadioGroupItemProps>(
  ({ value, disabled, className }, ref) => {
    const ctx = React.useContext(RadioGroupContext)
    if (!ctx) throw new Error("RadioGroupItem debe usarse dentro de RadioGroup")
    const checked = ctx.value === value

    const id = React.useId()

    const handleSelect = () => {
      if (!disabled) ctx.onValueChange(value)
    }

    return (
      <button
        ref={ref}
        id={id}
        role="radio"
        aria-checked={checked}
        aria-disabled={disabled}
        type="button"
        onClick={handleSelect}
        disabled={disabled}
        data-slot="radio-group-item"
        className={cn(
          // Contenedor circular
          "h-4 w-4 shrink-0 rounded-full border border-input bg-background",
          // Focus y accesibilidad
          "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 ring-offset-background",
          // Estado checked (reforzar borde)
          checked ? "border-primary" : "border-input",
          // Disabled
          "disabled:cursor-not-allowed disabled:opacity-50",
          // Transición
          "transition-colors duration-150 ease-out",
          className
        )}
      >
        <span
          aria-hidden
          className={cn(
            "block h-2 w-2 rounded-full",
            checked ? "bg-primary" : "bg-transparent"
          )}
        />
        {/* Input oculto para semántica de formulario */}
        <input
          type="radio"
          name={ctx.name}
          value={value}
          checked={checked}
          onChange={() => ctx.onValueChange(value)}
          className="sr-only"
          tabIndex={-1}
        />
      </button>
    )
  }
)
RadioGroupItem.displayName = "RadioGroupItem"

export { RadioGroup, RadioGroupItem }
