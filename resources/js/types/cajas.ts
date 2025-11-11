// Tipos compartidos para Cajas — Componentes, Validaciones y Formularios

export type DataSourceItem = { value: string; label: string };

export type Componente = {
    id: number;
    name: string;
    type: 'input' | 'select' | 'textarea' | 'date' | 'number' | 'dialog' | string;
    label: string;
    placeholder?: string | null;
    form_type: string;
    group_id: number;
    order: number;
    default_value?: string | null;
    is_disabled: boolean;
    is_readonly: boolean;
    data_source?: DataSourceItem[] | null;
    css_classes?: string | null;
    help_text?: string | null;
    target: number;
    event_config: Record<string, unknown>;
    search_type?: string | null;
    date_max?: string | null;
    number_min?: number | null;
    number_max?: number | null;
    number_step?: number;
    formulario_id?: number;
};

export type Validacion = {
    id: number;
    componente_id: number;
    is_required: boolean;
    pattern?: string | null;
    default_value?: string | null;
    max_length?: number | null;
    min_length?: number | null;
    field_size: number;
    detail_info?: string | null;
    numeric_range?: string | null;
};

export type LayoutConfig = {
    columns: number;
    spacing: 'sm' | 'md' | 'lg';
    theme: 'default' | 'professional' | 'clean' | 'support' | 'feedback';
};

export type Permissions = {
    public: boolean;
    roles: string[];
};

export type Formulario = {
    id: number;
    name: string;
    title: string;
    description?: string | null;
    module: string;
    endpoint: string;
    method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE' | string;
    is_active: boolean;
    layout_config: LayoutConfig | string;
    permissions: Permissions | string;
};
