// Constantes del flujo de registro
import { Building2, GraduationCap, Briefcase, Users, HardHat, User, Mail, MessageCircle } from "lucide-react"
import type { UserTypeOption, DeliveryMethod } from "@/types/auth"

export const userTypes: UserTypeOption[] = [
  { id: "empresa", label: "Empresa o Empleador", icon: <Building2 className="w-8 h-8 text-blue-500" /> },
  { id: "trabajador", label: "Trabajador Dependiente", icon: <HardHat className="w-8 h-8 text-yellow-500" /> },
  { id: "independiente", label: "Trabajador Independiente-Aportante", icon: <GraduationCap className="w-8 h-8 text-green-500" /> },
  { id: "pensionado", label: "Pensionado", icon: <User className="w-8 h-8 text-pink-500" /> },
  { id: "facultativo", label: "Facultativo", icon: <Briefcase className="w-8 h-8 text-purple-500" /> },
  { id: "particular", label: "Particular", icon: <Users className="w-8 h-8 text-orange-500" /> },
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
    trabajador= "T",
    pensionado= "O",
}

export const DeliveryOptions: Array<{
  id: DeliveryMethod
  label: string
  description: string
  icon: typeof Mail
}> = [
  {
    id: 'email',
    label: 'Correo electrónico',
    description: 'Recibirás el código en tu bandeja de entrada asociada.',
    icon: Mail,
  },
  {
    id: 'whatsapp',
    label: 'WhatsApp',
    description: 'Enviaremos el código a tu número registrado por WhatsApp.',
    icon: MessageCircle,
  },
]
