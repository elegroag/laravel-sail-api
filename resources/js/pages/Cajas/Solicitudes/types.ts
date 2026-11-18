/** Contratos compartidos por los módulos Aprueba* (listado, ficha y acciones). */

export type PaginationMeta = {
    current_page: number;
    last_page: number;
    per_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type SolicitudRow = {
    id: number;
    identificacion: string;
    nombre: string;
    estado: string;
    estado_detalle: string;
    fecsol: string | null;
    dias: number;
    alerta: 'normal' | 'warning' | 'danger';
    recepcion: string;
    show_url: string;
    edit_url: string | null;
};

export type SolicitudFilters = {
    estado: string;
    q: string;
    per_page: number;
};

export type EstadoOption = {
    value: string;
    label: string;
    count: number;
};

export type SolicitudResumen = {
    id: number;
    nit: string;
    razsoc: string;
    estado: string;
    estado_detalle: string;
    motivo: string;
    urls: {
        index: string;
        show: string;
    };
};

export type Campo = { label: string; value: string };

export type Seccion = { titulo: string; campos: Campo[] };

export type SeguimientoItem = {
    item: number;
    nota: string;
    estado: string;
    estado_detalle: string;
    fecha: string | null;
};

export type AdjuntoItem = {
    coddoc: string;
    detalle: string;
    archivo: string;
    url: string;
};

export type Motivo = { codest: string; detalle: string };

export type CampoCorregible = { coddoc: string; detalle: string };

export type Acciones = {
    editar: boolean;
    aprobar: boolean;
    devolver: boolean;
    rechazar: boolean;
    aportes: boolean;
    deshacer: boolean;
    reaprobar: boolean;
};

export type SisuInfo = {
    consultado: boolean;
    existe: boolean;
    afiliada: boolean;
    estado: string | null;
    datos: Campo[];
};

export type SelectOption = { value: string; label: string };

export type FlashProps = {
    flash?: { success?: string | null; error?: string | null };
    errors?: Record<string, string>;
};
