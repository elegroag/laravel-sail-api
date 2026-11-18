import SolicitudesList from '@/pages/Cajas/Solicitudes/SolicitudesList';
import type { EstadoOption, PaginationMeta, SolicitudFilters, SolicitudRow } from '@/pages/Cajas/Solicitudes/types';

type Props = {
    items: { data: SolicitudRow[]; meta: { pagination: PaginationMeta } };
    filters: SolicitudFilters;
    estadoOptions: EstadoOption[];
    perPageOptions: number[];
};

export default function Index(props: Props) {
    return (
        <SolicitudesList
            title="Aprobación de empresas"
            baseUrl="/cajas/aprobacionemp/index"
            identificacionLabel="NIT"
            nombreLabel="Razón social"
            searchPlaceholder="NIT, razón social, documento o cédula del representante…"
            {...props}
        />
    );
}
