import { MutableRefObject, RefObject, useCallback } from "react"
import type { FormAction, FormState } from "@/types/auth"

// Tipos del formulario reutilizados aquí para SOLID (separación de responsabilidades)
// Tipos importados desde '@/types/register'

// Tipo flexible para refs de inputs (acepta RefObject o MutableRefObject con null)
type InputRef = RefObject<HTMLInputElement | null> | MutableRefObject<HTMLInputElement | null>

interface Refs {
  firstNameRef: InputRef
  lastNameRef: InputRef
  emailRef: InputRef
  identificationRef: InputRef
  passwordRef: InputRef
  confirmPasswordRef: InputRef
  companyNameRef: InputRef
  companyNitRef: InputRef
}

interface UseRegisterValidationParams {
  state: FormState
  step: number
  refs: Refs
  dispatch: (action: FormAction) => void
}

// Hook de validación: encapsula la lógica por paso y dispara errores en el estado
// Comentarios breves en español para mantener claridad y bajo acoplamiento
export function useRegisterValidation({ state, step, refs, dispatch }: UseRegisterValidationParams) {
  const validateStep = useCallback((): boolean => {
    const {
      firstNameRef,
      lastNameRef,
      emailRef,
      identificationRef,
      passwordRef,
      confirmPasswordRef,
      companyNameRef,
      companyNitRef,
    } = refs

    dispatch({ type: "CLEAR_ERRORS" })
    let isValid = true

    // Validación específica para empresas en paso 1
    if (state.selectedUserType === "empresa" && step === 1) {
      if (!state.companyName.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyName", error: "El nombre de la empresa es requerido" })
        companyNameRef.current?.focus()
        isValid = false
      }
      if (!state.companyNit.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyNit", error: "El NIT de la empresa es requerido" })
        if (isValid) companyNitRef.current?.focus()
        isValid = false
      }
      if (!state.societyType) {
        dispatch({ type: "SET_ERROR", field: "societyType", error: "El tipo de sociedad es requerido" })
        isValid = false
      }
      if (!state.companyCategory) {
        dispatch({ type: "SET_ERROR", field: "companyCategory", error: "La categoría de empresa es requerida" })
        isValid = false
      }
      return isValid
    }

    // Validación global (paso 2 u otros tipos de usuario)
    if (!state.documentType) {
      dispatch({ type: "SET_ERROR", field: "documentType", error: "El tipo de documento es requerido" })
      isValid = false
    }

    // Validación de select: ciudad (aplica en paso 2 / usuarios no empresa)
    if (!state.city) {
      dispatch({ type: "SET_ERROR", field: "city", error: "La ciudad es requerida" })
      isValid = false
    }

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

    return isValid
  }, [dispatch, refs, state, step])

  return { validateStep }
}
