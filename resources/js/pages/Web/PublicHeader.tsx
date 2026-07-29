import { Link, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { cn } from '@/lib/utils';
import ComfacaLogo from '@/components/ComfacaLogo';

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
                                page.url === link.href
                                    ? 'text-emerald-600'
                                    : 'text-gray-600'
                            )}
                        >
                            {link.title}
                        </Link>
                    ))}
                </nav>

                {/* CTA */}
                <div className="flex items-center gap-3">
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
                </div>
            </div>
        </header>
    );
}
