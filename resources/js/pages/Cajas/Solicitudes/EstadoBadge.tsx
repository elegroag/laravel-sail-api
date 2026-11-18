import { cajasBadgeClass } from '@/pages/Cajas/styles/cajas-classes';

const ESTADO_CLASSES: Record<string, string> = {
    P: 'bg-sky-100 text-sky-800 ring-sky-200',
    A: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    D: 'bg-amber-100 text-amber-800 ring-amber-200',
    X: 'bg-red-100 text-red-800 ring-red-200',
    T: 'bg-pink-100 text-pink-800 ring-pink-200',
    I: 'bg-muted text-muted-foreground ring-border',
};

export function estadoBadgeClass(estado: string): string {
    return `${cajasBadgeClass} ${ESTADO_CLASSES[estado] ?? 'bg-muted text-muted-foreground ring-border'}`;
}

export default function EstadoBadge({ estado, label }: { estado: string; label?: string }) {
    return <span className={estadoBadgeClass(estado)}>{label || estado}</span>;
}
