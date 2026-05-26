import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { usePage } from '@inertiajs/react';
import ComfacaLogo from '@/components/ComfacaLogo';

const navLinks = [
    { title: 'Nosotros', href: '/web/about' },
    { title: 'Productos', href: '/web/products' },
    { title: 'Documentación', href: '/web/documentation' },
    { title: 'Contáctenos', href: '/web/contact' },
];

export default function PublicHeader() {
    const page = usePage();
    const isLoginPage = page.url === '/web/login';

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