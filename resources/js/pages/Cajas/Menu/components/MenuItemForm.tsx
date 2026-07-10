import { Link } from '@inertiajs/react';
import type { FormEvent, ChangeEvent } from 'react';
import { Plus, Trash2 } from 'lucide-react';
import { CajasMenuIcon } from '@/pages/Cajas/components/CajasMenuIcon';
import { ParentItemPicker } from '@/pages/Cajas/Menu/components/ParentItemPicker';
import {
    cajasCheckboxClass,
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
} from '@/pages/Cajas/styles/cajas-classes';

export type TipoOption = {
    value: string;
    label: string;
};

export type MenuTipoFormRow = {
    tipo: string;
    is_visible: boolean;
    position: number;
};

export type MenuItemFormData = {
    title: string;
    default_url: string;
    url_app: 'cajas' | 'mercurio' | 'web' | '';
    url_path: string;
    icon: string;
    color: string;
    nota: string;
    parent_id: string;
    codapl: string;
    controller: string;
    action: string;
};

export const URL_APP_OPTIONS: { value: MenuItemFormData['url_app']; label: string; prefix: string }[] = [
    { value: '', label: 'Sin ruta', prefix: '' },
    { value: 'cajas', label: 'Cajas', prefix: 'cajas' },
    { value: 'mercurio', label: 'Mercurio', prefix: 'mercurio' },
    { value: 'web', label: 'Web', prefix: 'web' },
];

export function splitDefaultUrl(raw: string): { app: MenuItemFormData['url_app']; path: string } {
    const value = (raw || '').trim().replace(/^\/+/, '');
    if (!value) return { app: '', path: '' };

    for (const opt of URL_APP_OPTIONS) {
        if (!opt.prefix) continue;
        if (value === opt.prefix || value.startsWith(`${opt.prefix}/`)) {
            return { app: opt.value, path: value.slice(opt.prefix.length).replace(/^\/+/, '') };
        }
    }

    return { app: '', path: value };
}

export function composeDefaultUrl(app: MenuItemFormData['url_app'], path: string): string {
    const cleanPath = path.replace(/^\/+/, '').trim();
    const opt = URL_APP_OPTIONS.find((o) => o.value === app);
    if (!opt || !opt.prefix) {
        return cleanPath;
    }
    if (!cleanPath) return opt.prefix;
    return `${opt.prefix}/${cleanPath}`;
}

export const menuFormInputClass = cajasFormInputClass;

export const menuFormSelectClass = cajasFormSelectClass;

export const menuFormTextareaClass = cajasFormTextareaClass;

export const menuFormBtnPrimary = cajasFormBtnPrimary;

export const menuFormBtnSecondary = cajasFormBtnSecondary;

function FieldRow({ children }: { children: React.ReactNode }) {
    return <div className="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-6">{children}</div>;
}

function UrlField({
    formData,
    errors,
    onChange,
}: {
    formData: MenuItemFormData;
    errors: Record<string, string>;
    onChange: (e: ChangeEvent<HTMLInputElement | HTMLSelectElement>) => void;
}) {
    const errorClass = errors.default_url ? cajasInputErrorClass : '';
    const appErrorClass = errors.default_url ? cajasInputErrorClass : '';
    const pathErrorClass = errors.default_url ? cajasInputErrorClass : '';
    const composed = composeDefaultUrl(formData.url_app, formData.url_path);

    return (
        <div>
            <div className="flex flex-col gap-2 sm:flex-row">
                <select
                    name="url_app"
                    id="url_app"
                    aria-label="Aplicación de la URL"
                    className={`${menuFormSelectClass} sm:w-44 ${appErrorClass}`}
                    value={formData.url_app}
                    onChange={onChange}
                >
                    {URL_APP_OPTIONS.map((opt) => (
                        <option key={opt.value || 'none'} value={opt.value}>
                            {opt.label}
                        </option>
                    ))}
                </select>
                <input
                    type="text"
                    name="url_path"
                    id="url_path"
                    placeholder="aprueba-empresa"
                    disabled={!formData.url_app}
                    aria-label="Ruta"
                    className={`${menuFormInputClass} flex-1 ${pathErrorClass} disabled:cursor-not-allowed disabled:bg-muted disabled:text-muted-foreground`}
                    value={formData.url_path}
                    onChange={onChange}
                />
            </div>

            <input type="hidden" name="default_url" id="default_url" value={composed} readOnly />

            <div className="mt-2 flex items-center gap-2 text-xs text-muted-foreground">
                <span className="font-medium text-foreground">Resultado:</span>
                <code className="rounded bg-muted px-1.5 py-0.5 font-mono text-[11px] text-foreground">
                    {composed || '— sin ruta —'}
                </code>
            </div>
            {errors.default_url && <p className="mt-1 text-xs text-cajas-danger">{errors.default_url}</p>}
        </div>
    );
}

