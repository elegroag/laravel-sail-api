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

            <div className="flex-1 bg-gradient-to-br from-emerald-200 via-teal-100 to-green-200 flex items-center justify-center px-4 pt-16 pb-16 md:pt-20 md:pb-20">
                <div className="w-full max-w-6xl bg-white rounded-3xl overflow-hidden">
                    <div className="flex flex-col lg:flex-row min-h-[700px]">
                        {children}
                    </div>
                </div>
            </div>

            <PublicFooter />
        </div>
    );
}
