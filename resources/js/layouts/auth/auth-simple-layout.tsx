import { type PropsWithChildren } from 'react';
import PublicHeader from '@/pages/Web/PublicHeader';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <>
            <PublicHeader />

            <div className="min-h-screen bg-gradient-to-br from-emerald-200 via-teal-100 to-green-200 flex items-center justify-center p-4">
                <div className="w-full max-w-6xl bg-white rounded-3xl overflow-hidden">
                    <div className="flex flex-col lg:flex-row min-h-[700px]">
                        {children}
                    </div>
                </div>
            </div>
        </>
    );
}
