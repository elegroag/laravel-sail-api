// Tipos compartidos para formularios de registro
// Mantiene bajo acoplamiento entre componentes (empresa/persona) y validación

export type RegisterValues = {
  documentType: string
  identification: string
  firstName: string
  lastName: string
  email: string
  phone: string
  password: string
  confirmPassword: string
  companyName: string
  companyNit: string
  address: string
  city: string
  societyType: string
  companyCategory: string
  userRole: string
  position: string
  // Datos del representante (solo cuando userRole === 'delegado')
  repName: string
  repIdentification: string
  repEmail: string
  repPhone: string
  // Aportes (independiente/pensionado): 2%, 0.6% o 0%
  contributionRate: string
}
