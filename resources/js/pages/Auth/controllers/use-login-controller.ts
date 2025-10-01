import type React from "react"
import { useState, useMemo } from "react"
import { TipoFuncionario } from "@/constants/auth"
import type {  UserType, LoginProps } from "@/types/auth"
import { router } from '@inertiajs/react';

const useLoginController = ({
  Coddoc,
}: LoginProps ) => {
  const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null)
  const [documentType, setDocumentType] = useState("")
  const [identification, setIdentification] = useState("")
  const [password, setPassword] = useState("")
  const [processing, setProcessing] = useState(false);
  // Estado para mostrar mensajes de error en un Alert
  const [alertMessage, setAlertMessage] = useState<string | null>(null)

  // Mapea Coddoc ({ [codigo]: descripcion }) a opciones { value, label } esperadas por LoginForm
  // Uso de useMemo para cumplir con buenas prácticas de rendimiento
  const documentTypeOptions = useMemo(
    () => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label })),
    [Coddoc]
  )

  const handleUserTypeSelect = (userType: UserType) => {
    setSelectedUserType(userType)
  }

  const handleBack = () => {
    setSelectedUserType(null)
    setDocumentType("")
    setIdentification("")
    setPassword("")
    // Limpiar alertas al volver atrás
    setAlertMessage(null)
  }

  const handleLogin = async (e: React.FormEvent) => {
      e.preventDefault();
      setProcessing(true);
      // Reiniciar cualquier alerta previa antes de intentar login
      setAlertMessage(null)

      const tipoValue = TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario];

      // Usar el flujo WEB: el backend creará la sesión y redirigirá con Inertia
      router.post(route('login.authenticate'), {
        documentType,
        password,
        identification: identification ? parseInt(identification) : null,
        tipo: tipoValue,
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          // El backend redirige según el tipo; no es necesario manejar aquí
        },
        onError: (errors) => {
          // Puede contener errores de validación; mostrar mensaje genérico o detallar si es necesario
          console.error('Error de autenticación:', errors);
          setAlertMessage('No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.');
        },
        onFinish: () => setProcessing(false),
      });
  };

  return {
    documentTypeOptions,
    selectedUserType,
    handleUserTypeSelect,
    handleBack,
    handleLogin,
    processing,
    alertMessage,
    documentType,
    identification,
    password,
    setDocumentType,
    setIdentification,
    setPassword,
  }
}

export default useLoginController
