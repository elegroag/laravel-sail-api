export interface Auth {
    user: User | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: any;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: any;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

// Session user from Mercurio (no id, no email_verified_at, etc.)
export interface SessionUser {
    documento: string | number;
    coddoc: string;
    nombre: string;
    email?: string;
    tipo?: string;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    nombre: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface Task {
    id: number;
    title: string;
    description?: string | null;
    completed?: boolean;
    created_at?: string;
    updated_at?: string;
    [key: string]: unknown;
}

export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export interface MenuItem {
    id: string;
    label: string;
    icon?: string;
    route?: string;
    children?: MenuItem[];
}

export type AppShellVariant = 'default' | 'sidebar';

export type EstadoAfiliacion = 'PENDIENTE' | 'APROBADO' | 'RECHAZADO' | 'DEVUELTO' | 'TEMPORAL' | 'EN_REVISION';
