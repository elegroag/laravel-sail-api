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
    repPhone: string
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
