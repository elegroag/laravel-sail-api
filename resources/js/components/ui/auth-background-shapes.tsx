import type React from "react"

// Componente decorativo de fondo (tres figuras). Reutilizable.
// Mantenerlo simple y configurable vía className si hiciera falta.
export default function AuthBackgroundShapes({ className = "" }: { className?: string }) {
  return (
    <div className={className}>
      <div className="absolute top-6 right-6 w-16 h-16 bg-gradient-to-br from-emerald-200 to-teal-300 rounded-2xl opacity-70"></div>
      <div className="absolute bottom-6 right-12 w-8 h-8 bg-gradient-to-tr from-emerald-300 to-green-400 rounded-lg opacity-50"></div>
      <div className="absolute top-1/3 left-6 w-12 h-12 bg-gradient-to-bl from-teal-200 to-emerald-200 rounded-full opacity-40"></div>
    </div>
  )
}
