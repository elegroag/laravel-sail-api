import {
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { cajasSidebarMenuButtonClass } from '@/pages/Cajas/styles/cajas-classes';
import { Link } from '@inertiajs/react';
import { BookOpen, Shield } from 'lucide-react';

const footerLinks = [
    {
        title: 'Documentación',
        href: '/web/documentation',
        icon: BookOpen,
        external: false,
    },
    {
        title: 'Protección de datos',
        href: 'https://comfaca.dataprotected.co',
        icon: Shield,
        external: true,
    },
] as const;

export function CajasSidebarFooter() {
    return (
        <SidebarFooter>
            <SidebarGroup className="px-2 py-1 group-data-[collapsible=icon]:p-0">
                <SidebarGroupContent>
                    <SidebarMenu className="gap-1.5">
                        {footerLinks.map((item) => (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    className={`${cajasSidebarMenuButtonClass} group-data-[collapsible=icon]:px-2`}
                                    tooltip={{ children: item.title }}
                                >
                                    {item.external ? (
                                        <a href={item.href} target="_blank" rel="noopener noreferrer">
                                            <item.icon className="size-4 shrink-0" />
                                            <span>{item.title}</span>
                                        </a>
                                    ) : (
                                        <Link href={item.href} prefetch>
                                            <item.icon className="size-4 shrink-0" />
                                            <span>{item.title}</span>
                                        </Link>
                                    )}
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        ))}
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarFooter>
    );
}
