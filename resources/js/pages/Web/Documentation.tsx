import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import WebLayout from './WebLayout';

interface Video {
    id: string;
    title: string;
    description: string;
    video: string;
    duration: string;
}

interface Manual {
    id: string;
    title: string;
    description: string;
    file: string;
    size: string;
}

interface Props {
    videos: Video[];
    manuales: Manual[];
}

export default function Documentation({ videos, manuales }: Props) {
    return (
        <WebLayout>
            {/* Hero */}
            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-20">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">Documentación y Guías</h1>
                        <p className="text-lg text-emerald-100">
                            Videos tutoriales y manuales para aprovechar al máximo el portal COMFACA En Línea
                        </p>
                    </div>
                </div>
            </section>

            {/* Videos */}
            <section className="py-16">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-2">Videos tutoriales</h2>
                    <p className="text-gray-500 mb-8">
                        Vea nuestras guías paso a paso para aprender a usar el portal
                    </p>

                    {videos.length === 0 ? (
                        <div className="text-center py-12 text-gray-400">
                            <p className="text-lg">🎥 Próximamente nuevos videos tutoriales</p>
                            <p className="text-sm mt-2">Los videos estará disponibles en breve</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {videos.map((video) => (
                                <div key={video.id} className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                    {/* Player */}
                                    <div className="relative bg-gray-900 aspect-video flex items-center justify-center">
                                        <video
                                            controls
                                            preload="metadata"
                                            className="w-full h-full object-contain"
                                            src={video.video}
                                            onError={(e) => {
                                                const target = e.currentTarget;
                                                target.style.display = 'none';
                                                const placeholder = target.nextElementSibling as HTMLElement;
                                                if (placeholder) placeholder.style.display = 'flex';
                                            }}
                                        />
                                        <div className="hidden absolute inset-0 flex-col items-center justify-center bg-gray-800 text-gray-400 gap-3">
                                            <span className="text-4xl">🎥</span>
                                            <p className="text-sm">Video no disponible</p>
                                        </div>
                                    </div>

                                    {/* Info */}
                                    <div className="p-5">
                                        <div className="flex items-start justify-between gap-2 mb-2">
                                            <h3 className="font-semibold text-gray-900">{video.title}</h3>
                                            <span className="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full flex-shrink-0">
                                                {video.duration}
                                            </span>
                                        </div>
                                        <p className="text-sm text-gray-500">{video.description}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* Manuales */}
            <section className="py-16 bg-gray-50">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-2">Manuales para descargar</h2>
                    <p className="text-gray-500 mb-8">
                        Descargue las guías completas en formato PDF
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {manuales.map((manual) => (
                            <div key={manual.id} className="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                                <div className="h-12 w-12 rounded-lg bg-red-50 flex items-center justify-center mb-4 flex-shrink-0">
                                    <span className="text-2xl">📄</span>
                                </div>
                                <h3 className="font-semibold text-gray-900 mb-2">{manual.title}</h3>
                                <p className="text-sm text-gray-500 mb-4 flex-1">{manual.description}</p>
                                <div className="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                    <span className="text-xs text-gray-400">{manual.size}</span>
                                    <a
                                        href={manual.file}
                                        download
                                        className="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
                                    >
                                        Descargar PDF
                                        <span>↓</span>
                                    </a>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA final */}
            <section className="py-16 bg-emerald-600 text-white">
                <div className="container mx-auto px-4 md:max-w-7xl max-w-3xl text-center">
                    <h2 className="text-3xl font-bold mb-4">¿Necesita más ayuda?</h2>
                    <p className="text-emerald-100 mb-6">
                        Contáctenos y nuestro equipo le asistirá en lo que necesite
                    </p>
                    <Link href="/web/contact">
                        <Button size="lg" className="bg-white text-emerald-700 hover:bg-emerald-50 font-semibold px-8">
                            Contáctenos
                        </Button>
                    </Link>
                </div>
            </section>
        </WebLayout>
    );
}
