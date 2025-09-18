import type React from "react"
import { useReducer, useRef, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Building2, GraduationCap, Briefcase, Users, Home, HardHat, ChevronLeft } from "lucide-react"
import imageLogo from "../../assets/comfaca-logo.png";
import AuthLayout from "@/layouts/auth-layout";
import TextLink from "@/components/text-link";

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

interface ReactProps {
  status?: string;
  canResetPassword: boolean;
}

export default function Register({ status, canResetPassword }: ReactProps){ 
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
      <div className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
        <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full -translate-y-16 translate-x-16 opacity-60"></div>
        <div className="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full translate-y-12 -translate-x-12 opacity-40"></div>
        <div className="absolute top-1/2 left-0 w-16 h-16 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full -translate-x-8 opacity-30"></div>

        <div className="relative z-10">
          <h1 className="text-4xl font-bold mb-2">REGISTRO</h1>
          <div className="w-16 h-0.5 bg-white mb-6"></div>
          <p className="text-emerald-100 text-lg mb-6">Únete a Value Aims</p>

          <p className="text-emerald-100 text-sm leading-relaxed mb-6">
            Crea tu cuenta para acceder a todos los servicios y beneficios que Value Aims tiene para ofrecerte. Un
            proceso simple y seguro para comenzar tu experiencia.
          </p>

          <TextLink href={route('login')}
            className="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm"
          >
            <ChevronLeft className="w-4 h-4 mr-1" />
            ¿Ya tienes cuenta? Inicia sesión
          </TextLink>
        </div>
      </div>

      {/* Right Panel - Registration Form */}
      <div className="lg:w-1/2 p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px]">
        <div className="absolute top-6 right-6 w-16 h-16 bg-gradient-to-br from-emerald-200 to-teal-300 rounded-2xl opacity-70"></div>
        <div className="absolute bottom-6 right-12 w-8 h-8 bg-gradient-to-tr from-emerald-300 to-green-400 rounded-lg opacity-50"></div>
        <div className="absolute top-1/3 left-6 w-12 h-12 bg-gradient-to-bl from-teal-200 to-emerald-200 rounded-full opacity-40"></div>

        <div className="max-w-md mx-auto w-full">
          {!state.selectedUserType ? (
            <>
            <div className="mb-6 flex justify-center">
            <img
              src={imageLogo}
              alt="Comfaca Logo"
              width={180}
              height={60}
              className="opacity-90"
            />
          </div>
              <h2 className="text-2xl font-semibold text-gray-800 mb-2 text-center">Crear cuenta</h2>
              <p className="text-lg text-gray-600 mb-8 text-center">Selecciona tu tipo de usuario</p>

              <div className="grid grid-cols-3 gap-4 mb-8">
                {userTypes.map((userType) => (
                  <button
                    key={userType.id}
                    onClick={() => handleUserTypeSelect(userType.id)}
                    className="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-emerald-500 hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 transition-all duration-200 group"
                  >
                    <div className="text-emerald-600 group-hover:text-emerald-700 mb-2">{userType.icon}</div>
                    <span className="text-xs text-gray-600 text-center font-medium">{userType.label}</span>
                  </button>
                ))}
              </div>
            </>
          ) : (
            <>
              <div className="flex items-center mb-6">
                <button onClick={handleBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
                  <ChevronLeft className="w-5 h-5 text-gray-600" />
                </button>
                <div>
                  <h2 className="text-xl font-semibold text-gray-800">
                    {userTypes.find((ut) => ut.id === state.selectedUserType)?.label}
                  </h2>
                  <p className="text-sm text-gray-600">Completa tu información</p>
                </div>
              </div>

              <form onSubmit={handleRegister} className="space-y-4">
                {/* Personal Information */}
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="firstName" className="text-sm font-medium text-gray-700">
                      Nombre *
                    </Label>
                    <Input
                      id="firstName"
                      ref={firstNameRef}
                      type="text"
                      value={state.firstName}
                      onChange={(e) => dispatch({ type: "SET_FIELD", field: "firstName", value: e.target.value })}
                      placeholder="Tu nombre"
                      className={`mt-1 ${state.errors.firstName ? "border-red-500" : ""}`}
                    />
                    {state.errors.firstName && (
                      <p className="text-red-500 text-xs mt-1">{state.errors.firstName}</p>
                    )}
                  </div>

                  <div>
                    <Label htmlFor="lastName" className="text-sm font-medium text-gray-700">
                      Apellido *
                    </Label>
                    <Input
                      id="lastName"
                      ref={lastNameRef}
                      type="text"
                      value={state.lastName}
                      onChange={(e) => dispatch({ type: "SET_FIELD", field: "lastName", value: e.target.value })}
                      placeholder="Tu apellido"
                      className={`mt-1 ${state.errors.lastName ? "border-red-500" : ""}`}
                    />
                    {state.errors.lastName && <p className="text-red-500 text-xs mt-1">{state.errors.lastName}</p>}
                  </div>
                </div>

                <div>
                  <Label htmlFor="email" className="text-sm font-medium text-gray-700">
                    Email *
                  </Label>
                  <Input
                    id="email"
                    ref={emailRef}
                    type="email"
                    value={state.email}
                    onChange={(e) => dispatch({ type: "SET_FIELD", field: "email", value: e.target.value })}
                    placeholder="tu@email.com"
                    className={`mt-1 ${state.errors.email ? "border-red-500" : ""}`}
                  />
                  {state.errors.email && <p className="text-red-500 text-xs mt-1">{state.errors.email}</p>}
                </div>

                <div>
                  <Label htmlFor="phone" className="text-sm font-medium text-gray-700">
                    Teléfono
                  </Label>
                  <Input
                    id="phone"
                    ref={phoneRef}
                    type="tel"
                    value={state.phone}
                    onChange={(e) => dispatch({ type: "SET_FIELD", field: "phone", value: e.target.value })}
                    placeholder="Tu número de teléfono"
                    className="mt-1"
                  />
                </div>

                {/* Document Information */}
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="documentType" className="text-sm font-medium text-gray-700">
                      Tipo de documento *
                    </Label>
                    <Select
                      value={state.documentType}
                      onValueChange={(value) => dispatch({ type: "SET_FIELD", field: "documentType", value })}
                    >
                      <SelectTrigger className={`mt-1 ${state.errors.documentType ? "border-red-500" : ""}`}>
                        <SelectValue placeholder="Selecciona" />
                      </SelectTrigger>
                      <SelectContent>
                        {documentTypes.map((doc) => (
                          <SelectItem key={doc.value} value={doc.value}>
                            {doc.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    {state.errors.documentType && (
                      <p className="text-red-500 text-xs mt-1">{state.errors.documentType}</p>
                    )}
                  </div>

                  <div>
                    <Label htmlFor="identification" className="text-sm font-medium text-gray-700">
                      Número *
                    </Label>
                    <Input
                      id="identification"
                      ref={identificationRef}
                      type="text"
                      value={state.identification}
                      onChange={(e) =>
                        dispatch({ type: "SET_FIELD", field: "identification", value: e.target.value })
                      }
                      placeholder="Número de documento"
                      className={`mt-1 ${state.errors.identification ? "border-red-500" : ""}`}
                    />
                    {state.errors.identification && (
                      <p className="text-red-500 text-xs mt-1">{state.errors.identification}</p>
                    )}
                  </div>
                </div>

                {/* Company Information (only for empresa type) */}
                {isCompanyType && (
                  <>
                    <div>
                      <Label htmlFor="companyName" className="text-sm font-medium text-gray-700">
                        Nombre de la empresa *
                      </Label>
                      <Input
                        id="companyName"
                        ref={companyNameRef}
                        type="text"
                        value={state.companyName}
                        onChange={(e) =>
                          dispatch({ type: "SET_FIELD", field: "companyName", value: e.target.value })
                        }
                        placeholder="Nombre de tu empresa"
                        className={`mt-1 ${state.errors.companyName ? "border-red-500" : ""}`}
                      />
                      {state.errors.companyName && (
                        <p className="text-red-500 text-xs mt-1">{state.errors.companyName}</p>
                      )}
                    </div>

                    <div>
                      <Label htmlFor="companyNit" className="text-sm font-medium text-gray-700">
                        NIT de la empresa *
                      </Label>
                      <Input
                        id="companyNit"
                        ref={companyNitRef}
                        type="text"
                        value={state.companyNit}
                        onChange={(e) =>
                          dispatch({ type: "SET_FIELD", field: "companyNit", value: e.target.value })
                        }
                        placeholder="NIT de la empresa"
                        className={`mt-1 ${state.errors.companyNit ? "border-red-500" : ""}`}
                      />
                      {state.errors.companyNit && (
                        <p className="text-red-500 text-xs mt-1">{state.errors.companyNit}</p>
                      )}
                    </div>
                  </>
                )}

                <div>
                  <Label htmlFor="address" className="text-sm font-medium text-gray-700">
                    Dirección
                  </Label>
                  <Input
                    id="address"
                    ref={addressRef}
                    type="text"
                    value={state.address}
                    onChange={(e) => dispatch({ type: "SET_FIELD", field: "address", value: e.target.value })}
                    placeholder="Tu dirección"
                    className="mt-1"
                  />
                </div>

                {/* Password Fields */}
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="password" className="text-sm font-medium text-gray-700">
                      Contraseña *
                    </Label>
                    <Input
                      id="password"
                      ref={passwordRef}
                      type="password"
                      value={state.password}
                      onChange={(e) => dispatch({ type: "SET_FIELD", field: "password", value: e.target.value })}
                      placeholder="Mínimo 6 caracteres"
                      className={`mt-1 ${state.errors.password ? "border-red-500" : ""}`}
                    />
                    {state.errors.password && <p className="text-red-500 text-xs mt-1">{state.errors.password}</p>}
                  </div>

                  <div>
                    <Label htmlFor="confirmPassword" className="text-sm font-medium text-gray-700">
                      Confirmar *
                    </Label>
                    <Input
                      id="confirmPassword"
                      ref={confirmPasswordRef}
                      type="password"
                      value={state.confirmPassword}
                      onChange={(e) =>
                        dispatch({ type: "SET_FIELD", field: "confirmPassword", value: e.target.value })
                      }
                      placeholder="Repite la contraseña"
                      className={`mt-1 ${state.errors.confirmPassword ? "border-red-500" : ""}`}
                    />
                    {state.errors.confirmPassword && (
                      <p className="text-red-500 text-xs mt-1">{state.errors.confirmPassword}</p>
                    )}
                  </div>
                </div>

                <Button
                  type="submit"
                  disabled={state.isSubmitting}
                  className="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg mt-6"
                >
                  {state.isSubmitting ? "Registrando..." : "Crear cuenta"}
                </Button>
              </form>
            </>
          )}
        </div>
      </div>
    </AuthLayout>
  )
}
