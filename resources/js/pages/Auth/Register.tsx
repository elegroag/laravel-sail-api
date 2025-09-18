import type React from "react"
import { useReducer, useRef, useEffect } from "react"
import { Building2, GraduationCap, Briefcase, Users, Home, HardHat } from "lucide-react"
import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/components/auth-welcome";
import AuthUserTypeSelector from "@/components/auth-user-type-selector";
import RegisterForm from "@/components/register-form";
import imageLogo from "../../assets/comfaca-logo.png";

type UserType = "empresa" | "independiente" | "facultativo" | "particular" | "domestico" | "trabajador"

interface UserTypeOption {
  id: UserType
  label: string
  icon: React.ReactNode
}

const userTypes: UserTypeOption[] = [
  { id: "empresa", label: "Empresa aportante", icon: <Building2 className="w-8 h-8 text-blue-500" /> },
  { id: "independiente", label: "Independiente aportante", icon: <GraduationCap className="w-8 h-8 text-green-500" /> },
  { id: "facultativo", label: "Facultativo", icon: <Briefcase className="w-8 h-8 text-purple-500" /> },
  { id: "particular", label: "Particular", icon: <Users className="w-8 h-8 text-orange-500" /> },
  { id: "domestico", label: "Servicio doméstico", icon: <Home className="w-8 h-8 text-red-500" /> },
  { id: "trabajador", label: "Trabajador", icon: <HardHat className="w-8 h-8 text-yellow-500" /> },
]

const documentTypes = [
  { value: "cc", label: "Cédula de Ciudadanía" },
  { value: "ce", label: "Cédula de Extranjería" },
  { value: "nit", label: "NIT" },
  { value: "pasaporte", label: "Pasaporte" },
]

