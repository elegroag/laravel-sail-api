import { TipoFuncionario } from '@/constants/auth';
import type { DocumentTypeOption, LoginProps, UserType } from '@/types/auth';
import { router } from '@inertiajs/react';
import type React from 'react';
import { useEffect, useMemo, useState } from 'react';

const useLoginController = ({ errors }: LoginProps) => {
    const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null);
    const [documentType, setDocumentType] = useState('');
    const [identification, setIdentification] = useState('');
    const [password, setPassword] = useState('');
    const [processing, setProcessing] = useState(false);
    const [Coddoc, setCoddoc] = useState<Record<string, string>>({});
    // Estado para mostrar mensajes de error en un Alert
    const [alertMessage, setAlertMessage] = useState<string | null>(null);

    useEffect(() => {
        if (errors?.message) {
            setAlertMessage(errors.message);
        }
        loadParams(setCoddoc);
    }, [errors, setCoddoc]);

    const documentTypeOptions: DocumentTypeOption[] = useMemo(
        () => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label }) as DocumentTypeOption),
        [Coddoc],
    );

    useEffect(() => {
        const first = Object.keys(Coddoc || {})[0];
        if (!documentType && first) {
            setDocumentType(first);
        }
    }, [Coddoc, documentType]);

    const handleUserTypeSelect = (userType: UserType) => {
        setSelectedUserType(userType);
    };

    const handleBack = () => {
        setSelectedUserType(null);
        setDocumentType('');
        setIdentification('');
        setPassword('');
        // Limpiar alertas al volver atrás
        setAlertMessage(null);
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        // Reiniciar cualquier alerta previa antes de intentar login
        setAlertMessage(null);

        const tipoValue = TipoFuncionario[selectedUserType as keyof typeof TipoFuncionario];

        router.post(
            route('login.authenticate'),
            {
                documentType,
                password,
                identification: identification ? parseInt(identification) : null,
                tipo: tipoValue,
            },
            {
                onSuccess: (response) => {
                    console.log('Response', response);
                },
                onError: (errors) => {
                    console.error('Error de autenticación:', errors);
                    setAlertMessage('No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.');
                },
                onFinish: () => setProcessing(false),
            },
        );
    };

    const loadParams = async (setCoddoc: React.Dispatch<React.SetStateAction<Record<string, string>>>) => {
        try {
            const response = await fetch(route('login.params'), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            const responseJson = await response.json();
            if (response.ok) {
                setCoddoc(responseJson?.Coddoc ?? {});
            } else {
                console.error('Error al cargar parámetros de login:', responseJson);
                setAlertMessage(responseJson?.message);
            }
        } catch (error) {
            console.error('Error al cargar parámetros de login:', error);
            setAlertMessage('No fue posible cargar los parámetros de login. Verifique su conexión e intente nuevamente.');
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
        loadParams,
    };
};

export default useLoginController;
