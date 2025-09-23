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

export type LoginProps = {
    Coddoc: { [key: string]: string };
    Tipsoc: { [key: string]: string };
    Codciu: { [key: string]: string };
    Detadoc: { [key: string]: string };
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
    telefono: string
    codciu: string
    // Empresa (delegado/representante)
    is_delegado?: boolean
    cargo?: string
    rep_nombre?: string
    rep_coddoc?: string
    rep_documento?: string
    rep_email?: string
    rep_telefono?: string
    // Independiente/Pensionado
    contribution_rate?: string
}
