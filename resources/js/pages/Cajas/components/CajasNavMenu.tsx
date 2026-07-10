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

type CajasNavMenuProps = {
    items?: CajasMenuItem[];
};

function normalizePath(url: string): string {
    return url.split('?')[0].replace(/\/$/, '') || '/';
}

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
                    <Link href={item.href} prefetch>
                        <CajasMenuIcon icon={item.icon} color={item.color} />
                        <span>{item.title}</span>
                    </Link>
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
                                        <Link href={child.href} prefetch>
                                            <span>{child.title}</span>
                                        </Link>
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
