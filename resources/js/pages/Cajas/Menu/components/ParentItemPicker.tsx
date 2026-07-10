import { useCallback, useEffect, useState } from 'react';
import { Search, X } from 'lucide-react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    cajasFormBtnPrimary,
    cajasFormBtnSecondary,
    cajasFormInputClass,
    cajasFormLabelClass,
} from '@/pages/Cajas/styles/cajas-classes';

export type ParentMenuOption = {
    id: number;
    title: string;
    controller: string | null;
    action: string | null;
    parent_id: number | null;
    default_url: string | null;
};

type ParentItemPickerProps = {
    value: string;
    codapl: string;
    excludeItemId?: number;
    initialParent?: { id: number; title: string } | null;
    error?: string;
    onChange: (parentId: string) => void;
};

export function ParentItemPicker({
    value,
    codapl,
    excludeItemId,
    initialParent,
    error,
    onChange,
}: ParentItemPickerProps) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [items, setItems] = useState<ParentMenuOption[]>([]);
    const [loading, setLoading] = useState(false);
    const [fetchError, setFetchError] = useState<string | null>(null);
    const [selectedLabel, setSelectedLabel] = useState<string>(() => {
        if (initialParent && String(initialParent.id) === value) {
            return `#${initialParent.id} — ${initialParent.title}`;
        }
        return value ? `#${value}` : '';
    });

    useEffect(() => {
        if (initialParent && String(initialParent.id) === value) {
            setSelectedLabel(`#${initialParent.id} — ${initialParent.title}`);
        } else if (!value) {
            setSelectedLabel('');
        } else if (value && !selectedLabel.includes(' — ')) {
            setSelectedLabel(`#${value}`);
        }
    }, [initialParent, value]);

    const loadItems = useCallback(
        async (query: string) => {
            setLoading(true);
            setFetchError(null);

            try {
                const response = await fetch('/cajas/menu/parent-options', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        q: query,
                        codapl,
                        exclude_id: excludeItemId ?? null,
                    }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.message || 'No se pudieron cargar los items de menú.');
                }

                setItems(Array.isArray(data.data) ? data.data : []);
            } catch (err) {
                setFetchError(err instanceof Error ? err.message : 'Error al cargar items.');
                setItems([]);
            } finally {
                setLoading(false);
            }
        },
        [codapl, excludeItemId],
    );

    useEffect(() => {
        if (open) {
            setSearch('');
            loadItems('');
        }
    }, [open, loadItems]);

    const handleSelect = (item: ParentMenuOption) => {
        onChange(String(item.id));
        setSelectedLabel(`#${item.id} — ${item.title}`);
        setOpen(false);
    };

    const handleClear = () => {
        onChange('');
        setSelectedLabel('');
    };

    return (
        <>
            <div className="flex gap-2">
                <input
                    type="text"
                    id="parent_id_display"
                    readOnly
                    placeholder="Sin item padre (raíz)"
                    className={`${cajasFormInputClass} flex-1 cursor-default bg-muted/30 ${error ? 'border-cajas-danger' : ''}`}
                    value={selectedLabel}
                />
                {value && (
                    <button
                        type="button"
                        onClick={handleClear}
                        className={cajasFormBtnSecondary}
                        title="Quitar padre"
                        aria-label="Quitar padre"
                    >
                        <X className="size-4" />
                    </button>
                )}
                <button type="button" onClick={() => setOpen(true)} className={cajasFormBtnSecondary}>
                    <Search className="size-4" />
                    Buscar
                </button>
            </div>

            <input type="hidden" name="parent_id" id="parent_id" value={value} readOnly />

            {error && <p className="mt-1 text-xs text-cajas-danger">{error}</p>}

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="flex max-h-[85vh] flex-col gap-0 overflow-hidden p-0 sm:max-w-3xl">
                    <DialogHeader className="border-b border-border px-5 py-4">
                        <DialogTitle>Seleccionar item padre</DialogTitle>
                        <DialogDescription>
                            Elija el item de menú que será el padre. Déjelo vacío para crear un item raíz.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="space-y-4 px-5 py-4">
                        <div>
                            <label htmlFor="parent_search" className={cajasFormLabelClass}>
                                Buscar
                            </label>
                            <div className="mt-1 flex gap-2">
                                <div className="relative flex-1">
                                    <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input
                                        id="parent_search"
                                        type="text"
                                        className={`${cajasFormInputClass} pl-9`}
                                        placeholder="ID, título, controller o action"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                loadItems(search);
                                            }
                                        }}
                                    />
                                </div>
                                <button type="button" onClick={() => loadItems(search)} className={cajasFormBtnSecondary}>
                                    Buscar
                                </button>
                            </div>
                        </div>

                        {fetchError && (
                            <div className="rounded-md border border-cajas-danger/30 bg-cajas-danger/10 px-3 py-2 text-sm text-cajas-danger">
                                {fetchError}
                            </div>
                        )}

                        <div className="overflow-hidden rounded-lg border border-border">
                            <div className="max-h-[min(50vh,420px)] overflow-auto">
                                <table className="min-w-full divide-y divide-border text-sm">
                                    <thead className="sticky top-0 bg-muted/60 backdrop-blur-sm">
                                        <tr>
                                            <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">ID</th>
                                            <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Título</th>
                                            <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Controller</th>
                                            <th className="px-3 py-2 text-left text-xs font-semibold text-muted-foreground">Action</th>
                                            <th className="px-3 py-2 text-right text-xs font-semibold text-muted-foreground">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border bg-card">
                                        {loading && (
                                            <tr>
                                                <td colSpan={5} className="px-3 py-8 text-center text-muted-foreground">
                                                    Cargando items…
                                                </td>
                                            </tr>
                                        )}

                                        {!loading && items.length === 0 && (
                                            <tr>
                                                <td colSpan={5} className="px-3 py-8 text-center text-muted-foreground">
                                                    No se encontraron items de menú.
                                                </td>
                                            </tr>
                                        )}

                                        {!loading &&
                                            items.map((item) => (
                                                <tr key={item.id} className="transition-colors hover:bg-muted/30">
                                                    <td className="px-3 py-2.5 font-mono text-xs text-muted-foreground">{item.id}</td>
                                                    <td className="px-3 py-2.5 font-medium text-foreground">{item.title}</td>
                                                    <td className="px-3 py-2.5 text-muted-foreground">{item.controller || '—'}</td>
                                                    <td className="px-3 py-2.5 text-muted-foreground">{item.action || '—'}</td>
                                                    <td className="px-3 py-2.5 text-right">
                                                        <button
                                                            type="button"
                                                            onClick={() => handleSelect(item)}
                                                            className={cajasFormBtnPrimary}
                                                        >
                                                            Seleccionar
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end border-t border-border px-5 py-3">
                        <button type="button" onClick={() => setOpen(false)} className={cajasFormBtnSecondary}>
                            Cerrar
                        </button>
                    </div>
                </DialogContent>
            </Dialog>
        </>
    );
}
