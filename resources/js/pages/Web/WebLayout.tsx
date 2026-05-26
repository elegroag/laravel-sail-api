import PublicHeader from './PublicHeader';
import ComfacaLogo from '@/components/ComfacaLogo';

export default function WebLayout({ children }: { children: React.ReactNode }) {
    const currentYear = new Date().getFullYear();

    return (
        <div className="min-h-screen flex flex-col">
            <PublicHeader />

            <main className="flex-1">{children}</main>

            <footer className="border-t border-gray-200 bg-gray-50 py-10">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        {/* Logo y descripción */}
                        <div>
                            <div className="flex items-center mb-3">
                                <ComfacaLogo className="h-16 w-auto" />
                            </div>
                            <p className="text-sm text-gray-500">
                                Caja de Compensación Familiar del Caquetá. Trabajamos por el bienestar de las familias colombianas.
                            </p>
                        </div>

                        {/* Enlaces rápida */}
                        <div>
                            <h3 className="font-semibold text-gray-900 mb-3">Servicios</h3>
                            <ul className="space-y-2">
                                <li>
                                    <a href="/web/products" className="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                                        Afiliación de trabajadores
                                    </a>
                                </li>
                                <li>
                                    <a href="/web/products" className="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                                        Afiliación independiente
                                    </a>
                                </li>
                                <li>
                                    <a href="/web/products" className="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                                        Afiliación pensionado
                                    </a>
                                </li>
                                <li>
                                    <a href="/web/products" className="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                                        Afiliación trabajador doméstico
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {/* Contacto */}
                        <div>
                            <h3 className="font-semibold text-gray-900 mb-3">Contáctenos</h3>
                            <ul className="space-y-2 text-sm text-gray-500">
                                <li>📍 Cra. 11 #10-34,Florencia, Caquetá</li>
                                <li>📞 (608) 436 6300 EXT 1061</li>
                                <li>✉️ afiliacionyregistro@comfaca.com</li>
                            </ul>
                        </div>
                    </div>

                    <div className="border-t border-gray-200 pt-6 text-center">
                        <p className="text-sm text-gray-500">
                            &copy; {currentYear} COMFACA – Caja de Compensación Familiar del Caquetá. Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
