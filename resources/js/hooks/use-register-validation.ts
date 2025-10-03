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

    const isCompany = state.selectedUserType === "empresa"
    const isWorker = state.selectedUserType === "trabajador"
    const isIndependent = state.selectedUserType === "independiente"
    const isPensioner = state.selectedUserType === "pensionado"

    // Paso 1 (empresa): datos de empresa
    if (isCompany && step === 1) {
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
      if (!state.documentType) {
        dispatch({ type: "SET_ERROR", field: "documentType", error: "El tipo de documento de la empresa es requerido" })
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

    // Paso 2 (Trabajador): datos de empresa (nit, razón social, cargo)
    if (isWorker && step === 2) {
      const reasons: string[] = []
      if (!state.companyNit?.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyNit", error: "El NIT de la empresa es requerido" })
        reasons.push('companyNit')
        companyNitRef.current?.focus()
        isValid = false
      }
      if (!state.companyName?.trim()) {
        dispatch({ type: "SET_ERROR", field: "companyName", error: "La razón social es requerida" })
        reasons.push('companyName')
        if (isValid) companyNameRef.current?.focus()
        isValid = false
      }
      if (!state.position?.trim()) {
        dispatch({ type: "SET_ERROR", field: "position", error: "El cargo es requerido" })
        reasons.push('position')
        isValid = false
      }
      if (reasons.length) {
        console.debug('[validación] Paso 2 (Trabajador) inválido ->', reasons)
      }
      return isValid
    }

    // Paso personales: empresa paso 2, otros (incluye trabajador) paso 1
    if ((isCompany && step === 2) || (!isCompany && step === 1)) {
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
      if (!state.city) {
        dispatch({ type: "SET_ERROR", field: "city", error: "La ciudad es requerida" })
        isValid = false
      }
      if (isCompany && !state.userRole) {
        dispatch({ type: "SET_ERROR", field: "userRole", error: "Debes indicar si eres representante o delegado" })
        isValid = false
      }
      // Empresa Natural no puede tener delegado
      if (isCompany && state.companyCategory === 'N' && state.userRole === 'delegado') {
        dispatch({ type: "SET_ERROR", field: "userRole", error: "Para persona natural no aplica la opción de delegado" })
        isValid = false
      }
      if (isCompany && state.userRole === 'delegado' && !state.position.trim()) {
        dispatch({ type: "SET_ERROR", field: "position", error: "El cargo u ocupación es requerido para delegados" })
        isValid = false
      }
      // Independiente/Pensionado: contributionRate requerido en paso 1
      if (!isCompany && (isIndependent || isPensioner)) {
        if (!state.contributionRate) {
          dispatch({ type: "SET_ERROR", field: "contributionRate", error: "Selecciona la tasa de contribución" })
          isValid = false
        }
      }
      return isValid
    }

    // Paso 3 adicional para empresas con delegado: datos del representante
    if (isCompany && state.userRole === 'delegado' && step === 3) {
      if (!state.repName?.trim()) {
        dispatch({ type: "SET_ERROR", field: "repName", error: "El nombre del representante es requerido" })
        isValid = false
      }
      if (!state.repIdentification?.trim()) {
        dispatch({ type: "SET_ERROR", field: "repIdentification", error: "La identificación del representante es requerida" })
        isValid = false
      }
      if (!state.repEmail?.trim()) {
        dispatch({ type: "SET_ERROR", field: "repEmail", error: "El email del representante es requerido" })
        isValid = false
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.repEmail)) {
        dispatch({ type: "SET_ERROR", field: "repEmail", error: "Email del representante inválido" })
        isValid = false
      }
      if (!state.repPhone?.trim()) {
        dispatch({ type: "SET_ERROR", field: "repPhone", error: "El teléfono del representante es requerido" })
        isValid = false
      }
      return isValid
    }

    // Paso sesión: empresa (paso 3 normal o paso 4 si es delegado), trabajador (paso 3), otros (paso 2)
    if (
      (isCompany && ((state.userRole === 'delegado' && step === 4) || (state.userRole !== 'delegado' && step === 3)))
      || (isWorker && step === 3)
      || (!isCompany && !isWorker && step === 2)
    ) {
      // En empresa el tipo de documento de sesión es "documentType"; en otros flujos es "documentTypeUser"
      // Debe coincidir con el payload en handleRegister y con el UI en SessionRegister
      const docTypeField: 'documentType' | 'documentTypeUser' = isCompany ? 'documentType' : 'documentTypeUser'
      const docTypeValue = state[docTypeField]
      if (!docTypeValue) {
        dispatch({ type: "SET_ERROR", field: docTypeField, error: "El tipo de documento es requerido" })
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
      } else {
        const strongPwd = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/
        if (!strongPwd.test(state.password)) {
          dispatch({
            type: "SET_ERROR",
            field: "password",
            error: "La contraseña debe tener mínimo 10 caracteres, incluir 1 mayúscula, 1 número y 1 símbolo"
          })
          if (isValid) passwordRef.current?.focus()
          isValid = false
        }
      }
      if (state.password !== state.confirmPassword) {
        dispatch({ type: "SET_ERROR", field: "confirmPassword", error: "Las contraseñas no coinciden" })
        if (isValid) confirmPasswordRef.current?.focus()
        isValid = false
      }
      return isValid
    }

    return isValid
  }, [dispatch, refs, state, step])

  return { validateStep }
}
