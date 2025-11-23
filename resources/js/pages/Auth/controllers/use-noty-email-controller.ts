import { useEffect, useMemo, useState } from 'react';

type TipoAfiliado = 'T' | 'P' | 'O' | 'F' | 'I' | 'E' | 'S' | '';

interface NotyEmailControllerProps {
    errors?: {
        message?: string;
    };
}

interface Option {
    value: string;
    label: string;
}

const tipoAfiliadoOptions: Option[] = [
    { value: 'T', label: 'Trabajador' },
    { value: 'P', label: 'Particular' },
    { value: 'O', label: 'Pensionado' },
    { value: 'F', label: 'Facultativo' },
    { value: 'I', label: 'Independiente' },
    { value: 'E', label: 'Empleador' },
    { value: 'S', label: 'Servicio doméstico' },
];

const useNotyEmailController = ({ errors }: NotyEmailControllerProps) => {
    const [alertMessage, setAlertMessage] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [processing, setProcessing] = useState(false);

    const [Coddoc, setCoddoc] = useState<Record<string, string>>({});

    const [tipoAfiliado, setTipoAfiliado] = useState<TipoAfiliado>('');
    const [documentType, setDocumentType] = useState('');
    const [documento, setDocumento] = useState('');
    const [nombre, setNombre] = useState('');
    const [telefono, setTelefono] = useState('');
    const [email, setEmail] = useState('');
    const [novedad, setNovedad] = useState('');

    useEffect(() => {
        if (errors?.message) {
            setAlertMessage(errors.message);
        }
        // Cargar parámetros de tipos de documento reutilizando el endpoint de login
        loadParams();
    }, [errors]);

    const documentTypeOptions: Option[] = useMemo(() => Object.entries(Coddoc || {}).map(([value, label]) => ({ value, label }) as Option), [Coddoc]);

    useEffect(() => {
        // Seleccionar valores por defecto cuando existan opciones
        if (!tipoAfiliado) {
            setTipoAfiliado('T');
        }
        const firstDoc = Object.keys(Coddoc || {})[0];
        if (!documentType && firstDoc) {
            setDocumentType(firstDoc);
        }
    }, [Coddoc, tipoAfiliado, documentType]);

    const loadParams = async () => {
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
                setAlertMessage(responseJson?.message || 'No fue posible cargar los parámetros de tipos de documento.');
            }
        } catch (error) {
            console.error('Error al cargar parámetros de login:', error);
            setAlertMessage('No fue posible cargar los parámetros. Verifique su conexión e intente nuevamente.');
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setAlertMessage(null);
        setSuccessMessage(null);

        try {
            const response = await fetch(route('mercurio.cambio_correo'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    tipo: tipoAfiliado,
                    coddoc: documentType,
                    documento,
                    email,
                    telefono,
                    novedad,
                }),
            });

            const data = await response.json();

            if (response.ok && data?.success) {
                setSuccessMessage(data.msj || 'Se ha enviado la solicitud de cambio de correo correctamente.');
                // Limpiar algunos campos tras éxito
                setTelefono('');
                setNovedad('');
            } else {
                setAlertMessage(data?.msj || data?.message || 'No fue posible enviar la solicitud. Intente nuevamente.');
            }
        } catch (error) {
            console.error('Error al enviar solicitud de cambio de correo:', error);
            setAlertMessage('Ocurrió un error al enviar la solicitud. Verifique su conexión e intente nuevamente.');
        } finally {
            setProcessing(false);
        }
    };

    return {
        alertMessage,
        successMessage,
        processing,
        tipoAfiliado,
        setTipoAfiliado,
        documentType,
        setDocumentType,
        documento,
        setDocumento,
        nombre,
        setNombre,
        telefono,
        setTelefono,
        email,
        setEmail,
        novedad,
        setNovedad,
        documentTypeOptions,
        tipoAfiliadoOptions,
        handleSubmit,
    };
};

export default useNotyEmailController;