interface FormState {
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

type FormAction =
  | { type: "SET_USER_TYPE"; payload: UserType }
  | { type: "SET_FIELD"; field: keyof FormState; value: string }
  | { type: "SET_ERROR"; field: string; error: string }
  | { type: "CLEAR_ERRORS" }
  | { type: "SET_SUBMITTING"; payload: boolean }
  | { type: "RESET_FORM" }

const initialState: FormState = {
  selectedUserType: null,
  documentType: "",
  identification: "",
  firstName: "",
  lastName: "",
  email: "",
  phone: "",
  password: "",
  confirmPassword: "",
  companyName: "",
  companyNit: "",
  address: "",
  errors: {},
  isSubmitting: false,
}

function formReducer(state: FormState, action: FormAction): FormState {
  switch (action.type) {
    case "SET_USER_TYPE":
      return { ...state, selectedUserType: action.payload }
    case "SET_FIELD":
      return {
        ...state,
        [action.field]: action.value,
        errors: { ...state.errors, [action.field]: "" },
      }
    case "SET_ERROR":
      return {
        ...state,
        errors: { ...state.errors, [action.field]: action.error },
      }
    case "CLEAR_ERRORS":
      return { ...state, errors: {} }
    case "SET_SUBMITTING":
      return { ...state, isSubmitting: action.payload }
    case "RESET_FORM":
      return initialState
    default:
      return state
  }
}


export default function Register(){ 
  const [state, dispatch] = useReducer(formReducer, initialState)

  const firstNameRef = useRef<HTMLInputElement>(null)
  const lastNameRef = useRef<HTMLInputElement>(null)
  const emailRef = useRef<HTMLInputElement>(null)
  const phoneRef = useRef<HTMLInputElement>(null)
  const identificationRef = useRef<HTMLInputElement>(null)
  const passwordRef = useRef<HTMLInputElement>(null)
  const confirmPasswordRef = useRef<HTMLInputElement>(null)
  const companyNameRef = useRef<HTMLInputElement>(null)
  const companyNitRef = useRef<HTMLInputElement>(null)
  const addressRef = useRef<HTMLInputElement>(null)

  useEffect(() => {
    if (state.selectedUserType && firstNameRef.current) {
      firstNameRef.current.focus()
    }
  }, [state.selectedUserType])

  const handleUserTypeSelect = (userType: UserType) => {
    dispatch({ type: "SET_USER_TYPE", payload: userType })
  }

  const handleBack = () => {
    dispatch({ type: "RESET_FORM" })
  }

  const validateForm = (): boolean => {
    dispatch({ type: "CLEAR_ERRORS" })
    let isValid = true

    // Document type validation
    if (!state.documentType) {
      dispatch({ type: "SET_ERROR", field: "documentType", error: "El tipo de documento es requerido" })
      isValid = false
    }

    // Basic field validation
    if (!state.firstName.trim()) {
      dispatch({ type: "SET_ERROR", field: "firstName", error: "El nombre es requerido" })
      firstNameRef.current?.focus()
      isValid = false
    }

    if (!state.lastName.trim()) {
      dispatch({ type: "SET_ERROR", field: "lastName", error: "El apellido es requerido" })
      if (isValid) lastNameRef.current?.focus()
      isValid = false
    }

    if (!state.email.trim()) {
      dispatch({ type: "SET_ERROR", field: "email", error: "El email es requerido" })
      if (isValid) emailRef.current?.focus()
      isValid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.email)) {
      dispatch({ type: "SET_ERROR", field: "email", error: "Email inválido" })
      if (isValid) emailRef.current?.focus()
      isValid = false
    }

    if (!state.identification.trim()) {
      dispatch({ type: "SET_ERROR", field: "identification", error: "La identificación es requerida" })
      if (isValid) identificationRef.current?.focus()
      isValid = false
    }

    if (!state.password.trim()) {
      dispatch({ type: "SET_ERROR", field: "password", error: "La contraseña es requerida" })
      if (isValid) passwordRef.current?.focus()
      isValid = false
    } else if (state.password.length < 6) {
      dispatch({ type: "SET_ERROR", field: "password", error: "La contraseña debe tener al menos 6 caracteres" })
      if (isValid) passwordRef.current?.focus()
      isValid = false
    }

    if (state.password !== state.confirmPassword) {
      dispatch({ type: "SET_ERROR", field: "confirmPassword", error: "Las contraseñas no coinciden" })
      if (isValid) confirmPasswordRef.current?.focus()
      isValid = false
    }

    // Company-specific validation
    if (state.selectedUserType === "empresa") {
      if (!state.companyName.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyName", error: "El nombre de la empresa es requerido" })
        if (isValid) companyNameRef.current?.focus()
        isValid = false
      }
      if (!state.companyNit.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyNit", error: "El NIT de la empresa es requerido" })
        if (isValid) companyNitRef.current?.focus()
        isValid = false
      }
    }

    return isValid
  }

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!validateForm()) {
      return
    }

    dispatch({ type: "SET_SUBMITTING", payload: true })

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 2000))

      // Log registration data for development
      console.log("[v0] Registration data:", {
        userType: state.selectedUserType,
        personalInfo: {
          firstName: state.firstName,
          lastName: state.lastName,
          email: state.email,
          phone: state.phone,
        },
        document: {
          type: state.documentType,
          number: state.identification,
        },
        ...(state.selectedUserType === "empresa" && {
          company: {
            name: state.companyName,
            nit: state.companyNit,
          },
        }),
        address: state.address,
      })

      // Show success message and redirect
      alert("¡Registro exitoso! Serás redirigido al login.")

      // Reset form and redirect to login
      dispatch({ type: "RESET_FORM" })
      window.location.href = "/"
    } catch (error) {
      console.error("Registration error:", error)
      alert("Error en el registro. Por favor intenta nuevamente.")
    } finally {
      dispatch({ type: "SET_SUBMITTING", payload: false })
    }
  }

  const isCompanyType = state.selectedUserType === "empresa"

  return (
    <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
      {/* Left Panel - Welcome Section */}
      <AuthWelcome
        title="REGISTRO"
        tagline="Únete a Value Aims"
        description="Crea tu cuenta para acceder a todos los servicios y beneficios que Value Aims tiene para ofrecerte. Un proceso simple y seguro para comenzar tu experiencia."
        backHref={route('login')}
        backText="¿Ya tienes cuenta? Inicia sesión"
      />

      {/* Right Panel - Registration Form */}
      <div className="lg:w-1/2 p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px]">
        <div className="absolute top-6 right-6 w-16 h-16 bg-gradient-to-br from-emerald-200 to-teal-300 rounded-2xl opacity-70"></div>
        <div className="absolute bottom-6 right-12 w-8 h-8 bg-gradient-to-tr from-emerald-300 to-green-400 rounded-lg opacity-50"></div>
        <div className="absolute top-1/3 left-6 w-12 h-12 bg-gradient-to-bl from-teal-200 to-emerald-200 rounded-full opacity-40"></div>

        <div className="max-w-md mx-auto w-full">
          {!state.selectedUserType ? (
              <AuthUserTypeSelector
                title="Crear cuenta"
                subtitle="Selecciona tu tipo de usuario"
                logoSrc={imageLogo}
                logoAlt="Comfaca Logo"
                userTypes={userTypes}
                onSelect={(id) => handleUserTypeSelect(id)}
              />
          ) : (
            <RegisterForm
              userTypeLabel={userTypes.find((ut) => ut.id === state.selectedUserType)?.label || ""}
              values={{
                documentType: state.documentType,
                identification: state.identification,
                firstName: state.firstName,
                lastName: state.lastName,
                email: state.email,
                phone: state.phone,
                password: state.password,
                confirmPassword: state.confirmPassword,
                companyName: state.companyName,
                companyNit: state.companyNit,
                address: state.address,
              }}
              errors={state.errors}
              isSubmitting={state.isSubmitting}
              isCompanyType={isCompanyType}
              documentTypes={documentTypes}
              onBack={handleBack}
              onChange={(field, value) =>
                dispatch({ type: "SET_FIELD", field: field as keyof FormState, value })
              }
              onSubmit={handleRegister}
              firstNameRef={firstNameRef}
              lastNameRef={lastNameRef}
              emailRef={emailRef}
              phoneRef={phoneRef}
              identificationRef={identificationRef}
              passwordRef={passwordRef}
              confirmPasswordRef={confirmPasswordRef}
              companyNameRef={companyNameRef}
              companyNitRef={companyNitRef}
              addressRef={addressRef}
            />
          )}
        </div>
      </div>
    </AuthLayout>
  )
}
