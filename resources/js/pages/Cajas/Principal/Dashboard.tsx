import AppLayout from '@/layouts/AppLayout';
import { cajasCardBodyClass, cajasCardClass, cajasCardHeaderClass, cajasPageClass } from '@/pages/Cajas/styles/cajas-classes';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Legend,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

type ChartSeries = {
    data: number[];
    labels: string[];
};

type Props = {
    charts: {
        usuarios: ChartSeries;
        opcion: ChartSeries;
        rechazo: ChartSeries;
        laboral: ChartSeries;
    };
};

const CHART_COLORS = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'];

function toPieData(series: ChartSeries) {
    return series.labels.map((name, index) => ({
        name,
        value: Number(series.data[index] ?? 0),
    }));
}

function toBarData(series: ChartSeries) {
    return series.labels.map((name, index) => ({
        name,
        total: Number(series.data[index] ?? 0),
    }));
}

function hasValues(series: ChartSeries): boolean {
    return series.data.some((value) => Number(value) > 0);
}

function ChartEmpty() {
    return (
        <div className="flex h-[35vh] flex-col items-center justify-center text-center">
            <p className="text-sm font-medium text-foreground">Sin datos</p>
            <p className="mt-1 text-xs text-muted-foreground">No hay información disponible para mostrar.</p>
        </div>
    );
}

function DoughnutPanel({ kicker, title, series }: { kicker: string; title: string; series: ChartSeries }) {
    const data = toPieData(series);

    return (
        <div className={`${cajasCardClass} flex h-full flex-col`}>
            <div className={cajasCardHeaderClass}>
                <p className="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">{kicker}</p>
                <h2 className="text-sm font-semibold text-foreground">{title}</h2>
            </div>
            <div className={`${cajasCardBodyClass} flex-1`}>
                {!hasValues(series) ? (
                    <ChartEmpty />
                ) : (
                    <div className="h-[35vh] w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <PieChart>
                                <Pie
                                    data={data}
                                    dataKey="value"
                                    nameKey="name"
                                    cx="50%"
                                    cy="45%"
                                    innerRadius="52%"
                                    outerRadius="78%"
                                    paddingAngle={2}
                                >
                                    {data.map((entry, index) => (
                                        <Cell key={`${entry.name}-${index}`} fill={CHART_COLORS[index % CHART_COLORS.length]} />
                                    ))}
                                </Pie>
                                <Tooltip
                                    formatter={(value) => [Number(value), 'Cantidad']}
                                    contentStyle={{
                                        borderRadius: 8,
                                        border: '1px solid hsl(var(--border))',
                                        background: 'hsl(var(--card))',
                                    }}
                                />
                                <Legend verticalAlign="bottom" height={36} wrapperStyle={{ fontSize: 12 }} />
                            </PieChart>
                        </ResponsiveContainer>
                    </div>
                )}
            </div>
        </div>
    );
}

function BarPanel({ kicker, title, series }: { kicker: string; title: string; series: ChartSeries }) {
    const data = toBarData(series);

    return (
        <div className={`${cajasCardClass} flex h-full flex-col`}>
            <div className={cajasCardHeaderClass}>
                <p className="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">{kicker}</p>
                <h2 className="text-sm font-semibold text-foreground">{title}</h2>
            </div>
            <div className={`${cajasCardBodyClass} flex-1`}>
                {!hasValues(series) ? (
                    <ChartEmpty />
                ) : (
                    <div className="h-[35vh] w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={data} margin={{ top: 8, right: 8, left: 0, bottom: 8 }}>
                                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="rgba(148, 163, 184, 0.35)" />
                                <XAxis dataKey="name" tick={{ fill: '#64748b', fontSize: 11 }} interval={0} angle={-20} textAnchor="end" height={56} />
                                <YAxis allowDecimals={false} tick={{ fill: '#64748b', fontSize: 11 }} />
                                <Tooltip
                                    formatter={(value) => [Number(value), 'Total']}
                                    contentStyle={{
                                        borderRadius: 8,
                                        border: '1px solid hsl(var(--border))',
                                        background: 'hsl(var(--card))',
                                    }}
                                />
                                <Bar dataKey="total" radius={[6, 6, 0, 0]}>
                                    {data.map((entry, index) => (
                                        <Cell key={`${entry.name}-${index}`} fill={CHART_COLORS[index % CHART_COLORS.length]} />
                                    ))}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                )}
            </div>
        </div>
    );
}

export default function Dashboard({ charts }: Props) {
    return (
        <AppLayout title="Estadística">
            <div className={cajasPageClass}>
                <div>
                    <h1 className="text-base font-semibold text-foreground">Estadística</h1>
                    <p className="text-sm text-muted-foreground">Resumen de usuarios, opciones, rechazos y carga laboral</p>
                </div>

                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <DoughnutPanel kicker="Registro" title="Usuarios Registrados" series={charts.usuarios} />
                    <DoughnutPanel kicker="Opción" title="Más Usada" series={charts.opcion} />
                    <DoughnutPanel kicker="Motivo Rechazo" title="Más Usado" series={charts.rechazo} />
                    <BarPanel kicker="Carga" title="Laboral" series={charts.laboral} />
                </div>
            </div>
        </AppLayout>
    );
}
