import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import WebLayout from './WebLayout';

const servicios = [
    {
        id: 'empresa',
        title: 'Afiliación de Empresa',
        description:
            'Registre su empresa y afilie a sus trabajadores dependientes de manera rápida y sin complicaciones.',
        icon: '🏢',
        features: ['Registro empresarial', 'Afiliación de empleados', 'Reporte de novedades', 'Gestión de cuentas de cobro'],
        cta: 'Registrar empresa',
        href: '/web/register/company',
    },
    {
        id: 'trabajador',
        title: 'Afiliación Trabajador Dependiente',
        description:
            'Si es trabajador dependiente, su empleador puede afiliarlo directamente a través del portal.',
        icon: '👔',
        features: ['Afiliación por empleado', 'Verificación de datos', 'Consulta de estado', 'Seguimiento de solicitud'],
        cta: 'Más información',
        href: '/web/register/worker',
    },
    {
        id: 'independiente',
        title: 'Afiliación Trabajador Independiente',
        description:
            'Afiliese como trabajador independiente y acceda a los beneficios de COMFACA.',
        icon: '💼',
        features: ['Afiliación directa', 'Pago de cotizaciones', 'Programas de bienestar', 'Crédito social'],
        cta: 'Afiliarse como independiente',
        href: '/web/register/independiente',
    },
    {
        id: 'pensionado',
        title: 'Afiliación Pensionado',
        description:
            'Los pensionados por vejez, invalidez o sobrevivientes también pueden afiliarse y disfrutar de nuestros servicios.',
        icon: '🎓',
        features: ['Afiliación simplificada', 'Programas recreativos', 'Descuentos en comercio', 'Turismo social'],
        cta: 'Afiliarse como pensionado',
        href: '/web/register/pensionado',
    },
    {
        id: 'facultativo',
        title: 'Afiliación Facultativo',
        description:
            'Trabajadores independientes no cubiertos por el Sistema de Seguridad Social Integral pueden afiliarse de forma facultativa.',
        icon: '📋',
        features: ['Cotización directa', 'Acceso a servicios', 'Seguimiento en línea', 'Gestión de pagos'],
        cta: 'Más información',
        href: '/web/register/facultativo',
    },
    {
        id: 'domestico',
        title: 'Afiliación Trabajador Doméstico',
        description:
            'Trabajadores del servicio doméstico pueden ser afiliados por sus empleadores.',
        icon: '🏠',
        features: ['Afiliación simplificada', 'Beneficios para el hogar', 'Programas de capacitación', 'Recreación'],
        cta: 'Más información',
        href: '/web/register/domestico',
    },
    {
        id: 'consultas',
        title: 'Consultas de Información',
        description:
            'Consulte en línea el estado de sus afiliados, novedades reportadas y nuestro historial de servicios.',
        icon: '🔍',
        features: ['Consulta de afiliados', 'Estado de solicitudes', 'Historial de novedades', 'Reportes en línea'],
        cta: 'Acceder al portal',
        href: '/web/login',
    },
    {
        id: 'beneficiario',
        title: 'Afiliación de Beneficiarios',
        description:
            'Registre a sus beneficiarios: hijos menores, cónyuge o compañero permanente, según las normas vigentes.',
        icon: '👨‍👩‍👧‍👦',
        features: ['Registro de beneficiarios', 'Actualización de datos', 'Programas para la familia', 'Seguimiento en línea'],
        cta: 'Más información',
        href: '/web/login',
    },
];

export default function Products() {
    return (
        <WebLayout>
            {/* Hero */}
            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-20">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">Servicios y Afiliaciones</h1>
                        <p className="text-lg text-emerald-100">
                            Conozca todos los servicios que COMFACA ofrece para usted y su familia
                        </p>
                    </div>
                </div>
            </section>

            {/* Grid de servicios */}
            <section className="py-16">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {servicios.map((servicio) => (
                            <div
                                key={servicio.id}
                                className="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col"
                            >
                                {/* Header */}
                                <div className="p-6 flex-1">
                                    <div className="flex items-center gap-3 mb-4">
                                        <div className="h-12 w-12 rounded-lg bg-emerald-100 flex items-center justify-center text-2xl">
                                            {servicio.icon}
                                        </div>
                                        <h3 className="font-semibold text-gray-900 leading-tight">{servicio.title}</h3>
                                    </div>
                                    <p className="text-sm text-gray-500 mb-4">{servicio.description}</p>
                                    <ul className="space-y-1.5">
                                        {servicio.features.map((feature) => (
                                            <li key={feature} className="flex items-center gap-2 text-sm text-gray-500">
                                                <div className="h-1.5 w-1.5 rounded-full bg-emerald-500 flex-shrink-0" />
                                                {feature}
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Footer */}
                                <div className="px-6 pb-6">
                                    <Link href={servicio.href}>
                                        <Button
                                            variant={servicio.id === 'empresa' ? 'default' : 'outline'}
                                            className="w-full bg-emerald-600 hover:bg-emerald-700"
                                        >
                                            {servicio.cta}
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA final */}
            <section className="py-16 bg-emerald-600 text-white">
                <div className="container mx-auto px-4 md:max-w-7xl max-w-3xl text-center">
                    <h2 className="text-3xl font-bold mb-4">¿Ya tiene cuenta?</h2>
                    <p className="text-emerald-100 mb-6">
                        Inicie sesión en el portal y gestione todos sus trámites en línea sin desplazarse.
                    </p>
                    <Link href="/web/login">
                        <Button size="lg" className="bg-white text-emerald-700 hover:bg-emerald-50 font-semibold px-8">
                            Ingresar al portal
                        </Button>
                    </Link>
                </div>
            </section>
        </WebLayout>
    );
}
