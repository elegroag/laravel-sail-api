import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { useEffect, useState } from 'react';
import ComfacaLogo from '@/components/ComfacaLogo';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { cn } from '@/lib/utils';

const AGENTI_SCRIPT_ID = 'agenti-lite-comfaca-script';
const AGENTI_SCRIPT_SRC = 'https://comfaca.agenti.com.co/agenti_lite_comfaca/js/scripts.js';

const navLinks = [
    { title: 'Nosotros', href: '/web/about' },
    { title: 'Servicios', href: '/web/products' },
    { title: 'Tutoriales', href: '/web/documentation' },
    { title: 'Contáctenos', href: '/web/contact' },
];

function shouldLoadAgenti(url: string): boolean {
    const path = url.split('?')[0] ?? url;
    return (
        path === '/web/products' ||
        path === '/web/about' ||
        path === '/web/documentation' ||
        path === '/web/contact' ||
        path === '/web/login' ||
        path === '/web/register' ||
        path.startsWith('/web/register/')
    );
}

export default function PublicHeader() {
    const page = usePage();
    const isLoginPage = page.url === '/web/login';
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        if (!shouldLoadAgenti(page.url)) {
            return;
        }

        if (document.getElementById(AGENTI_SCRIPT_ID)) {
            return;
        }

        const script = document.createElement('script');
        script.id = AGENTI_SCRIPT_ID;
        script.src = AGENTI_SCRIPT_SRC;
        script.type = 'text/javascript';
        script.async = true;
        document.body.appendChild(script);
    }, [page.url]);

    useEffect(() => {
        setMobileOpen(false);
    }, [page.url]);

    return (
        <header className="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
            <div className="container mx-auto flex h-16 items-center justify-between px-4 md:max-w-7xl">
                {/* Logo */}
                <Link href="/web/about" className="flex items-center space-x-2">
                    <ComfacaLogo className="h-16 w-auto" />
                    <span className="font-semibold text-lg text-gray-900">Comfaca En Línea</span>
                </Link>

                {/* Desktop Nav */}
                <nav className="hidden md:flex items-center gap-6">
                    {navLinks.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className={cn(
                                'text-sm font-medium transition-colors hover:text-emerald-600',
                                page.url === link.href ? 'text-emerald-600' : 'text-gray-600',
                            )}
                        >
                            {link.title}
                        </Link>
                    ))}
                </nav>

                {/* CTA + Mobile Menu */}
                <div className="flex items-center gap-2 sm:gap-3">
                    <Link
                        href="/web/register"
                        className="hidden md:inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition-colors"
                    >
                        Afiliarse
                    </Link>
                    {!isLoginPage && (
                        <Link
                            href="/web/login"
                            className="text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors"
                        >
                            Iniciar sesión
                        </Link>
                    )}

                    <Sheet open={mobileOpen} onOpenChange={setMobileOpen}>
                        <SheetTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="md:hidden h-9 w-9 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50"
                                aria-label="Abrir menú de navegación"
                            >
                                <Menu className="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="right"
                            className="flex w-[min(100%,20rem)] flex-col gap-0 border-l-0 bg-gradient-to-br from-black via-emerald-950 to-emerald-600 p-0 text-white [&>button]:text-white [&>button]:hover:bg-white/10 [&>button]:hover:opacity-100"
                        >
                            <SheetHeader className="border-b border-white/15 px-4 py-4 text-left">
                                <SheetTitle className="text-base font-semibold text-white">Menú</SheetTitle>
                            </SheetHeader>

                            <nav className="flex flex-1 flex-col gap-1 p-4">
                                {navLinks.map((link) => (
                                    <Link
                                        key={link.href}
                                        href={link.href}
                                        onClick={() => setMobileOpen(false)}
                                        className={cn(
                                            'rounded-md px-3 py-2.5 text-sm font-medium text-white transition-colors',
                                            page.url === link.href
                                                ? 'bg-white/20'
                                                : 'hover:bg-white/10',
                                        )}
                                    >
                                        {link.title}
                                    </Link>
                                ))}
                            </nav>

                            <div className="mt-auto flex flex-col gap-2 border-t border-white/15 p-4">
                                <Link
                                    href="/web/register"
                                    onClick={() => setMobileOpen(false)}
                                    className="inline-flex items-center justify-center rounded-md border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-white/20"
                                >
                                    Afiliarse
                                </Link>
                                {!isLoginPage && (
                                    <Link
                                        href="/web/login"
                                        onClick={() => setMobileOpen(false)}
                                        className="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-white/10"
                                    >
                                        Iniciar sesión
                                    </Link>
                                )}
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>
    );
}
