import AppLayout from '@/layouts/AppLayout';
import { router } from '@inertiajs/react';
import { useState } from 'react';
import {
    MenuFormShell,
    MenuItemFields,
    composeDefaultUrl,
    type MenuItemFormData,
} from '@/pages/Cajas/Menu/components/MenuItemForm';

const initialFormData: MenuItemFormData = {
    title: '',
    default_url: '',
    url_app: 'cajas',
    url_path: '',
    icon: '',
    color: '',
    nota: '',
    parent_id: '',
    codapl: 'CA',
    controller: '',
    action: '',
};

export default function Create() {
    const [formData, setFormData] = useState<MenuItemFormData>(initialFormData);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));

        if (errors[name]) {
            setErrors((prev) => ({
                ...prev,
                [name]: '',
            }));
        }
    };

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setProcessing(true);

        try {
            const response = await fetch('/cajas/menu', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    ...formData,
                    default_url: composeDefaultUrl(formData.url_app, formData.url_path),
                    parent_id: formData.parent_id ? Number(formData.parent_id) : null,
                }),
            });

            if (response.ok) {
                const data = await response.json().catch(() => null);
                if (data?.redirect) {
                    router.visit(data.redirect);
                } else {
                    router.visit('/cajas/menu');
                }
                return;
            }

            const data = await response.json().catch(() => ({}));
            if (data.errors) {
                setErrors(data.errors);
            }
        } catch (error) {
            console.error('Error al crear item de menú:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Crear Menu Item">
            <MenuFormShell
                title="Crear item de menú"
                subtitle="Formulario para crear un nuevo item de menú"
                cancelHref="/cajas/menu"
                submitLabel="Guardar item de menú"
                processing={processing}
                onSubmit={handleSubmit}
            >
                <MenuItemFields formData={formData} errors={errors} onChange={handleChange} />
            </MenuFormShell>
        </AppLayout>
    );
}
