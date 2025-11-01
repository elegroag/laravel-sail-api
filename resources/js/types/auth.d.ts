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

export interface FormBasic {
    documentType: string,
    identification: string,
    email: string,
    errors: Record<string, string>,
    isSubmitting: boolean,
    isSuccess: boolean,
}

export interface FormBasicRecovery extends FormBasic {
    delivery_method: string,
    whatsapp?: string,
    email?: string,
}

export interface FormState extends FormBasic {
    selectedUserType: UserType | null
    firstName: string
    lastName: string
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
    contributionRate: string
    // Datos del representante (solo aplica cuando userRole === 'delegado')
    repName: string
    repIdentification: string
    repEmail: string
    repPhone: string,
    documentTypeUser: string,
    documentTypeRep: string
}

export type FormAction =
    | { type: "SET_USER_TYPE"; payload: UserType }
    | { type: "SET_FIELD"; field: keyof FormState; value: string }
    | { type: "SET_ERROR"; field: string; error: string }
    | { type: "CLEAR_ERRORS" }
    | { type: "SET_SUBMITTING"; payload: boolean }
    | { type: "RESET_FORM" }
    | { type: "SET_SUCCESS"; payload: boolean }
    | { type: "CLEAR_ERROR"; field: string }


export type FormActionRecovery =
    | { type: "SET_FIELD"; field: keyof FormBasicRecovery; value: string }
    | { type: "SET_ERROR"; field: string; error: string }
    | { type: "CLEAR_ERRORS" }
    | { type: "SET_SUBMITTING"; payload: boolean }
    | { type: "RESET_FORM" }
    | { type: "SET_SUCCESS"; payload: boolean }
    | { type: "CLEAR_ERROR"; field: string }

export type LoginProps = {
    Coddoc?: { [key: string]: string };
    Tipsoc?: { [key: string]: string };
    Codciu?: { [key: string]: string };
    Detadoc?: { [key: string]: string };
    errors?: Record<string, string>,
}

export type DocumentTypeOption = { value: string; label: string }


// Tipado fuerte del payload que se envía al backend (evita any)
export interface RegisterPayload {
    selected_user_type: string | null
    tipo: string | number
    // Sesión
    coddoc: string
    documento: string
    password: string
    // Empresa (opcionales)
    tipdoc?: string
    razsoc?: string
    nit?: string
    tipsoc?: string
    tipper?: string
    // Personales
    nombre: string
    email: string
    telefono: number
    codciu: number
    // Empresa (delegado/representante)
    is_delegado?: boolean
    cargo?: string
    rep_nombre?: string
    rep_coddoc?: string
    rep_documento?: string
    rep_email?: string
    rep_telefono?: number
    // Independiente/Pensionado
    contribution_rate?: string,
    first_name?: string,
    last_name?: string,
}


export type VerificationState = {
    code: string[]
    error: string | null
    canResend: boolean
    resendTimer: number
    isVerified: boolean
    deliveryMethod: DeliveryMethod
}


export type VerificationAction =
  | { type: 'SET_CODE_DIGIT'; index: number; value: string }
  | { type: 'SET_ERROR'; error: string | null }
  | { type: 'SET_CAN_RESEND'; canResend: boolean }
  | { type: 'SET_RESEND_TIMER'; timer: number }
  | { type: 'SET_VERIFIED'; verified: boolean }
  | { type: 'SET_DELIVERY_METHOD'; method: DeliveryMethod }
  | { type: 'RESET_CODE' }

export type DeliveryMethod = 'email' | 'whatsapp'

export type VerifyEmailProps = {
  documento?: string
  coddoc?: string
  tipo?: string
  errors: Record<string, string>
  token?: string,
  status?: string
}


