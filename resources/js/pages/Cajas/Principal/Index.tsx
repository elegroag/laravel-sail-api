import AppLayout from '@/layouts/AppLayout';
import { cajasCardClass, cajasPageClass } from '@/pages/Cajas/styles/cajas-classes';

type Cantidades = {
    pendientes: number;
    aprobados: number;
    rechazados: number;
    devueltos: number;
    temporales: number;
};

type TarjetaAfiliacion = {
    name: string;
    cantidad: Cantidades;
    icon: string;
    url: string;
    href: string;
    imagen: string;
    imagen_url: string;
};

type TarjetaProducto = {
    name: string;
    cantidad: number;
    icon: string;
    url: string;
    href: string;
    imagen: string;
    imagen_url: string;
};

type Props = {
    servicios: {
        afiliacion: TarjetaAfiliacion[];
        productos: TarjetaProducto[];
    };
};

const statusRows: { key: keyof Omit<Cantidades, 'temporales'>; label: string }[] = [
    { key: 'pendientes', label: 'Pendientes' },
    { key: 'aprobados', label: 'Aprobados' },
    { key: 'rechazados', label: 'Rechazados' },
    { key: 'devueltos', label: 'Devueltos' },
];

export default function Index({ servicios }: Props) {
    return (
        <AppLayout title="Inicio">
            <div className={cajasPageClass}>
                <section className="space-y-3">
                    <div>
                        <h2 className="text-base font-semibold text-foreground">Movimientos</h2>
                        <p className="text-sm text-muted-foreground">Solicitudes de afiliación</p>
                    </div>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        {servicios.afiliacion.map((row) => (
                            <a
                                key={row.url}
                                href={row.href}
                                className={`${cajasCardClass} group block transition-shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cajas-border`}
                            >
                                <div className="flex justify-center border-b border-border bg-muted/30 p-3">
                                    <img
                                        src={row.imagen_url}
                                        alt={row.name}
                                        className="h-24 w-auto max-w-full object-contain transition-transform group-hover:scale-[1.02]"
                                    />
                                </div>
                                <div className="space-y-3 p-4">
                                    <h3 className="text-sm font-semibold text-foreground">{row.name}</h3>
                                    <div className="space-y-1.5">
                                        {statusRows.map((status) => (
                                            <div key={status.key} className="flex items-center justify-between text-sm">
                                                <span className="text-muted-foreground">{status.label}</span>
                                                <span className="font-medium text-foreground">{row.cantidad[status.key]}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </a>
                        ))}
                    </div>
                </section>

                <section className="space-y-3">
                    <div>
                        <h2 className="text-base font-semibold text-foreground">Productos y Servicios</h2>
                        <p className="text-sm text-muted-foreground">
                            Productos y servicios adicionales de la CAJA de Compensación del Caquetá
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-4">
                        {servicios.productos.map((row) => (
                            <a
                                key={row.url}
                                href={row.href}
                                className={`${cajasCardClass} min-w-[240px] max-w-xs flex-1 transition-shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-cajas-border`}
                            >
                                <div className="space-y-3 p-4">
                                    <p className="text-sm font-medium text-muted-foreground">{row.name}</p>
                                    <img
                                        src={row.imagen_url}
                                        alt={row.name}
                                        className="mx-auto h-20 w-auto max-w-full object-contain"
                                    />
                                </div>
                            </a>
                        ))}
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
