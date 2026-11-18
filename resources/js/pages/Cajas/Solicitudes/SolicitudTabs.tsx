export type TabDef = { id: string; label: string };

type Props = {
    tabs: TabDef[];
    active: string;
    onChange: (id: string) => void;
};

export default function SolicitudTabs({ tabs, active, onChange }: Props) {
    return (
        <div className="border-border flex flex-wrap gap-1 border-b" role="tablist">
            {tabs.map((tab) => {
                const selected = tab.id === active;
                return (
                    <button
                        key={tab.id}
                        type="button"
                        role="tab"
                        id={`tab-${tab.id}`}
                        aria-selected={selected}
                        aria-controls={`panel-${tab.id}`}
                        onClick={() => onChange(tab.id)}
                        className={`-mb-px border-b-2 px-4 py-2 text-sm font-medium transition-colors ${
                            selected ? 'border-cajas-border text-foreground' : 'text-muted-foreground hover:text-foreground border-transparent'
                        }`}
                    >
                        {tab.label}
                    </button>
                );
            })}
        </div>
    );
}
