// Strict TypeScript types for dynamic components system

export type ComponentType = 'input' | 'select' | 'textarea' | 'dialog' | 'date' | 'number';

export type ComponentFormType = ComponentType;

export interface DataSourceOption {
    value: string | number;
    label: string;
}

export interface EventConfig {
    [key: string]: unknown;
}

export interface ComponentData {
    id?: number;
    name: string;
    type: ComponentType;
    label: string;
    placeholder: string;
    form_type: ComponentFormType;
    group_id: number;
    order: number;
    default_value: string;
    is_disabled: boolean;
    is_readonly: boolean;
    data_source: DataSourceOption[];
    css_classes: string;
    help_text: string;
    target: number;
    event_config: EventConfig;
    search_type: string;
    date_max: string;
    number_min: number;
    number_max: number;
    number_step: number;
    created_at?: string;
    updated_at?: string;
}

export interface ValidationRule {
    [key: string]: unknown;
}

export interface ErrorMessage {
    [key: string]: string;
}

export interface ValidationData {
    id?: number;
    componente_id: number;
    pattern: string;
    default_value: string;
    max_length: number;
    min_length: number;
    numeric_range: string;
    field_size: number;
    detail_info: string;
    is_required: boolean;
    custom_rules: ValidationRule;
    error_messages: ErrorMessage;
    created_at?: string;
    updated_at?: string;
}

export interface ComponentWithValidation extends ComponentData {
    validacion?: ValidationData;
}

export interface FormularioDinamico {
    id: number;
    name: string;
    title: string;
    description: string | null;
    module: string;
    endpoint: string;
    method: string;
    is_active: boolean;
    layout_config: Record<string, unknown>;
    permissions: Record<string, unknown>;
    componentes?: ComponentWithValidation[];
    created_at: string;
    updated_at: string;
}

export interface PaginationMeta {
    total_componentes: number;
    pagination: {
        current_page: number;
        last_page: number;
        per_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
}

export interface ComponentesResponse {
    data: ComponentWithValidation[];
    meta: PaginationMeta;
}

export interface ValidationResponse {
    data: ValidationData[];
    meta: PaginationMeta;
}

// Form field types for FormField component
export type FormFieldType = 'input' | 'select' | 'textarea' | 'checkbox';

export interface BaseFormFieldProps {
    label: string;
    name: string;
    error?: string;
    helperText?: string;
    required?: boolean;
}

export interface InputFormFieldProps extends BaseFormFieldProps {
    type: 'input';
    inputType?: 'text' | 'email' | 'password' | 'number' | 'date' | 'url';
    value?: string | number;
    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
    placeholder?: string;
}

export interface SelectFormFieldProps extends BaseFormFieldProps {
    type: 'select';
    options: DataSourceOption[];
    value?: string | number;
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    placeholder?: string;
}

export interface TextAreaFormFieldProps extends BaseFormFieldProps {
    type: 'textarea';
    value?: string;
    onChange?: (e: React.ChangeEvent<HTMLTextAreaElement>) => void;
    placeholder?: string;
    rows?: number;
    showCounter?: boolean;
    maxLength?: number;
}

export interface CheckboxFormFieldProps extends BaseFormFieldProps {
    type: 'checkbox';
    checked?: boolean;
    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

export type FormFieldProps = InputFormFieldProps | SelectFormFieldProps | TextAreaFormFieldProps | CheckboxFormFieldProps;

// Button variants and sizes
export type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'success' | 'warning';
export type ButtonSize = 'sm' | 'md' | 'lg';

// Badge variants and sizes
export type BadgeVariant = 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'danger';
export type BadgeSize = 'sm' | 'md' | 'lg';

// Filter types
export interface FilterOption {
    key: string;
    label: string;
    value: string;
    options?: DataSourceOption[];
    onChange: (value: string) => void;
}

// API Response types
export interface ApiResponse<T> {
    data: T;
    message?: string;
    errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: {
        pagination: {
            current_page: number;
            last_page: number;
            per_page: number;
            from: number | null;
            to: number | null;
            total: number;
        };
    };
}

// Hook return types
export interface UseComponentFormReturn {
    formData: ComponentData;
    errors: Record<string, string>;
    loading: boolean;
    updateField: (field: keyof ComponentData, value: unknown) => void;
    updateDataSource: (dataSource: DataSourceOption[]) => void;
    resetForm: () => void;
    handleSubmit: (e: React.FormEvent) => Promise<void>;
    isValid: () => boolean;
    setErrors: (errors: Record<string, string>) => void;
}

export interface UseValidationFormReturn {
    formData: ValidationData;
    errors: Record<string, string>;
    loading: boolean;
    updateField: (field: keyof ValidationData, value: unknown) => void;
    updateCustomRules: (rules: ValidationRule) => void;
    updateErrorMessages: (messages: ErrorMessage) => void;
    addCustomRule: (key: string, value: unknown) => void;
    removeCustomRule: (key: string) => void;
    addErrorMessage: (key: string, value: string) => void;
    removeErrorMessage: (key: string) => void;
    resetForm: () => void;
    handleSubmit: (e: React.FormEvent) => Promise<void>;
    isValid: () => boolean;
    setErrors: (errors: Record<string, string>) => void;
}

export interface UsePaginationReturn {
    currentPage: number;
    perPage: number;
    totalItems: number;
    totalPages: number;
    hasNextPage: boolean;
    hasPrevPage: boolean;
    from: number;
    to: number;
    goToPage: (page: number) => void;
    nextPage: () => void;
    prevPage: () => void;
    firstPage: () => void;
    lastPage: () => void;
    setPerPage: (perPage: number) => void;
    setTotalItems: (totalItems: number) => void;
    reset: () => void;
    getVisiblePages: () => (number | string)[];
}

export interface UseFiltersReturn {
    filters: Record<string, string>;
    searchValue: string;
    hasActiveFilters: boolean;
    activeFilterCount: number;
    updateFilter: (key: string, value: string) => void;
    updateSearch: (value: string) => void;
    clearFilter: (key: string) => void;
    clearAllFilters: () => void;
    applyFilters: (filters: Record<string, string>) => void;
    getFilterValue: (key: string) => string;
    getQueryParams: () => Record<string, string>;
}
