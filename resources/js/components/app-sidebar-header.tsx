import { Breadcrumbs } from '@/components/breadcrumbs';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { UserMenuContent } from '@/components/userMenuContent';
import { useInitials } from '@/hooks/useInitials';
import { usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { type BreadcrumbItem as BreadcrumbItemType, type SharedData } from '@/types';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    const page = usePage<SharedData>();
    const auth = page.props.auth;
    const getInitials = useInitials();

    if (!auth?.user) {
        return (
            <header className="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/50 bg-[rgb(228,197,213)] bg-[linear-gradient(90deg,rgb(28_197_213/0.89)_0%,rgb(51_181_14/0.87))] px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
                <div className="flex items-center gap-2">
                    <SidebarTrigger className="-ml-1" />
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                </div>
            </header>
        );
    }

    return (
        <header className="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/50 bg-[rgb(228,197,213)] bg-[linear-gradient(90deg,rgb(28_197_213/0.89)_0%,rgb(51_181_14/0.87))] px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>

            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <button
                        type="button"
                        className="flex items-center gap-2 h-10 px-2 rounded-full hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring/50 transition-colors"
                    >
                        <Avatar className="h-8 w-8 overflow-hidden rounded-full">
                            <AvatarImage src={auth.user.avatar} alt={auth.user.name} />
                            <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                {getInitials(auth.user.name)}
                            </AvatarFallback>
                        </Avatar>
                        <span className="hidden md:inline text-sm font-medium text-neutral-800 max-w-[140px] truncate">
                            {auth.user.name}
                        </span>
                        <ChevronDown className="hidden md:inline h-4 w-4 text-neutral-600" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="w-56" align="end">
                    <UserMenuContent user={auth.user} />
                </DropdownMenuContent>
            </DropdownMenu>
        </header>
    );
}
