export interface Componente {
  id: number;
  name: string;
  label: string;
  type: string;
  group_id: number;
  order: number;
  is_disabled: boolean;
  is_readonly: boolean;
  validacion?: {
    is_required: boolean;
    pattern: string | null;
  };
}

export interface Params {
  q?: string;
  page?: number;
  per_page?: number;
  type?: string;
  group_id?: number;
  has_validation?: boolean | '';
}

interface BackendMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

interface BackendResponse {
  data: Componente[];
  meta: BackendMeta;
}

export interface FrontendResponse {
  data: Componente[];
  meta: {
    total_componentes: number;
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

export async function getByFormulario(
  formularioId: number | string,
  params: Partial<Params> = {}
): Promise<FrontendResponse> {
  const url = `/api/cajas/formularios/${formularioId}/componentes`;

  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    credentials: 'same-origin',
    body: JSON.stringify(params)
  });

  if (!res.ok) {
    const text = await res.text().catch(() => '');
    throw new Error(`Error ${res.status}: ${text || res.statusText}`);
  }

  const json: BackendResponse = await res.json();

  return {
    data: json.data,
    meta: {
      total_componentes: json.meta.total,
      pagination: {
        current_page: json.meta.current_page,
        last_page: json.meta.last_page,
        per_page: json.meta.per_page,
        from: json.meta.from,
        to: json.meta.to,
        total: json.meta.total
      }
    }
  };
}
