import { Sidebar, SidebarContent, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { CajasNavMenu } from '@/pages/Cajas/components/CajasNavMenu';
import { CajasSidebarFooter } from '@/pages/Cajas/components/CajasSidebarFooter';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import AppLogo from './app-logo';

export function AppSidebar() {
    const { cajasMenu = [] } = usePage<SharedData>().props;

    return (
        <Sidebar collapsible="icon" variant="inset" className="bg-cajas-bg text-cajas-text">
            <SidebarHeader className="cajas-sidebar-header">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
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
