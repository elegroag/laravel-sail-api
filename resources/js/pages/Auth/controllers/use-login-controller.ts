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
      try {
        const tipoValue = TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario];

        const response = await fetch(route('api.authenticate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
              documentType,
              password,
              identification: identification ? parseInt(identification) : null,
              tipo: tipoValue,
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            router.visit('/web/login');
        } else {
            // Mostrar mensaje proveniente de la API si está disponible
            const msg = (typeof data?.message === 'string' && data.message.trim().length > 0)
              ? data.message
              : 'Ocurrió un error al iniciar sesión. Intenta nuevamente.'
            const detail = data?.errors;
            setAlertMessage(msg + (detail ? '\n' + JSON.stringify(detail) : ''));
            if (data?.errors) {
              console.error(data.errors);
            } else {
              console.error('Error desconocido:', data);
            }
        }
      } catch (error) {
          // Captura de excepciones de red/u otras y alerta genérica
          console.error('Error al iniciar sesión:', error);
          setAlertMessage('No fue posible conectar con el servidor. Intenta nuevamente.');
      } finally {
          setProcessing(false);
      }
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
