import AppLayout from '@/layouts/AppLayout';
import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import {
    MenuFormShell,
    MenuItemFields,
    composeDefaultUrl,
    splitDefaultUrl,
    type MenuItemFormData,
    type MenuTipoFormRow,
    type TipoOption,
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
    parent?: { id: number; title: string } | null;
    tipos: TipoOption[];
    menu_tipos: MenuTipoFormRow[];
};

export default function Edit({ menu_item, parent = null, tipos, menu_tipos }: Props) {
    const [formData, setFormData] = useState<MenuItemFormData>(() => {
        const split = splitDefaultUrl(menu_item.default_url || '');
        return {
            title: '',
            default_url: '',
            url_app: split.app || 'cajas',
            url_path: split.path,
            icon: '',
            color: '',
            nota: '',
            parent_id: '',
            codapl: 'CA',
            controller: '',
            action: '',
        };
    });

    const [menuTipos, setMenuTipos] = useState<MenuTipoFormRow[]>(menu_tipos);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    useEffect(() => {
        const split = splitDefaultUrl(menu_item.default_url || '');
        setFormData({
            title: menu_item.title || '',
            default_url: menu_item.default_url || '',
            url_app: split.app || 'cajas',
            url_path: split.path,
            icon: menu_item.icon || '',
            color: menu_item.color || '',
            nota: menu_item.nota || '',
            parent_id: menu_item.parent_id ? menu_item.parent_id.toString() : '',
            codapl: menu_item.codapl || 'CA',
            controller: menu_item.controller || '',
            action: menu_item.action || '',
        });
        setMenuTipos(menu_tipos);
    }, [menu_item, menu_tipos]);

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
                    default_url: composeDefaultUrl(formData.url_app, formData.url_path),
                    parent_id: formData.parent_id ? Number(formData.parent_id) : null,
                    tipos: menuTipos.filter((row) => row.tipo),
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
                <MenuItemFields
                    formData={formData}
                    errors={errors}
                    onChange={handleChange}
                    excludeItemId={menu_item.id}
                    initialParent={parent}
                    tiposCatalog={tipos}
                    menuTipos={menuTipos}
                    onMenuTiposChange={setMenuTipos}
                />
            </MenuFormShell>
        </AppLayout>
    );
}
