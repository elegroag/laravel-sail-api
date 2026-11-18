import SolicitudesList from '@/pages/Cajas/Solicitudes/SolicitudesList';
import type { EstadoOption, ModuloInfo, PaginationMeta, SolicitudFilters, SolicitudRow } from '@/pages/Cajas/Solicitudes/types';

type Props = {
    modulo: ModuloInfo & { baseUrl: string };
    items: { data: SolicitudRow[]; meta: { pagination: PaginationMeta } };
    filters: SolicitudFilters;
    estadoOptions: EstadoOption[];
    perPageOptions: number[];
};

export default function Index({ modulo, ...props }: Props) {
    return (
        <SolicitudesList
            title={modulo.titulo}
            baseUrl={modulo.baseUrl}
            identificacionLabel={modulo.identificacion}
            nombreLabel={modulo.nombre}
            searchPlaceholder={modulo.busqueda}
            showRecepcion={false}
            {...props}
        />
    );
}
