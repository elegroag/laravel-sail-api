// Tipos compartidos para el flujo de registro (mantener SOLID y reutilización)
import type React from "react"

export type UserType =
  | "empresa"
  | "independiente"
  | "facultativo"
  | "particular"
  | "domestico"
  | "trabajador"
  | "pensionado"

export interface UserTypeOption {
  id: UserType
  label: string
  icon: React.ReactNode
}

export interface FormState {
  selectedUserType: UserType | null
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
  errors: Record<string, string>
  isSubmitting: boolean
}

export type FormAction =
  | { type: "SET_USER_TYPE"; payload: UserType }
  | { type: "SET_FIELD"; field: keyof FormState; value: string }
  | { type: "SET_ERROR"; field: string; error: string }
  | { type: "CLEAR_ERRORS" }
  | { type: "SET_SUBMITTING"; payload: boolean }
  | { type: "RESET_FORM" }
