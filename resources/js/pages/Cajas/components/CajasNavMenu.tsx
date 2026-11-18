import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { CajasMenuIcon } from '@/pages/Cajas/components/CajasMenuIcon';
import { cajasSidebarMenuButtonClass, cajasSidebarSubMenuButtonClass } from '@/pages/Cajas/styles/cajas-classes';
import { type CajasMenuItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { forwardRef, type ComponentPropsWithoutRef } from 'react';

type CajasNavMenuProps = {
    items?: CajasMenuItem[];
};

/** Prefijos de rutas Cajas que responden con Inertia. Ampliar al migrar módulos. */
const CAJAS_INERTIA_PREFIXES = [
    '/cajas/menu',
    '/cajas/menu-permission',
    '/cajas/formulario-dinamico',
    '/cajas/componente-dinamico',
    '/cajas/mercurio01',
    '/cajas/mercurio02',
    '/cajas/mercurio06',
    '/cajas/mercurio12',
    '/cajas/mercurio13',
    '/cajas/mercurio14',
    '/cajas/gener42',
    '/cajas/banners',
    '/cajas/mercurio11',
    '/cajas/mercurio09',
    '/cajas/mercurio04',
    '/cajas/mercurio26',
    '/cajas/principal',
    '/cajas/auditoria',
    '/cajas/reasigna',
    '/cajas/consulta/carga_laboral',
] as const;

function normalizePath(url: string): string {
    return url.split('?')[0].replace(/\/$/, '') || '/';
}

function isInertiaHref(href: string): boolean {
    if (!href || href === '#') {
        return false;
    }

    const path = normalizePath(href);

    return CAJAS_INERTIA_PREFIXES.some((prefix) => path === prefix || path.startsWith(`${prefix}/`));
}

type MenuNavLinkProps = ComponentPropsWithoutRef<'a'> & {
    href: string;
};

/** Reenvía className/ref de SidebarMenuButton asChild (Radix Slot). */
const MenuNavLink = forwardRef<HTMLAnchorElement, MenuNavLinkProps>(function MenuNavLink(
    { href, children, ...props },
    ref,
) {
    if (isInertiaHref(href)) {
        return (
            <Link ref={ref} href={href} prefetch {...props}>
                {children}
            </Link>
        );
    }

    return (
        <a ref={ref} href={href} {...props}>
            {children}
        </a>
    );
});

function isHrefActive(href: string, currentPath: string): boolean {
    if (!href || href === '#') {
        return false;
    }

    const normalizedHref = normalizePath(href);
    const normalizedCurrent = normalizePath(currentPath);

    return normalizedCurrent === normalizedHref || normalizedCurrent.startsWith(`${normalizedHref}/`);
}

function hasActiveDescendant(item: CajasMenuItem, currentPath: string): boolean {
    if (isHrefActive(item.href, currentPath) || item.isActive) {
        return true;
    }

    return (item.children ?? []).some((child) => hasActiveDescendant(child, currentPath));
}

function CajasNavMenuItem({ item, currentPath }: { item: CajasMenuItem; currentPath: string }) {
    const children = item.children ?? [];
    const isActive = item.isActive || isHrefActive(item.href, currentPath);
    const isOpen = hasActiveDescendant(item, currentPath);

    if (children.length === 0) {
        return (
            <SidebarMenuItem>
                <SidebarMenuButton
                    asChild
                    isActive={isActive}
                    tooltip={{ children: item.title }}
                    className={cajasSidebarMenuButtonClass}
                >
                    <MenuNavLink href={item.href}>
                        <CajasMenuIcon icon={item.icon} color={item.color} />
                        <span>{item.title}</span>
                    </MenuNavLink>
                </SidebarMenuButton>
            </SidebarMenuItem>
        );
    }

    return (
        <Collapsible asChild defaultOpen={isOpen} className="group/collapsible">
            <SidebarMenuItem>
                <CollapsibleTrigger asChild>
                    <SidebarMenuButton
                        isActive={isActive}
                        tooltip={{ children: item.title }}
                        className={cajasSidebarMenuButtonClass}
                    >
                        <CajasMenuIcon icon={item.icon} color={item.color} />
                        <span>{item.title}</span>
                        <ChevronRight className="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-90" />
                    </SidebarMenuButton>
                </CollapsibleTrigger>
                <CollapsibleContent>
                    <SidebarMenuSub className="mx-1 border-cajas-border/40 px-1 py-0.5">
                        {children.map((child) => {
                            const childActive = child.isActive || isHrefActive(child.href, currentPath);

                            return (
                                <SidebarMenuSubItem key={child.id}>
                                    <SidebarMenuSubButton
                                        asChild
                                        isActive={childActive}
                                        className={cajasSidebarSubMenuButtonClass}
                                    >
                                        <MenuNavLink href={child.href}>
                                            <span>{child.title}</span>
                                        </MenuNavLink>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            );
                        })}
                    </SidebarMenuSub>
                </CollapsibleContent>
            </SidebarMenuItem>
        </Collapsible>
    );
}

export function CajasNavMenu({ items = [] }: CajasNavMenuProps) {
    const page = usePage();
    const currentPath = normalizePath(page.url);

    if (items.length === 0) {
        return null;
    }

    return (
        <SidebarGroup className="p-1">
            <SidebarGroupLabel className="h-6 px-0.5 text-cajas-text/70">Menú</SidebarGroupLabel>
            <SidebarMenu className="gap-px">
                {items.map((item) => (
                    <CajasNavMenuItem key={item.id} item={item} currentPath={currentPath} />
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
