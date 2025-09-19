import type React from "react"
import TextLink from "@/components/text-link"
import { useState, useRef, useReducer } from "react"
import { Button } from "@/components/ui/button"
import imageLogo from "../../assets/comfaca-logo.png";
import type { UserType, FormAction, FormBasic } from "@/types/auth";
import { userTypes } from "@/constants/auth"
import { CheckCircle } from "lucide-react"
import AuthWelcome from "./components/auth-welcome"
import AuthUserTypeSelector from "./components/auth-user-type-selector"
import ResetPasswordForm from "./components/reset-password-form"
import AuthLayout from "@/layouts/auth-layout";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";


const initialFormState: FormBasic = {
  documentType: "",
  identification: "",
  email: "",
  errors: {},
  isSubmitting: false,
  isSuccess: false,
}

function formReducer(state: FormBasic, action: FormAction): FormBasic {
  switch (action.type) {
    case "SET_FIELD":
      return { ...state, [action.field]: action.value }
    case "SET_ERROR":
      return { ...state, errors: { ...state.errors, [action.field]: action.error } }
    case "CLEAR_ERRORS":
      return { ...state, errors: {} }
    case "SET_SUBMITTING":
      return { ...state, isSubmitting: action.payload }
    case "SET_SUCCESS":
      return { ...state, isSuccess: action.payload }
    case "RESET_FORM":
      return initialFormState
    default:
      return state
  }
}

export default function ResetPassword() {
  const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null)
  const [formState, dispatch] = useReducer(formReducer, initialFormState)

  const documentTypeRef = useRef<HTMLButtonElement>(null)
  const identificationRef = useRef<HTMLInputElement>(null)
  const emailRef = useRef<HTMLInputElement>(null)

  const handleUserTypeSelect = (userType: UserType) => {
    setSelectedUserType(userType)
    dispatch({ type: "RESET_FORM" })
  }

  const handleBack = () => {
    setSelectedUserType(null)
    dispatch({ type: "RESET_FORM" })
  }

  const validateField = (field: keyof FormBasic["errors"], value: string) => {
    switch (field) {
      case "documentType":
        if (!value) {
          dispatch({ type: "SET_ERROR", field, error: "Selecciona un tipo de documento" })
          return false
        }
        break
      case "identification":
        if (!value) {
          dispatch({ type: "SET_ERROR", field, error: "Ingresa tu número de identificación" })
          return false
        }
        if (value.length < 6) {
          dispatch({ type: "SET_ERROR", field, error: "El número de identificación debe tener al menos 6 caracteres" })
          return false
        }
        break
      case "email":
        if (!value) {
          dispatch({ type: "SET_ERROR", field, error: "Ingresa tu correo electrónico" })
          return false
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
          dispatch({ type: "SET_ERROR", field, error: "Ingresa un correo electrónico válido" })
          return false
        }
        break
    }
    dispatch({ type: "CLEAR_ERROR", field })
    return true
  }

  const handleFieldChange = (field: keyof FormBasic, value: string) => {
    dispatch({ type: "SET_FIELD", field, value })
    if (formState.errors[field as keyof FormBasic["errors"]]) {
      validateField(field as keyof FormBasic["errors"], value)
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    const isDocumentTypeValid = validateField("documentType", formState.documentType)
    const isIdentificationValid = validateField("identification", formState.identification)
    const isEmailValid = validateField("email", formState.email)

    if (!isDocumentTypeValid || !isIdentificationValid || !isEmailValid) {
      // Focus on first invalid field
      if (!isDocumentTypeValid) documentTypeRef.current?.focus()
      else if (!isIdentificationValid) identificationRef.current?.focus()
      else if (!isEmailValid) emailRef.current?.focus()
      return
    }

    dispatch({ type: "SET_SUBMITTING", payload: true })

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 2000))

      console.log("[v0] Password reset request:", {
        userType: selectedUserType,
        documentType: formState.documentType,
        identification: formState.identification.substring(0, 3) + "***",
        email: formState.email,
      })

      dispatch({ type: "SET_SUCCESS", payload: true })
    } catch (error) {
      console.error("[v0] Password reset error:", error)
    } finally {
      dispatch({ type: "SET_SUBMITTING", payload: false })
    }
  }

  if (formState.isSuccess) {
    return (
      <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
      <div className="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 text-center">
        <div className="mb-6">
          <CheckCircle className="w-16 h-16 text-emerald-600 mx-auto mb-4" />
          <h2 className="text-2xl font-semibold text-gray-800 mb-2">¡Solicitud enviada!</h2>
          <p className="text-gray-600">
            Hemos enviado las instrucciones para restablecer tu clave a tu correo electrónico.
          </p>
        </div>
        <div className="space-y-4">
          <TextLink href={route("login")}>
            <Button className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white">
              Volver al inicio de sesión
            </Button>
          </TextLink>
        </div>
      </div>
      </AuthLayout>
    )
  }

  return (
    <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
     
          {/* Left Panel - Welcome Section */}
            <AuthWelcome
              title="RECUPERAR"
              tagline="Comfaca En Línea"
              description="Ingresa tu información para recibir las instrucciones de recuperación de clave en tu correo electrónico."
              backHref={route('login')}
              backText="¿Ya tienes cuenta? Inicia sesión"
            />
          {/* Right Panel - Forgot Password Form */}
          <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
          <AuthBackgroundShapes />
            <div className="max-w-md mx-auto w-full">
              {!selectedUserType ? (
                <AuthUserTypeSelector
                  title="Recuperar clave"
                  subtitle="Selecciona tu tipo de usuario"
                  logoSrc={imageLogo}
                  logoAlt="Comfaca Logo"
                  userTypes={userTypes}
                  onSelect={(id) => handleUserTypeSelect(id)}
                />
              ) : (
                <ResetPasswordForm
                  selectedUserType={selectedUserType}
                  formState={{
                    documentType: formState.documentType,
                    identification: formState.identification,
                    email: formState.email,
                    errors: formState.errors as Record<string, string>,
                    isSubmitting: formState.isSubmitting,
                  }}
                  onBack={handleBack}
                  onFieldChange={(field, value) => handleFieldChange(field as keyof FormBasic, value)}
                  onSubmit={handleSubmit}
                  documentTypeRef={documentTypeRef}
                  identificationRef={identificationRef}
                  emailRef={emailRef}
                  loginHref={route("login")}
                />
              )}
            </div>
          </div>
    </AuthLayout>
  )
}
