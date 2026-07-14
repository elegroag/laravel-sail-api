import { TipoFuncionario } from '@/constants/auth';
import type { DocumentTypeOption, LoginProps, UserType } from '@/types/auth';
import { router } from '@inertiajs/react';
import type React from 'react';
import { useEffect, useMemo, useState } from 'react';

// Tipos de usuario para los que se filtra NIT y NUIP del listado de documentos:
// NIT es exclusivo de empresas y "particulares" con NIT propio; NUIP aplica
// para menores de edad y no corresponde a un trabajador dependiente adulto,
// independiente, pensionado o facultativo.
const NATURAL_USER_TYPES: UserType[] = ['trabajador', 'independiente', 'pensionado', 'facultativo'];

const isNitOption = (opt: DocumentTypeOption) =>
    opt.label.toLowerCase().includes('nit') || opt.value.toLowerCase() === 'nit';

const isNuipOption = (opt: DocumentTypeOption) =>
    opt.label.toLowerCase().includes('nuip') || opt.value.toLowerCase() === 'nu';

const useLoginController = ({ errors }: LoginProps) => {
    const [selectedUserType, setSelectedUserType] = useState<UserType | null>(null);
    const [documentType, setDocumentType] = useState('');
    const [identification, setIdentification] = useState('');
    const [password, setPassword] = useState('');
    const [processing, setProcessing] = useState(false);
    const [Coddoc, setCoddoc] = useState<Record<string, string>>({});
    const [dialog, setDialog] = useState<{ message: string; type: 'success' | 'error' } | null>(null);

    useEffect(() => {
        if (errors?.message) {
            setDialog({ message: errors.message, type: 'error' });
        }
        loadParams(setCoddoc);
    }, [errors, setCoddoc]);

    const isNaturalLogin = !!selectedUserType && NATURAL_USER_TYPES.includes(selectedUserType);

    const documentTypeOptions: DocumentTypeOption[] = useMemo(() => {
        const base = Object.entries(Coddoc || {}).map(
            ([value, label]) => ({ value, label }) as DocumentTypeOption,
        );
        // Empresa: muestra todo el catálogo (incluido NIT) para identificarse con NIT.
        // Persona natural: oculta NIT (empresas) y NUIP (menores de edad).
        if (!isNaturalLogin) return base;
        return base.filter((opt) => !isNitOption(opt) && !isNuipOption(opt));
    }, [Coddoc, isNaturalLogin]);

    useEffect(() => {
        // Si la lista cambió y el documento actualmente seleccionado dejó de estar
        // disponible (porque el usuario cambió de tipo), limpiamos para no enviar
        // un valor inválido al backend.
        if (!documentType) return;
        const stillAvailable = documentTypeOptions.some((o) => o.value === documentType);
        if (!stillAvailable) {
            const first = documentTypeOptions[0]?.value ?? '';
            setDocumentType(first);
        }
    }, [documentTypeOptions, documentType]);

    const handleUserTypeSelect = (userType: UserType) => {
        setSelectedUserType(userType);
    };

    const handleBack = () => {
        setSelectedUserType(null);
        setDocumentType('');
        setIdentification('');
        setPassword('');
        setDialog(null);
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setDialog(null);

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
                    const message = errors.message || 'No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.';
                    setDialog({ message, type: 'error' });
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
                setDialog({ message: responseJson?.message || 'Error al cargar parámetros.', type: 'error' });
            }
        } catch (error) {
            console.error('Error al cargar parámetros de login:', error);
            setDialog({ message: 'No fue posible cargar los parámetros de login. Verifique su conexión e intente nuevamente.', type: 'error' });
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
        dialog,
        setDialog,
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
