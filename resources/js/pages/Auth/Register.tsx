import type React from "react"
import { useReducer, useRef, useEffect, useState, useMemo } from "react"
import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/pages/Auth/components/auth-welcome";
import AuthUserTypeSelector from "@/pages/Auth/components/auth-user-type-selector";
import CompanyRegisterForm from "@/pages/Auth/components/company-register-form";
import PersonRegisterForm from "@/pages/Auth/components/person-register-form";
import imageLogo from "../../assets/comfaca-logo.png";
import { useRegisterValidation } from "@/hooks/use-register-validation";
import { TipoFuncionario, userTypes } from "@/constants/auth";
import type { UserType, FormState, FormAction, LoginProps } from "@/types/auth";
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
  city: "",
  societyType: "",
  companyCategory: "",
  userRole: "",
  position: "",
  errors: {},
  isSubmitting: false,
  isSuccess: false
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

export default function Register({
    Coddoc,
    Tipsoc,
    Codciu,
    Detadoc
}: LoginProps){
  const [state, dispatch] = useReducer(formReducer, initialState)
  const [step, setStep] = useState(1)
  const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null)

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

  const documentTypeOptions = useMemo(
    () => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label })),
    [Coddoc]
  )

  // Opciones de ciudades mapeadas desde Codciu
  const cityOptions = useMemo(
    () => Object.entries(Codciu || {}).map(([value, label]) => ({ value, label })),
    [Codciu]
  )

  // Opciones de tipos de sociedad desde Tipsoc
  const societyOptions = useMemo(
    () => Object.entries(Tipsoc || {}).map(([value, label]) => ({ value, label })),
    [Tipsoc]
  )

  // Opciones de categoría de empresa (Natural/Jurídica)
  const companyCategoryOptions = useMemo(
    () => [
      { value: 'N', label: 'NATURAL' },
      { value: 'J', label: 'JURIDICA' },
    ],
    []
  )

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

  // Navegación entre pasos usando validación
  const handleNextStep = () => {
    const maxSteps = state.selectedUserType === "empresa" ? 3 : 2
    if (validateStep()) {
      setStep((prev) => Math.min(prev + 1, maxSteps))
    }
  }

  const handlePrevStep = () => {
    setStep((prev) => Math.max(prev - 1, 1))
  }

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!validateStep()) {
      return
    }
    dispatch({ type: "SET_SUBMITTING", payload: true })
    try {
      const tipoValue = TipoFuncionario[state.selectedUserType as keyof typeof TipoFuncionario];

      // Mapeo de campos a las propiedades esperadas por el backend
      const payload = {
        tipo: tipoValue,
        // Sesión
        coddoc: state.documentType,
        documento: state.identification,
        password: state.password,
        // Empresa
        razsoc: state.companyName,
        nit: state.companyNit,
        tipsoc: state.societyType,
        tipper: state.companyCategory,
        // Personales
        nombre: `${state.firstName} ${state.lastName}`.trim(),
        email: state.email,
        telefono: state.phone,
        codciu: state.city,
        // Delegado/Representante
        is_delegado: state.userRole === 'delegado',
        cargo: state.userRole === 'delegado' ? state.position : null,
      }
      console.debug('Payload de registro (previo a envío):', payload)

      const response = await fetch(route('api.register'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(payload)
      })

      const data = await response.json()

      if (response.ok && data?.success) {
        setToast({ message: '¡Registro exitoso! Serás redirigido al login.', type: 'success' })
        dispatch({ type: 'RESET_FORM' })
        setStep(1)
        setTimeout(() => { window.location.href = '/' }, 1500)
      } else {
        console.error('Error al registrar:', data)
        setToast({ message: typeof data?.message === 'string' ? data.message : 'No fue posible completar el registro.', type: 'error' })
      }
    } catch (error) {
      console.log("Error en el registro. Por favor intenta nuevamente.", error)
      setToast({ message: 'No fue posible completar el registro. Intenta nuevamente.', type: 'error' })
    } finally {
      dispatch({ type: "SET_SUBMITTING", payload: false })
    }
  }

  const isCompanyType = state.selectedUserType === "empresa"

  return (
    <>
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
            state.selectedUserType === 'empresa' ? (
              <CompanyRegisterForm
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
                  city: state.city,
                  societyType: state.societyType,
                  companyCategory: state.companyCategory,
                  userRole: state.userRole,
                  position: state.position,
                }}
                errors={state.errors}
                isSubmitting={state.isSubmitting}
                documentTypes={documentTypeOptions}
                cityOptions={cityOptions}
                societyOptions={societyOptions}
                categoryOptions={companyCategoryOptions}
                onBack={handleBack}
                onChange={(field, value) =>
                  dispatch({ type: "SET_FIELD", field: field as keyof FormState, value })
                }
                onSubmit={handleRegister}
                step={step}
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
            ) : (
              <PersonRegisterForm
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
                  city: state.city,
                  societyType: state.societyType,
                  companyCategory: state.companyCategory,
                  userRole: state.userRole,
                  position: state.position,
                }}
                errors={state.errors}
                isSubmitting={state.isSubmitting}
                documentTypes={documentTypeOptions}
                cityOptions={cityOptions}
                onBack={handleBack}
                onChange={(field, value) =>
                  dispatch({ type: "SET_FIELD", field: field as keyof FormState, value })
                }
                onSubmit={handleRegister}
                step={step}
                onNextStep={handleNextStep}
                onPrevStep={handlePrevStep}
                firstNameRef={firstNameRef}
                lastNameRef={lastNameRef}
                emailRef={emailRef}
                phoneRef={phoneRef}
                identificationRef={identificationRef}
                passwordRef={passwordRef}
                confirmPasswordRef={confirmPasswordRef}
              />
            )
          )}
        </div>
      </div>
    </AuthLayout>

    {/* Toast simple */}
    {toast && (
      <div
        className={`fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`}
      >
        {toast.message}
        <button
          type="button"
          className="ml-3 underline text-white/90 hover:text-white"
          onClick={() => setToast(null)}
        >
          Cerrar
        </button>
      </div>
    )}
    </>
  )
}
