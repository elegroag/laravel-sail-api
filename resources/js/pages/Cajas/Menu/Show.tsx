import AppLayout from '@/layouts/AppLayout';
import { CajasMenuIcon } from '@/pages/Cajas/components/CajasMenuIcon';
import {
    cajasBadgeClass,
    cajasCardBodyClass,
    cajasCardClass,
    cajasCardHeaderClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasPageClass,
} from '@/pages/Cajas/styles/cajas-classes';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

type MenuTipoRow = {
    id: number;
    tipo: string;
    detalle: string;
    is_visible: boolean;
    position: number;
};

type Props = {
    menu_item: {
        id: number;
        title: string;
        default_url: string | null;
        icon: string | null;
        color: string | null;
        nota: string | null;
        parent_id: number | null;
        codapl: string;
        controller: string;
        action: string;
    };
    menu_tipos: MenuTipoRow[];
    parent?: { id: number; title: string } | null;
};

function DetailRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="grid grid-cols-1 gap-1 border-b border-border px-4 py-4 last:border-b-0 sm:grid-cols-3 sm:gap-4 sm:px-5">
            <dt className="text-sm font-medium text-muted-foreground">{label}</dt>
            <dd className="text-sm text-foreground sm:col-span-2">{value}</dd>
        </div>
    );
}

export default function Show({ menu_item, menu_tipos, parent = null }: Props) {
    const [deleting, setDeleting] = useState(false);

    const handleDelete = async () => {
        if (!confirm('¿Estás seguro de que deseas eliminar este item de menú? Esta acción no se puede deshacer.')) {
            return;
        }

        setDeleting(true);

        try {
            await router.delete(`/cajas/menu/${menu_item.id}`, {
                onSuccess: () => {
                    router.visit('/cajas/menu');
                },
                onError: () => {
                    setDeleting(false);
                },
            });
        } catch (error) {
            console.error('Error al eliminar item de menú:', error);
            setDeleting(false);
        }
    };

    return (
        <AppLayout title={`Menu Item: ${menu_item.title}`}>
            <div className={cajasPageClass}>
                <section className={`${cajasCardClass} mx-auto max-w-4xl`}>
                    <div className={`${cajasCardHeaderClass} flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between`}>
                        <div className="flex items-start gap-3">
                            <div className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-cajas-header-bg ring-1 ring-cajas-border/30">
                                <CajasMenuIcon icon={menu_item.icon} color={menu_item.color} className="text-sm" />
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold text-foreground">Detalles del item de menú</h3>
                                <p className="mt-0.5 text-sm text-muted-foreground">Información completa del item #{menu_item.id}</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link href={`/cajas/menu/${menu_item.id}/edit`} className={cajasFormBtnSecondary}>
                                Editar
                            </Link>
                            <Link href="/cajas/menu" className={cajasFormBtnPrimary}>
                                Volver al listado
                            </Link>
                        </div>
                    </div>

                    <div className={cajasCardBodyClass}>
                        <dl>
                            <DetailRow label="Título" value={menu_item.title} />
                            <DetailRow label="Controller / Action" value={`${menu_item.controller} / ${menu_item.action}`} />
                            <DetailRow label="URL por defecto" value={menu_item.default_url || 'N/A'} />
                            <DetailRow label="Icono / Color" value={`${menu_item.icon || 'N/A'} / ${menu_item.color || 'N/A'}`} />
                            <DetailRow
                                label="Item padre"
                                value={parent ? `#${parent.id} — ${parent.title}` : menu_item.parent_id ? `#${menu_item.parent_id}` : 'Raíz'}
                            />
                            <DetailRow label="Aplicación" value={menu_item.codapl} />
                            <DetailRow label="Permiso" value={`${menu_item.controller}.${menu_item.action}`} />
                            {menu_item.nota && <DetailRow label="Nota" value={menu_item.nota} />}
                        </dl>

                        <div className="mt-6 border-t border-border pt-5">
                            <div className="mb-3 flex items-center justify-between gap-2">
                                <h4 className="text-sm font-semibold text-foreground">Tipos asociados</h4>
                                <span className="text-xs text-muted-foreground">
                                    {menu_tipos.length} tipo{menu_tipos.length === 1 ? '' : 's'}
                                </span>
                            </div>

                            {menu_tipos.length === 0 ? (
                                <p className="text-sm text-muted-foreground">Este item no tiene tipos asociados.</p>
                            ) : (
                                <ul className="space-y-2">
                                    {menu_tipos.map((tipoItem) => (
                                        <li
                                            key={tipoItem.id}
                                            className="flex items-center justify-between gap-3 rounded-lg border border-border px-3 py-2.5"
                                        >
                                            <div>
                                                <p className="text-sm font-medium text-foreground">{tipoItem.detalle}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Código: {tipoItem.tipo} · Pos: {tipoItem.position}
                                                </p>
                                            </div>
                                            <span
                                                className={`${cajasBadgeClass} shrink-0 ${
                                                    tipoItem.is_visible
                                                        ? 'bg-cajas-success/10 text-cajas-success ring-cajas-success/20'
                                                        : 'bg-cajas-danger/10 text-cajas-danger ring-cajas-danger/20'
                                                }`}
                                            >
                                                {tipoItem.is_visible ? 'Visible' : 'Oculto'}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>

                        <div className="mt-6 flex flex-col-reverse justify-between gap-3 border-t border-border pt-5 sm:flex-row sm:items-center">
                            <button
                                type="button"
                                onClick={handleDelete}
                                disabled={deleting}
                                className="inline-flex items-center rounded-md border border-cajas-danger/50 px-4 py-2 text-sm font-medium text-cajas-danger transition-colors hover:bg-cajas-danger/10 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {deleting ? 'Eliminando...' : 'Eliminar item'}
                            </button>
                            <Link href={`/cajas/menu/${menu_item.id}/edit`} className={cajasFormBtnSecondary}>
                                Editar item
                            </Link>
                        </div>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
