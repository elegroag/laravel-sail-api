import AppLayout from '@/layouts/AppLayout';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import {
    MenuFormShell,
    MenuItemFields,
    type MenuItemFormData,
} from '@/pages/Cajas/Menu/components/MenuItemForm';

type Props = {
    menu_item: {
        id: number;
        title: string;
        default_url: string;
        icon: string;
        color: string;
        nota: string;
        parent_id: number | null;
        codapl: string;
        controller: string;
        action: string;
    };
};

export default function Edit({ menu_item }: Props) {
    const [formData, setFormData] = useState<MenuItemFormData>({
        title: '',
        default_url: '',
        icon: '',
        color: '',
        nota: '',
        parent_id: '',
        codapl: 'CA',
        controller: '',
        action: '',
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    useEffect(() => {
        setFormData({
            title: menu_item.title || '',
            default_url: menu_item.default_url || '',
            icon: menu_item.icon || '',
            color: menu_item.color || '',
            nota: menu_item.nota || '',
            parent_id: menu_item.parent_id ? menu_item.parent_id.toString() : '',
            codapl: menu_item.codapl || 'CA',
            controller: menu_item.controller || '',
            action: menu_item.action || '',
        });
    }, [menu_item]);

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
            const response = await fetch(`/cajas/menu/${menu_item.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    ...formData,
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
            console.error('Error al actualizar item:', error);
        } finally {
            setProcessing(false);
        }
    };

    return (
        <AppLayout title="Editar Menu Item">
            <MenuFormShell
                title="Editar item de menú"
                subtitle="Modificar los datos del item de menú"
                cancelHref="/cajas/menu"
                submitLabel="Actualizar item"
                processing={processing}
                onSubmit={handleSubmit}
            >
                <MenuItemFields formData={formData} errors={errors} onChange={handleChange} />
            </MenuFormShell>
        </AppLayout>
    );
}
