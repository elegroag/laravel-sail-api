import { Building2, GraduationCap, Briefcase, Users, HardHat, User, Mail, MessageCircle } from 'lucide-vue-next'
import type { UserTypeOption, DeliveryMethod } from '@/types/auth'

export const userTypes: UserTypeOption[] = [
  { id: 'empresa', label: 'Empresa o Empleador', icon: Building2 },
  { id: 'trabajador', label: 'Trabajador Dependiente', icon: HardHat },
  { id: 'independiente', label: 'Trabajador Independiente-Aportante', icon: GraduationCap },
  { id: 'pensionado', label: 'Pensionado', icon: User },
  { id: 'facultativo', label: 'Facultativo', icon: Briefcase },
  { id: 'particular', label: 'Particular', icon: Users },
]

export const documentTypes = [
  { value: 'cc', label: 'Cédula de Ciudadanía' },
  { value: 'ce', label: 'Cédula de Extranjería' },
  { value: 'nit', label: 'NIT' },
  { value: 'pasaporte', label: 'Pasaporte' },
]

export enum TipoFuncionario {
  empresa = 'E',
  independiente = 'I',
  facultativo = 'F',
  particular = 'P',
  trabajador = 'T',
  pensionado = 'O',
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