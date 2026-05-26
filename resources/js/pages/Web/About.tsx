import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import WebLayout from './WebLayout';

export default function About() {
    return (
        <WebLayout>
            {/* Hero */}
            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-20">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">Sobre Nosotros</h1>
                        <p className="text-lg text-emerald-100">
                            Más de 50 años al servicio de la comunidad del Caquetá
                        </p>
                    </div>
                </div>
            </section>

            {/* Contenido */}
            <section className="py-16">
                <div className="container mx-auto px-4 md:max-w-7xl max-w-3xl">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-4">¿Quiénes somos?</h2>
                    <p className="text-gray-600 mb-6">
                        La Caja de Compensación Familiar del Caquetá – COMFACA, es una empresa
                        social sin ánimo de lucro, vigilada por la Superintendencia del Subsidio
                        Familiar, cuya función principal es administrar los recursos provenientes
                        de las cotizaciones de los empleadores y brindar servicios de bienestar social
                        a los trabajadores beneficiarios y sus familias.
                    </p>

                    <h2 className="text-2xl font-semibold text-gray-900 mb-4">Nuestra Misión</h2>
                    <p className="text-gray-600 mb-6">
                        Administrar con eficiencia y transparencia los recursos del subsidio
                        familiar, y prestar servicios de bienestar social con criterios de
                        calidad, pertinencia y enfoque diferencial, buscando el mejoramiento
                        de la calidad de vida de nuestros afiliados y sus familias.
                    </p>

                    <h2 className="text-2xl font-semibold text-gray-900 mb-4">Nuestros Valores</h2>
                    <ul className="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                        {[
                            'Compromiso con el usuario',
                            'Transparencia',
                            'Eficiencia en la gestión',
                            'Vocación de servicio',
                            'Respeto y dignidad',
                            'Equidad e inclusión',
                        ].map((valor) => (
                            <li key={valor} className="flex items-center gap-2">
                                <div className="h-2 w-2 rounded-full bg-emerald-600 flex-shrink-0" />
                                {valor}
                            </li>
                        ))}
                    </ul>
                </div>
            </section>

            {/* Servicios destacados */}
            <section className="py-16 bg-gray-50">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-8 text-center">Servicios que ofrecemos</h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {[
                            {
                                title: 'Afiliación',
                                desc: 'Afiliación de empresas, trabajadores dependientes, independientes, pensionados y trabajadores domésticos.',
                            },
                            {
                                title: 'Crédito Social',
                                desc: 'Líneas de crédito con tasas preferenciales para la vivienda, educación y necesidades familiares.',
                            },
                            {
                                title: 'Recreación',
                                desc: 'Programas recreativos, culturales y turísticos para el bienestar de su familia.',
                            },
                        ].map((item) => (
                            <div key={item.title} className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                                <h3 className="font-semibold text-gray-900 mb-2">{item.title}</h3>
                                <p className="text-sm text-gray-500">{item.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA final */}
            <section className="py-16 bg-emerald-600 text-white">
                <div className="container mx-auto px-4 md:max-w-7xl max-w-3xl text-center">
                    <h2 className="text-3xl font-bold mb-4">¿Listo para afiliarse?</h2>
                    <p className="text-emerald-100 mb-6">
                        Conozca nuestros servicios y afíliese de forma rápida y segura a través de nuestro portal en línea.
                    </p>
                    <Link href="/web/products">
                        <Button size="lg" className="bg-white text-emerald-700 hover:bg-emerald-50 font-semibold px-8">
                            Ver servicios y afiliaciones
                        </Button>
                    </Link>
                </div>
            </section>
        </WebLayout>
    );
}
