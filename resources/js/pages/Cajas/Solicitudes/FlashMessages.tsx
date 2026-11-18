import type { FlashProps } from '@/pages/Cajas/Solicitudes/types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/** Muestra mensajes flash (éxito/error) y el error general `errors.form` del servidor. */
export default function FlashMessages() {
    const page = usePage<FlashProps>();
    const success = page.props.flash?.success ?? null;
    const flashError = page.props.flash?.error ?? null;
    const formError = page.props.errors?.form ?? null;

    const [toast, setToast] = useState<string | null>(null);

    useEffect(() => {
        if (!success) return;
        setToast(success);
        const timer = window.setTimeout(() => setToast(null), 6000);
        return () => window.clearTimeout(timer);
    }, [success]);

    const error = formError ?? flashError;

    return (
        <>
            {toast && <div className="bg-cajas-success rounded-md px-3 py-2 text-sm text-white shadow-sm">{toast}</div>}
            {error && (
                <div role="alert" className="border-cajas-danger/30 bg-cajas-danger/10 text-cajas-danger rounded-md border px-3 py-2 text-sm">
                    {error}
                </div>
            )}
        </>
    );
}
