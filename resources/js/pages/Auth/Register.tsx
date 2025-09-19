import type React from "react"
import { useReducer, useRef, useEffect, useState } from "react"
import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/pages/Auth/components/auth-welcome";
import AuthUserTypeSelector from "@/pages/Auth/components/auth-user-type-selector";
import RegisterForm from "@/pages/Auth/components/register-form";
import imageLogo from "../../assets/comfaca-logo.png";
import { useRegisterValidation } from "@/hooks/use-register-validation";
import { userTypes, documentTypes } from "@/constants/auth";
import type { UserType, FormState, FormAction } from "@/types/auth";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";

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
  const [step, setStep] = useState(1)

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

  // Hook de validación extraído para reducir lógica en el componente
  const { validateStep } = useRegisterValidation({
    state,
    step,
    dispatch,
    refs: {
      firstNameRef,
      lastNameRef,
      emailRef,
      identificationRef,
      passwordRef,
      confirmPasswordRef,
      companyNameRef,
      companyNitRef,
    },
  })

  const handleUserTypeSelect = (userType: UserType) => {
    dispatch({ type: "SET_USER_TYPE", payload: userType })
    setStep(1)
  }

  const handleBack = () => {
    if (state.selectedUserType === "empresa" && step === 2) {
      setStep(1)
    } else {
      dispatch({ type: "RESET_FORM" })
      setStep(1)
    }
  }

  // Validación por paso delegada al hook (mantiene la API anterior)

  const handleNextStep = () => {
    if (validateStep()) {
      setStep(2)
    }
  }

  const handlePrevStep = () => {
    setStep(1)
  }

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!validateStep()) {
      return
    }
    dispatch({ type: "SET_SUBMITTING", payload: true })
    try {
      await new Promise((resolve) => setTimeout(resolve, 2000))
      alert("¡Registro exitoso! Serás redirigido al login.")
      dispatch({ type: "RESET_FORM" })
      setStep(1)
      window.location.href = "/"
    } catch (error) {
      console.log("Error en el registro. Por favor intenta nuevamente.", error)
    } finally {
      dispatch({ type: "SET_SUBMITTING", payload: false })
    }
  }

  const isCompanyType = state.selectedUserType === "empresa"

  return (
    <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
      <AuthWelcome
        title="REGISTRO"
        tagline="Únete a Comfaca En Línea"
        description="Crea tu cuenta para acceder a todos los servicios y beneficios que Comfaca tiene para ofrecerte. Un proceso simple y seguro para comenzar tu experiencia."
        backHref={route('login')}
        backText="¿Ya tienes cuenta? Inicia sesión"
      />
      <div className="lg:w-1/2 p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px]">
        <AuthBackgroundShapes />
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
              step={isCompanyType ? step : 2}
              onNextStep={handleNextStep}
              onPrevStep={handlePrevStep}
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
