import type React from "react"
import { useReducer, useRef, useEffect, useState, useMemo } from "react"
import { useRegisterValidation } from "@/hooks/use-register-validation";
import { TipoFuncionario } from "@/constants/auth";
import type { UserType, FormState, FormAction, RegisterPayload, LoginProps } from "@/types/auth";
import { router } from "@inertiajs/react";


const initialState: FormState = {
  selectedUserType: null,
  documentType: "",
  documentTypeUser: "",
  documentTypeRep: "",
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
  repName: "",
  repIdentification: "",
  repEmail: "",
  repPhone: "",
  contributionRate: "",
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

const useRegisterController = ({
    Coddoc,
    Tipsoc,
    Codciu,
    errors
}:LoginProps)  => {
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
  
    useEffect(() => {
      if (!errors || Object.keys(errors).length === 0) {
        return
      }
  
      dispatch({ type: "CLEAR_ERRORS" })
  
      Object.entries(errors).forEach(([field, message]) => {
        dispatch({ type: "SET_ERROR", field, error: message })
      })
  
      const detalleErrores = Object.entries(errors)
        .map(([field, message]) => `${field}: ${message}`)
        .join(" | ")
  
      setToast({
        message: `Se detectaron errores en el registro. ${detalleErrores}`,
        type: 'error'
      })
    }, [errors, dispatch])
  
    const handleUserTypeSelect = (userType: UserType) => {
      dispatch({ type: "SET_USER_TYPE", payload: userType })
      // Reiniciar tasa de contribución al cambiar entre tipos para evitar selecciones previas
      dispatch({ type: "SET_FIELD", field: "contributionRate", value: "" })
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
      const isCompany = state.selectedUserType === "empresa"
      const isWorker = state.selectedUserType === "trabajador"
      const maxSteps = isCompany ? (state.userRole === 'delegado' ? 4 : 3) : (isWorker ? 3 : 2)
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
  
        const isCompany = state.selectedUserType === 'empresa'
        const isWorker = state.selectedUserType === 'trabajador'
        const isIndependent = state.selectedUserType === 'independiente'
        const isPensioner = state.selectedUserType === 'pensionado'
  
        // Mapeo de campos a las propiedades esperadas por el backend
        const payload: RegisterPayload = {
          selected_user_type: state.selectedUserType,
          tipo: tipoValue,
          // Sesión
          coddoc: state.selectedUserType === 'empresa' ? state.documentType : state.documentTypeUser,
          documento: state.identification,
          password: state.password,
          // Empresa (si aplica)
          tipdoc: state.documentType || undefined,
          razsoc: state.companyName || undefined,
          nit: state.companyNit || undefined,
          tipsoc: state.societyType || undefined,
          tipper: state.companyCategory || undefined,
          // Personales
          nombre: `${state.firstName} ${state.lastName}`.trim(),
          email: state.email,
          telefono: Number(state.phone),
          codciu: Number(state.city),
          first_name: state.firstName,
          last_name: state.lastName,
          rep_nombre: '',
          rep_documento: '',
          rep_email: '',
          rep_telefono: Number(state.phone),
          rep_coddoc: '',
          cargo: '',
        }
  
        // Delegado/Representante (empresa)
        if (isCompany) {
          payload.is_delegado = state.userRole === 'delegado'
          payload.cargo = state.userRole === 'delegado' ? state.position : undefined
          if (state.userRole === 'delegado') {
            payload.rep_nombre = state.repName || undefined
            payload.rep_documento = state.repIdentification || undefined
            payload.rep_email = state.repEmail || undefined
            payload.rep_telefono = Number(state.repPhone) || undefined
            payload.rep_coddoc = state.documentTypeRep || undefined
          }
        }
  
        // Trabajador: también enviar cargo si fue diligenciado
        if (isWorker && state.position) {
          payload.cargo = state.position
        }
  
        // Independiente / Pensionado: tasa de contribución
        if ((isIndependent || isPensioner) && state.contributionRate) {
          // Nombre del campo genérico para tasa de contribución
          payload.contribution_rate = state.contributionRate
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
  
        const responseJson = await response.json()
  
        if (response.ok && responseJson?.success) {
          setToast({ message: '¡Registro exitoso! Serás redirigido al login.', type: 'success' })
          dispatch({ type: 'RESET_FORM' })
          setStep(1)
          setTimeout(() => {
            router.visit(route('verify.show', {
              tipo: responseJson.data.tipo,
              coddoc: responseJson.data.coddoc,
              documento: responseJson.data.documento,
            }));
          }, 1000);
        } else {
          console.error('Error al registrar:', responseJson)
          setToast({ message: typeof responseJson?.message === 'string' ? responseJson.message : 'No fue posible completar el registro.', type: 'error' })
        }
      } catch (error) {
        console.log("Error en el registro. Por favor intenta nuevamente.", error)
        setToast({ message: 'No fue posible completar el registro. Intenta nuevamente.', type: 'error' })
      } finally {
        dispatch({ type: "SET_SUBMITTING", payload: false })
      }
    }

    return {
        dispatch, 
        state,
        toast,
        setToast,
        step,
        validateStep,
        domRef:{
          firstNameRef,
          lastNameRef,
          emailRef,
          phoneRef,
          identificationRef,
          passwordRef,
          confirmPasswordRef,
          companyNameRef,
          companyNitRef,
          addressRef,
        },
        events: {
          handleBack,
          handleNextStep,
          handlePrevStep,
          handleRegister,
          handleUserTypeSelect
        },
        collections:{
          documentTypeOptions,
          cityOptions,
          societyOptions,
          companyCategoryOptions,
        }
    }
}

export default useRegisterController
