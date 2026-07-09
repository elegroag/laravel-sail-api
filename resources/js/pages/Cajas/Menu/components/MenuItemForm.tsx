import { Link } from '@inertiajs/react';
import type { FormEvent, ChangeEvent } from 'react';
import {
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
    cajasFormSelectClass,
    cajasFormTextareaClass,
    cajasInputErrorClass,
} from '@/pages/Cajas/styles/cajas-classes';

export type MenuItemFormData = {
    title: string;
    default_url: string;
    icon: string;
    color: string;
    nota: string;
    parent_id: string;
    codapl: string;
    controller: string;
    action: string;
};

export const menuFormInputClass = cajasFormInputClass;

export const menuFormSelectClass = cajasFormSelectClass;

export const menuFormTextareaClass = cajasFormTextareaClass;

export const menuFormBtnPrimary = cajasFormBtnPrimary;

export const menuFormBtnSecondary = cajasFormBtnSecondary;

type MenuItemFieldsProps = {
    formData: MenuItemFormData;
    errors: Record<string, string>;
    onChange: (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => void;
};

export function MenuItemFields({ formData, errors, onChange }: MenuItemFieldsProps) {
    const inputErrorClass = (field: string) => (errors[field] ? cajasInputErrorClass : '');

    return (
        <div className="grid grid-cols-6 gap-6">
            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="title" className={cajasFormLabelClass}>
                    Título *
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    required
                    className={`${menuFormInputClass} ${inputErrorClass('title')}`}
                    value={formData.title}
                    onChange={onChange}
                />
                {errors.title && <p className="mt-1 text-sm text-red-600">{errors.title}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="codapl" className={cajasFormLabelClass}>
                    Aplicación *
                </label>
                <select
                    name="codapl"
                    id="codapl"
                    className={menuFormSelectClass}
                    value={formData.codapl}
                    onChange={onChange}
                >
                    <option value="CA">CA</option>
                    <option value="ME">ME</option>
                </select>
                {errors.codapl && <p className="mt-1 text-sm text-red-600">{errors.codapl}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="controller" className={cajasFormLabelClass}>
                    Controller *
                </label>
                <input
                    type="text"
                    name="controller"
                    id="controller"
                    required
                    className={`${menuFormInputClass} ${inputErrorClass('controller')}`}
                    value={formData.controller}
                    onChange={onChange}
                />
                {errors.controller && <p className="mt-1 text-sm text-red-600">{errors.controller}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="action" className={cajasFormLabelClass}>
                    Action *
                </label>
                <input
                    type="text"
                    name="action"
                    id="action"
                    required
                    className={`${menuFormInputClass} ${inputErrorClass('action')}`}
                    value={formData.action}
                    onChange={onChange}
                />
                {errors.action && <p className="mt-1 text-sm text-red-600">{errors.action}</p>}
            </div>

            <div className="col-span-6">
                <label htmlFor="default_url" className={cajasFormLabelClass}>
                    URL por defecto
                </label>
                <input
                    type="text"
                    name="default_url"
                    id="default_url"
                    className={`${menuFormInputClass} ${inputErrorClass('default_url')}`}
                    value={formData.default_url}
                    onChange={onChange}
                />
                {errors.default_url && <p className="mt-1 text-sm text-red-600">{errors.default_url}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="icon" className={cajasFormLabelClass}>
                    Icono
                </label>
                <input
                    type="text"
                    name="icon"
                    id="icon"
                    className={`${menuFormInputClass} ${inputErrorClass('icon')}`}
                    value={formData.icon}
                    onChange={onChange}
                />
                {errors.icon && <p className="mt-1 text-sm text-red-600">{errors.icon}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="color" className={cajasFormLabelClass}>
                    Color
                </label>
                <input
                    type="text"
                    name="color"
                    id="color"
                    className={`${menuFormInputClass} ${inputErrorClass('color')}`}
                    value={formData.color}
                    onChange={onChange}
                />
                {errors.color && <p className="mt-1 text-sm text-red-600">{errors.color}</p>}
            </div>

            <div className="col-span-6 sm:col-span-3">
                <label htmlFor="parent_id" className={cajasFormLabelClass}>
                    Padre (ID)
                </label>
                <input
                    type="number"
                    name="parent_id"
                    id="parent_id"
                    className={`${menuFormInputClass} ${inputErrorClass('parent_id')}`}
                    value={formData.parent_id}
                    onChange={onChange}
                />
                {errors.parent_id && <p className="mt-1 text-sm text-red-600">{errors.parent_id}</p>}
            </div>

            <div className="col-span-6">
                <label htmlFor="nota" className={cajasFormLabelClass}>
                    Nota
                </label>
                <textarea
                    name="nota"
                    id="nota"
                    rows={3}
                    className={menuFormTextareaClass}
                    value={formData.nota}
                    onChange={onChange}
                />
                {errors.nota && <p className="mt-1 text-sm text-red-600">{errors.nota}</p>}
            </div>
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
        <div className="bg-white border border-gray-200 rounded-lg shadow-sm m-2 overflow-hidden">
            <div className="px-4 py-5 sm:px-6 border-b border-gray-200 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 className="text-lg leading-6 font-medium text-gray-900">{title}</h3>
                    <p className="mt-1 max-w-2xl text-sm text-gray-500">{subtitle}</p>
                </div>
                <Link href={cancelHref} className={menuFormBtnSecondary}>
                    Volver
                </Link>
            </div>

            <form onSubmit={onSubmit} className="px-4 py-5 sm:px-6">
                {children}

                <div className="flex justify-end gap-3 pt-6 mt-2 border-t border-gray-100">
                    <Link href={cancelHref} className={menuFormBtnSecondary}>
                        Cancelar
                    </Link>
                    <button type="submit" disabled={processing} className={menuFormBtnPrimary}>
                        {processing ? 'Guardando...' : submitLabel}
                    </button>
                </div>
            </form>
        </div>
    );
}