function Field({
    label,
    htmlFor,
    required,
    error,
    helper,
    className = '',
    children,
}: {
    label: string;
    htmlFor: string;
    required?: boolean;
    error?: string;
    helper?: string;
    className?: string;
    children: React.ReactNode;
}) {
    return (
        <div className={className}>
            <label htmlFor={htmlFor} className="mb-1.5 flex items-center justify-between">
                <span className={cajasFormLabelClass}>
                    {label}
                    {required && <span className="ml-1 text-cajas-danger">*</span>}
                </span>
                {helper && <span className="text-xs font-normal text-muted-foreground">{helper}</span>}
            </label>
            {children}
            {error && <p className="mt-1 text-xs text-cajas-danger">{error}</p>}
        </div>
    );
}

function SectionHeader({
    title,
    description,
}: {
    title: string;
    description: string;
}) {
    return (
        <div className="border-b border-border pb-3 mb-5">
            <h4 className="text-sm font-semibold tracking-wide text-foreground">{title}</h4>
            <p className="mt-0.5 text-xs text-muted-foreground">{description}</p>
        </div>
    );
}

type MenuItemFieldsProps = {
    formData: MenuItemFormData;
    errors: Record<string, string>;
    onChange: (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => void;
    excludeItemId?: number;
    initialParent?: { id: number; title: string } | null;
    tiposCatalog: TipoOption[];
    menuTipos: MenuTipoFormRow[];
    onMenuTiposChange: (tipos: MenuTipoFormRow[]) => void;
};

function MenuTiposField({
    tiposCatalog,
    menuTipos,
    onChange,
    errors,
}: {
    tiposCatalog: TipoOption[];
    menuTipos: MenuTipoFormRow[];
    onChange: (tipos: MenuTipoFormRow[]) => void;
    errors: Record<string, string>;
}) {
    const usedTipos = new Set(menuTipos.map((row) => row.tipo).filter(Boolean));

    const addRow = () => {
        const nextTipo = tiposCatalog.find((opt) => !usedTipos.has(opt.value));
        if (!nextTipo) return;

        const nextPosition = menuTipos.reduce((max, row) => Math.max(max, row.position), 0) + 1;
        onChange([...menuTipos, { tipo: nextTipo.value, is_visible: true, position: nextPosition }]);
    };

    const updateRow = (index: number, patch: Partial<MenuTipoFormRow>) => {
        onChange(menuTipos.map((row, i) => (i === index ? { ...row, ...patch } : row)));
    };

    const removeRow = (index: number) => {
        onChange(menuTipos.filter((_, i) => i !== index));
    };

    const canAddMore = tiposCatalog.some((opt) => !usedTipos.has(opt.value));

    return (
        <div className="space-y-3">
            {menuTipos.length === 0 ? (
                <p className="text-sm text-muted-foreground">Agregue al menos un tipo de usuario para este item.</p>
            ) : (
                <ul className="space-y-3">
                    {menuTipos.map((row, index) => {
                        const availableOptions = tiposCatalog.filter(
                            (opt) => opt.value === row.tipo || !usedTipos.has(opt.value),
                        );

                        return (
                            <li key={`${row.tipo}-${index}`} className="rounded-lg border border-border p-3">
                                <div className="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                                    <div className="md:col-span-5">
                                        <label className={cajasFormLabelClass}>Tipo *</label>
                                        <select
                                            className={menuFormSelectClass}
                                            value={row.tipo}
                                            onChange={(e) => updateRow(index, { tipo: e.target.value })}
                                        >
                                            <option value="">— Seleccione —</option>
                                            {availableOptions.map((opt) => (
                                                <option key={opt.value} value={opt.value}>
                                                    {opt.label} ({opt.value})
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="md:col-span-3">
                                        <label className={cajasFormLabelClass}>Posición</label>
                                        <input
                                            type="number"
                                            min={0}
                                            className={menuFormInputClass}
                                            value={row.position}
                                            onChange={(e) => updateRow(index, { position: Number(e.target.value) || 0 })}
                                        />
                                    </div>

                                    <div className="md:col-span-3">
                                        <label className={`${cajasFormLabelClass} mb-2 block`}>Visible</label>
                                        <label className="inline-flex items-center gap-2 text-sm text-foreground">
                                            <input
                                                type="checkbox"
                                                className={cajasCheckboxClass}
                                                checked={row.is_visible}
                                                onChange={(e) => updateRow(index, { is_visible: e.target.checked })}
                                            />
                                            Mostrar en menú
                                        </label>
                                    </div>

                                    <div className="md:col-span-1 md:flex md:justify-end">
                                        <button
                                            type="button"
                                            onClick={() => removeRow(index)}
                                            className={cajasFormBtnSecondary}
                                            aria-label="Quitar tipo"
                                            title="Quitar tipo"
                                        >
                                            <Trash2 className="size-4" />
                                        </button>
                                    </div>
                                </div>
                            </li>
                        );
                    })}
                </ul>
            )}

            <div className="flex items-center justify-between gap-3">
                <button type="button" onClick={addRow} disabled={!canAddMore} className={cajasFormBtnSecondary}>
                    <Plus className="size-4" />
                    Agregar tipo
                </button>
                {!canAddMore && menuTipos.length > 0 && (
                    <span className="text-xs text-muted-foreground">Todos los tipos disponibles ya están asignados.</span>
                )}
            </div>

            {errors.tipos && <p className="text-xs text-cajas-danger">{errors.tipos}</p>}
        </div>
    );
}

export function MenuItemFields({
    formData,
    errors,
    onChange,
    excludeItemId,
    initialParent,
    tiposCatalog,
    menuTipos,
    onMenuTiposChange,
}: MenuItemFieldsProps) {
    const inputErrorClass = (field: string) => (errors[field] ? cajasInputErrorClass : '');

    const handleParentChange = (parentId: string) => {
        onChange({
            target: { name: 'parent_id', value: parentId },
        } as ChangeEvent<HTMLInputElement>);
    };

    return (
        <div className="space-y-8">
            <section>
                <SectionHeader
                    title="Identificación"
                    description="Información visible del item en el menú del sistema."
                />
                <FieldRow>
                    <Field label="Título" htmlFor="title" required error={errors.title} className="md:col-span-3">
                        <input
                            type="text"
                            name="title"
                            id="title"
                            required
                            placeholder="Ej. Empresas"
                            className={`${menuFormInputClass} ${inputErrorClass('title')}`}
                            value={formData.title}
                            onChange={onChange}
                        />
                    </Field>

                    <Field label="Aplicación" htmlFor="codapl" required error={errors.codapl} className="md:col-span-3">
                        <select
                            name="codapl"
                            id="codapl"
                            className={menuFormSelectClass}
                            value={formData.codapl}
                            onChange={onChange}
                        >
                            <option value="CA">CA — Cajas</option>
                            <option value="ME">ME — Mercurio</option>
                        </select>
                    </Field>
                </FieldRow>
            </section>

            <section>
                <SectionHeader
                    title="Comportamiento de ruta"
                    description="Cómo se resolverá la navegación cuando el usuario seleccione el item."
                />
                <FieldRow>
                    <Field
                        label="Controller"
                        htmlFor="controller"
                        required
                        error={errors.controller}
                        className="md:col-span-3"
                    >
                        <input
                            type="text"
                            name="controller"
                            id="controller"
                            required
                            placeholder="Ej. ApruebaEmpresaController"
                            className={`${menuFormInputClass} ${inputErrorClass('controller')}`}
                            value={formData.controller}
                            onChange={onChange}
                        />
                    </Field>

                    <Field label="Action" htmlFor="action" required error={errors.action} className="md:col-span-3">
                        <input
                            type="text"
                            name="action"
                            id="action"
                            required
                            placeholder="Ej. index"
                            className={`${menuFormInputClass} ${inputErrorClass('action')}`}
                            value={formData.action}
                            onChange={onChange}
                        />
                    </Field>

                    <Field
                        label="URL por defecto"
                        htmlFor="default_url"
                        error={errors.default_url}
                        helper="Opcional — usada como fallback"
                        className="md:col-span-6"
                    >
                        <UrlField formData={formData} errors={errors} onChange={onChange} />
                    </Field>

                    <Field
                        label="Item padre"
                        htmlFor="parent_id"
                        error={errors.parent_id}
                        helper="Déjelo vacío para items raíz"
                        className="md:col-span-6"
                    >
                        <ParentItemPicker
                            value={formData.parent_id}
                            codapl={formData.codapl}
                            excludeItemId={excludeItemId}
                            initialParent={initialParent}
                            error={errors.parent_id}
                            onChange={handleParentChange}
                        />
                    </Field>
                </FieldRow>
            </section>

            <section>
                <SectionHeader
                    title="Tipos de usuario"
                    description="Define para qué tipos de usuario estará disponible este item en el menú."
                />
                <MenuTiposField
                    tiposCatalog={tiposCatalog}
                    menuTipos={menuTipos}
                    onChange={onMenuTiposChange}
                    errors={errors}
                />
            </section>

            <section>
                <SectionHeader
                    title="Apariencia"
                    description="Icono y color que se muestran en el sidebar."
                />
                <FieldRow>
                    <Field
                        label="Icono"
                        htmlFor="icon"
                        error={errors.icon}
                        helper="fa-solid fa-users · ni ni-building"
                        className="md:col-span-3"
                    >
                        <input
                            type="text"
                            name="icon"
                            id="icon"
                            placeholder="fa-solid fa-users"
                            className={`${menuFormInputClass} ${inputErrorClass('icon')}`}
                            value={formData.icon}
                            onChange={onChange}
                        />
                    </Field>

                    <Field
                        label="Color"
                        htmlFor="color"
                        error={errors.color}
                        helper="text-primary · text-success"
                        className="md:col-span-3"
                    >
                        <input
                            type="text"
                            name="color"
                            id="color"
                            placeholder="text-primary"
                            className={`${menuFormInputClass} ${inputErrorClass('color')}`}
                            value={formData.color}
                            onChange={onChange}
                        />
                    </Field>

                    <div className="md:col-span-6">
                        <label className="mb-1.5 block text-sm font-medium text-foreground">Vista previa</label>
                        <div className="flex items-center gap-3 rounded-lg border border-dashed border-border bg-muted/30 px-4 py-3">
                            <span className="flex h-9 w-9 items-center justify-center rounded-md bg-cajas-bg text-cajas-text">
                                <CajasMenuIcon icon={formData.icon} color={formData.color} />
                            </span>
                            <div className="min-w-0">
                                <p className="truncate text-sm font-medium text-foreground">
                                    {formData.title || 'Título del item'}
                                </p>
                                <p className="truncate text-xs text-muted-foreground">
                                    {formData.controller && formData.action
                                        ? `${formData.controller}@${formData.action}`
                                        : 'controller@action'}
                                </p>
                            </div>
                        </div>
                    </div>
                </FieldRow>
            </section>

            <section>
                <SectionHeader title="Notas" description="Información adicional para auditoría o seguimiento." />
                <FieldRow>
                    <Field label="Nota" htmlFor="nota" error={errors.nota} className="md:col-span-6">
                        <textarea
                            name="nota"
                            id="nota"
                            rows={3}
                            placeholder="Observaciones internas sobre este item…"
                            className={`${menuFormTextareaClass} ${inputErrorClass('nota')}`}
                            value={formData.nota}
                            onChange={onChange}
                        />
                    </Field>
                </FieldRow>
            </section>
        </div>
    );
}

type MenuFormShellProps = {
    title: string;
    subtitle: string;
    cancelHref: string;
    submitLabel: string;
    processing: boolean;
    onSubmit: (event: FormEvent<HTMLFormElement>) => void;
    children: React.ReactNode;
};

export function MenuFormShell({
    title,
    subtitle,
    cancelHref,
    submitLabel,
    processing,
    onSubmit,
    children,
}: MenuFormShellProps) {
    return (
        <div className="mx-auto w-full max-w-4xl p-2 md:p-4">
            <div className="cajas-card">
                <div className="cajas-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 className="text-lg font-semibold text-foreground">{title}</h3>
                        <p className="mt-0.5 text-sm text-muted-foreground">{subtitle}</p>
                    </div>
                    <Link href={cancelHref} className={cajasFormBtnSecondary}>
                        Volver
                    </Link>
                </div>

                <form onSubmit={onSubmit} className="cajas-card-body">
                    {children}

                    <div className="mt-8 flex flex-col-reverse items-stretch justify-end gap-3 border-t border-border pt-5 sm:flex-row sm:items-center">
                        <Link href={cancelHref} className={cajasFormBtnSecondary}>
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className={`${cajasFormBtnPrimary} disabled:cursor-not-allowed disabled:opacity-60`}
                        >
                            {processing ? 'Guardando...' : submitLabel}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}