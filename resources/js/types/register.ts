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
}
