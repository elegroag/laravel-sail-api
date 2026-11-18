import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { cajasFormBtnPrimary, cajasFormBtnSecondary } from '@/pages/Cajas/styles/cajas-classes';
import { Loader2 } from 'lucide-react';

type Props = {
    open: boolean;
    title: string;
    description: string;
    confirmLabel: string;
    processing?: boolean;
    onConfirm: () => void;
    onCancel: () => void;
};

export default function ConfirmDialog({ open, title, description, confirmLabel, processing = false, onConfirm, onCancel }: Props) {
    return (
        <Dialog open={open} onOpenChange={(next) => !next && !processing && onCancel()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <button type="button" className={cajasFormBtnSecondary} onClick={onCancel} disabled={processing}>
                        Cancelar
                    </button>
                    <button type="button" className={cajasFormBtnPrimary} onClick={onConfirm} disabled={processing}>
                        {processing && <Loader2 className="mr-1.5 size-4 animate-spin" />}
                        {confirmLabel}
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
