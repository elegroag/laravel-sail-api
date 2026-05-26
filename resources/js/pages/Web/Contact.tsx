import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/react';
import WebLayout from './WebLayout';

export default function Contact() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/web/contact/send', {
            onSuccess: () => {
                alert('Mensaje enviado correctamente.');
                setData({ name: '', email: '', subject: '', message: '' });
            },
            onError: (err) => {
                const msg = Object.values(err).join(', ');
                alert('Error al enviar: ' + msg);
            },
        });
    };

    return (
        <WebLayout>
            {/* Hero */}
            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-20">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <h1 className="text-4xl md:text-5xl font-bold mb-4">Contáctenos</h1>
                        <p className="text-lg text-emerald-100">
                            Estamos listos para atender sus consultas
                        </p>
                    </div>
                </div>
            </section>

            {/* Contenido */}
            <section className="py-16">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-5xl">
                        {/* Info de contacto */}
                        <div>
                            <h2 className="text-2xl font-semibold text-gray-900 mb-6">Información de contacto</h2>

                            <div className="space-y-6">
                                <div className="flex items-start gap-4">
                                    <div className="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-1">
                                        <span className="text-lg">📍</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Dirección</h3>
                                        <p className="text-gray-500 text-sm">Cra. 11 #10-34,Florencia, Caquetá, Colombia</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-4">
                                    <div className="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-1">
                                        <span className="text-lg">📞</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Teléfono</h3>
                                        <p className="text-gray-500 text-sm">(608) 436 6300 EXT 1061</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-4">
                                    <div className="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-1">
                                        <span className="text-lg">✉️</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Correo electrónico</h3>
                                        <p className="text-gray-500 text-sm">afiliacionyregistro@comfaca.com</p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-4">
                                    <div className="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-1">
                                        <span className="text-lg">🕐</span>
                                    </div>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Horario de atención</h3>
                                        <p className="text-gray-500 text-sm">Lunes a Viernes: 8:00 AM – 5:00 PM</p>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-8 p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                                <h3 className="font-medium text-emerald-800 mb-2">Portal en línea</h3>
                                <p className="text-sm text-emerald-700">
                                    Si ya está afiliado, acceda a{' '}
                                    <a href="/web/login" className="underline font-medium">
                                        nuestro portal
                                    </a>{' '}
                                    para gestionar sus trámites sin desplazarse.
                                </p>
                            </div>
                        </div>

                        {/* Formulario */}
                        <div>
                            <h2 className="text-2xl font-semibold text-gray-900 mb-6">Envíenos un mensaje</h2>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div>
                                    <Label htmlFor="name">Nombre completo</Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        placeholder="Su nombre"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                    />
                                    {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                                </div>
                                <div>
                                    <Label htmlFor="email">Correo electrónico</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        placeholder="correo@ejemplo.com"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                    />
                                    {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                                </div>
                                <div>
                                    <Label htmlFor="subject">Asunto</Label>
                                    <Input
                                        id="subject"
                                        type="text"
                                        placeholder="Asunto del mensaje"
                                        value={data.subject}
                                        onChange={(e) => setData('subject', e.target.value)}
                                    />
                                    {errors.subject && <p className="text-red-500 text-sm mt-1">{errors.subject}</p>}
                                </div>
                                <div>
                                    <Label htmlFor="message">Mensaje</Label>
                                    <textarea
                                        id="message"
                                        rows={5}
                                        className="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Escriba su mensaje aquí..."
                                        value={data.message}
                                        onChange={(e) => setData('message', e.target.value)}
                                    />
                                    {errors.message && <p className="text-red-500 text-sm mt-1">{errors.message}</p>}
                                </div>
                                <Button type="submit" className="w-full bg-emerald-600 hover:bg-emerald-700" disabled={processing}>
                                    {processing ? 'Enviando...' : 'Enviar mensaje'}
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </WebLayout>
    );
}
