import { type PropsWithChildren } from 'react';
import PublicHeader from '@/pages/Web/PublicHeader';
import PublicFooter from '@/pages/Web/PublicFooter';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="min-h-screen flex flex-col">
            <PublicHeader />

            <div className="relative flex-1 overflow-hidden bg-gradient-to-br from-emerald-300 via-teal-200 to-green-300 px-4 pt-16 pb-16 md:pt-20 md:pb-20">
                {/* Luces de profundidad */}
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0"
                    style={{
                        backgroundImage: [
                            'radial-gradient(ellipse 55% 45% at 12% 18%, rgba(255,255,255,0.55), transparent 70%)',
                            'radial-gradient(ellipse 50% 40% at 88% 12%, rgba(16,185,129,0.45), transparent 65%)',
                            'radial-gradient(ellipse 60% 50% at 78% 88%, rgba(13,148,136,0.35), transparent 70%)',
                            'radial-gradient(ellipse 40% 35% at 30% 75%, rgba(52,211,153,0.35), transparent 70%)',
                        ].join(', '),
                    }}
                />

                {/* Malla geométrica visible */}
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 opacity-80"
                    style={{
                        backgroundImage:
                            "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56' viewBox='0 0 56 56'%3E%3Cpath d='M28 0L56 14v28L28 56 0 42V14z' fill='none' stroke='%23065f46' stroke-opacity='0.35' stroke-width='1'/%3E%3Ccircle cx='28' cy='28' r='1.6' fill='%23065f46' fill-opacity='0.45'/%3E%3C/svg%3E\")",
                        backgroundSize: '56px 56px',
                    }}
                />

                {/* Ruido SVG real (más perceptible que un tile de filtro) */}
                <svg
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 h-full w-full opacity-55 mix-blend-multiply"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <filter id="login-noise">
                        <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="3" stitchTiles="stitch" />
                        <feColorMatrix type="saturate" values="0" />
                    </filter>
                    <rect width="100%" height="100%" filter="url(#login-noise)" />
                </svg>

                {/* Viñeta suave para contraste */}
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0"
                    style={{
                        background:
                            'radial-gradient(ellipse 75% 65% at 50% 45%, transparent 40%, rgba(6, 78, 59, 0.18) 100%)',
                    }}
                />

                <div className="relative z-10 flex min-h-full items-center justify-center">
                    <div className="w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-emerald-950/20 ring-1 ring-emerald-900/10">
                        <div className="flex min-h-[700px] flex-col lg:flex-row">
                            {children}
                        </div>
                    </div>
                </div>
            </div>

            <PublicFooter />
        </div>
    );
}
