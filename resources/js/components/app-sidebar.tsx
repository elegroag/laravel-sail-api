import { Sidebar, SidebarContent, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { CajasNavMenu } from '@/pages/Cajas/components/CajasNavMenu';
import { CajasSidebarFooter } from '@/pages/Cajas/components/CajasSidebarFooter';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import AppLogo from './app-logo';

export function AppSidebar() {
    const { cajasMenu = [] } = usePage<SharedData>().props;

    return (
        <Sidebar collapsible="icon" variant="inset" className="bg-cajas-bg text-cajas-text pr-0">
            <SidebarHeader className="cajas-sidebar-header justify-center !pr-4">
                <SidebarMenu className="items-center">
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild tooltip={{ children: 'Comfaca en línea' }} className="justify-center">
                            <Link href="/cajas/principal" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <CajasNavMenu items={cajasMenu} />
            </SidebarContent>

            <CajasSidebarFooter />
        </Sidebar>
    );
}
