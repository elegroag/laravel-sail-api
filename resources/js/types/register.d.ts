import type { DocumentTypeOption } from "@/types/auth"

// Interfaces base para compartir propiedades comunes entre pasos del formulario
interface BaseFormProps {
    values: RegisterValues
    errors: Record<string, string>
    onChange: (field: keyof RegisterValues, value: string) => void
}
  
interface WithNextStep {
    onNextStep?: () => void
}
  
interface WithPrevStep {
    onPrevStep?: () => void
}
  
export interface DataCompany extends BaseFormProps, WithNextStep {
    categoryOptions: DocumentTypeOption[]
    documentTypes: DocumentTypeOption[]
    societyOptions: DocumentTypeOption[]
    isJuridicaRepresentative: boolean
    companyNameRef?: React.Ref<HTMLInputElement>
    companyNitRef?: React.Ref<HTMLInputElement>
    addressRef?: React.Ref<HTMLInputElement>
}
  
export interface DataRepresentative extends BaseFormProps, WithNextStep, WithPrevStep {
    isJuridica: boolean
    isNatural: boolean
    cityOptions: DocumentTypeOption[]
    firstNameRef?: React.Ref<HTMLInputElement>
    lastNameRef?: React.Ref<HTMLInputElement>
    emailRef?: React.Ref<HTMLInputElement>
    phoneRef?: React.Ref<HTMLInputElement>
}

export interface DataPersonRegister extends BaseFormProps, WithNextStep {
    firstNameRef: React.Ref<HTMLInputElement>
    lastNameRef: React.Ref<HTMLInputElement>
    emailRef: React.Ref<HTMLInputElement>
    phoneRef: React.Ref<HTMLInputElement>
    cityOptions: DocumentTypeOption[]
    isIndependentType?: boolean
    isPensionerType?: boolean
    isWorkerType?: boolean
    onBack: () => void
}

export interface DataEmpresaRegister extends BaseFormProps, WithNextStep, WithPrevStep {}
  
export interface DataDelegado extends BaseFormProps, WithNextStep, WithPrevStep {}
  
export interface DataSession extends BaseFormProps, WithPrevStep {
    isJuridicaRepresentative: boolean;
    filteredDocumentTypes: DocumentTypeOption[];
    identificationRef?: React.Ref<HTMLInputElement>;
    passwordRef?: React.Ref<HTMLInputElement>;
    showPassword: boolean;
    setShowPassword: React.Dispatch<React.SetStateAction<boolean>>;
    confirmPasswordRef?: React.Ref<HTMLInputElement>;
    showConfirm: boolean;
    setShowConfirm: React.Dispatch<React.SetStateAction<boolean>>;
    pwdReqs: {
      length: boolean,
      upper: boolean,
      number: boolean,
      symbol: boolean,
    };
    suggestStrongPassword: () => void;
    isSubmitting: boolean;
}

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
    contributionRate: string,
    documentTypeUser: string
}

export interface PropsCompanyRegisterForm {
    subtitle?: string
    userTypeLabel?: string
    values: RegisterValues
    errors: Record<string, string>
    isSubmitting: boolean
    documentTypes: DocumentTypeOption[]
    societyOptions: DocumentTypeOption[]
    cityOptions: DocumentTypeOption[]
    categoryOptions: DocumentTypeOption[]
    onBack: () => void
    onChange: (field: keyof RegisterValues, value: string) => void
    onSubmit: (e: React.FormEvent) => void
    step?: number
    onNextStep?: () => void
    onPrevStep?: () => void
    firstNameRef: React.Ref<HTMLInputElement>
    lastNameRef: React.Ref<HTMLInputElement>
    emailRef: React.Ref<HTMLInputElement>
    phoneRef: React.Ref<HTMLInputElement>
    identificationRef: React.Ref<HTMLInputElement>
    passwordRef: React.Ref<HTMLInputElement>
    confirmPasswordRef: React.Ref<HTMLInputElement>
    companyNameRef: React.Ref<HTMLInputElement>
    companyNitRef: React.Ref<HTMLInputElement>
    addressRef: React.Ref<HTMLInputElement>
}

export interface PropsPersonRegisterForm extends  PropsCompanyRegisterForm {
    isWorkerType: boolean
    isIndependentType: boolean
    isPensionerType: boolean
}


export interface HeaderRegisterProps  {
    subtitle?: string
    userTypeLabel?: string,
    onBack: () => void
}