import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren } from 'react';

interface AppLayoutProps {
    variant?: 'sidebar' | 'header';
    title?: string;
    description?: string;
    breadcrumbs?: BreadcrumbItem[];
}

export default function AppLayout({ variant = 'sidebar', title, description, breadcrumbs = [], children }: PropsWithChildren<AppLayoutProps>) {
    if (variant === 'header') {
        return (
            <AppShell variant="header">
                <AppHeader breadcrumbs={breadcrumbs} title={title} description={description} />
                <AppContent variant="header">{children}</AppContent>
            </AppShell>
        );
    }

    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden bg-[rgb(250,244,232)]">
                <AppSidebarHeader breadcrumbs={breadcrumbs} title={title} description={description} />
                {children}
            </AppContent>
        </AppShell>
    );
}
