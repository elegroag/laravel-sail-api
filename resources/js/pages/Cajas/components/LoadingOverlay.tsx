import { Loader2 } from 'lucide-react';

type Props = {
    show: boolean;
    message?: string;
};

export default function LoadingOverlay({ show, message = 'Cargando…' }: Props) {
    if (!show) {
        return null;
    }

    return (
        <div
            className="fixed inset-0 z-[100] flex items-center justify-center bg-background/70 backdrop-blur-[2px]"
            role="status"
            aria-live="polite"
            aria-busy="true"
        >
            <div className="mx-4 flex max-w-sm flex-col items-center gap-3 rounded-xl border border-border bg-card px-6 py-5 shadow-lg">
                <Loader2 className="h-8 w-8 animate-spin text-cajas-border" />
                <p className="text-sm font-medium text-foreground">{message}</p>
                <p className="text-center text-xs text-muted-foreground">Esto puede tardar unos segundos</p>
            </div>
        </div>
    );
}
