// Constantes del flujo de registro
import { Building2, GraduationCap, Briefcase, Users, Home, HardHat, User } from "lucide-react"
import type { UserTypeOption } from "@/types/auth"

export const userTypes: UserTypeOption[] = [
  { id: "empresa", label: "Empresa aportante", icon: <Building2 className="w-8 h-8 text-blue-500" /> },
  { id: "independiente", label: "Independiente aportante", icon: <GraduationCap className="w-8 h-8 text-green-500" /> },
  { id: "facultativo", label: "Facultativo", icon: <Briefcase className="w-8 h-8 text-purple-500" /> },
  { id: "particular", label: "Particular", icon: <Users className="w-8 h-8 text-orange-500" /> },
  { id: "domestico", label: "Servicio doméstico", icon: <Home className="w-8 h-8 text-red-500" /> },
  { id: "trabajador", label: "Trabajador", icon: <HardHat className="w-8 h-8 text-yellow-500" /> },
  { id: "pensionado", label: "Pensionado", icon: <User className="w-8 h-8 text-pink-500" /> },
]

export const documentTypes = [
  { value: "cc", label: "Cédula de Ciudadanía" },
  { value: "ce", label: "Cédula de Extranjería" },
  { value: "nit", label: "NIT" },
  { value: "pasaporte", label: "Pasaporte" },
]

export enum TipoFuncionario {
    empresa= "E",
    independiente= "I",
    facultativo= "F",
    particular= "P",
    domestico= "D",
    trabajador= "T",
    pensionado= "O",
}
