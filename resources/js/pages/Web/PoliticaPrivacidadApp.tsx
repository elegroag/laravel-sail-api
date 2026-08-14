import { Head, Link } from '@inertiajs/react';
import WebLayout from './WebLayout';

const sections = [
    {
        title: '1. Identificación del responsable del tratamiento',
        body: [
            'La Caja de Compensación Familiar del Caquetá – COMFACA, identificada con NIT 891.190.047-2, con domicilio en la Cra. 11 No. 10-34, Florencia, Caquetá, Colombia, correo electrónico afiliacionyregistro@comfaca.com y teléfono (608) 436 6300, actúa como Responsable del Tratamiento de los datos personales recolectados a través de la plataforma COMFACA En Línea, de conformidad con la Ley 1581 de 2012, el Decreto 1377 de 2013 y el Decreto Único Reglamentario 1074 de 2015.',
        ],
    },
    {
        title: '2. Marco normativo',
        body: [
            'El tratamiento de datos personales se rige principalmente por la Ley Estatutaria 1581 de 2012 “Por la cual se dictan disposiciones generales para la protección de datos personales”, sus decretos reglamentarios y las instrucciones de la Superintendencia de Industria y Comercio (SIC). COMFACA garantiza el respeto de los principios de legalidad, finalidad, libertad, veracidad o calidad, transparencia, acceso y circulación restringida, seguridad y confidencialidad.',
        ],
    },
    {
        title: '3. Alcance',
        body: [
            'La presente Política de Tratamiento de Datos Personales aplica a todas las personas naturales cuyos datos sean recolectados, almacenados, usados, circulados o suprimidos en el ejercicio de las funciones de COMFACA a través de COMFACA En Línea, incluyendo empleadores, trabajadores, independientes, pensionados, facultativos, trabajadores domésticos, beneficiarios, cónyuges o compañeros permanentes, usuarios registrados y visitantes de la plataforma.',
        ],
    },
    {
        title: '4. Definiciones relevantes',
        body: [
            'Para efectos de esta política se adoptan, entre otras, las definiciones de la Ley 1581 de 2012: Dato personal, Dato público, Dato semiprivado, Dato privado, Dato sensible, Titular, Responsable del Tratamiento, Encargado del Tratamiento, Tratamiento, Autorización, Aviso de privacidad y Base de Datos.',
        ],
    },
    {
        title: '5. Datos personales objeto de tratamiento',
        body: [
            'Según el tipo de trámite o servicio, COMFACA podrá tratar datos de identificación (nombres, apellidos, tipo y número de documento), datos de contacto (dirección, correo electrónico, teléfono o celular, ciudad), datos laborales y de afiliación (NIT o razón social de la empresa, cargo, tipo de vinculación), datos de autenticación (usuario, contraseñas cifradas, códigos de verificación) y, cuando resulte necesario y con la autorización correspondiente, datos sensibles o de menores de edad conforme a la ley.',
            'Asimismo, podrán recolectarse datos técnicos de conexión o dispositivo (dirección IP, tipo de navegador, sistema operativo, identificadores de sesión e información de logs) necesarios para la seguridad, auditoría y correcto funcionamiento de la plataforma.',
        ],
    },
    {
        title: '6. Finalidades del tratamiento',
        body: [
            'Los datos personales se tratan para: (i) gestionar afiliaciones, novedades, actualizaciones y consultas relacionadas con el Sistema de Compensación Familiar; (ii) autenticar usuarios y garantizar la seguridad de las cuentas; (iii) atender solicitudes, PQRS y ejercicio de derechos de habeas data; (iv) enviar notificaciones relacionadas con trámites, estados de solicitud y comunicaciones institucionales; (v) cumplir obligaciones legales, contractuales y de supervisión; (vi) generar estadísticas e indicadores de gestión con datos agregados o anonimizados cuando corresponda; y (vii) prevenir fraudes, accesos no autorizados e incidentes de seguridad de la información.',
        ],
    },
    {
        title: '7. Autorización',
        body: [
            'Salvo las excepciones legales, el tratamiento de datos personales requiere autorización previa, expresa e informada del Titular. Dicha autorización podrá obtenerse por medios escritos, electrónicos, digitales o mediante conductas inequívocas que permitan concluir de forma razonable que fue otorgada, de acuerdo con el artículo 9 de la Ley 1581 de 2012 y sus normas reglamentarias.',
            'El Titular podrá revocar la autorización y/o solicitar la supresión del dato cuando no exista un deber legal o contractual que impida eliminarlo, siguiendo el procedimiento previsto en esta política.',
        ],
    },
    {
        title: '8. Derechos de los titulares',
        body: [
            'De conformidad con el artículo 8 de la Ley 1581 de 2012, el Titular tiene derecho a: conocer, actualizar y rectificar sus datos personales; solicitar prueba de la autorización otorgada; ser informado sobre el uso que se ha dado a sus datos; presentar quejas ante la SIC; revocar la autorización y/o solicitar la supresión del dato cuando sea procedente; y acceder en forma gratuita a sus datos personales que hayan sido objeto de tratamiento.',
        ],
    },
    {
        title: '9. Procedimiento para el ejercicio de derechos',
        body: [
            'Para ejercer sus derechos, el Titular o su representante legal podrá presentar consulta o reclamo ante COMFACA a través del correo afiliacionyregistro@comfaca.com, de manera presencial en la Cra. 11 No. 10-34, Florencia, Caquetá, o por los canales institucionales habilitados, indicando nombre completo, documento de identidad, descripción clara de la solicitud y datos de contacto para respuesta.',
            'Las consultas serán atendidas en un término máximo de diez (10) días hábiles contados a partir de su recepción, prorrogables por hasta cinco (5) días hábiles adicionales previa comunicación al interesado. Los reclamos serán atendidos en un término máximo de quince (15) días hábiles, prorrogables por ocho (8) días hábiles adicionales en los términos del artículo 15 de la Ley 1581 de 2012.',
        ],
    },
    {
        title: '10. Seguridad de la información',
        body: [
            'COMFACA adopta medidas técnicas, humanas y administrativas razonables para proteger los datos personales frente a acceso no autorizado, pérdida, alteración, destrucción o uso indebido. El acceso a COMFACA En Línea se realiza mediante autenticación de usuarios, y las contraseñas se gestionan con mecanismos de cifrado o resguardo adecuados. Sin perjuicio de lo anterior, ningún sistema es absolutamente invulnerable; el usuario también es responsable de custodiar sus credenciales y de notificar cualquier uso indebido de su cuenta.',
        ],
    },
    {
        title: '11. Acceso a la plataforma mediante navegador web y dispositivos móviles',
        body: [
            'COMFACA En Línea es una plataforma digital institucional diseñada para permitir a afiliados, empleadores y demás usuarios autorizados gestionar trámites y consultas relacionados con los servicios de la Caja. El acceso puede realizarse a través de un navegador web en computadores o dispositivos móviles, o mediante aplicaciones y entornos compatibles publicados o autorizados por COMFACA para uso en teléfonos inteligentes y tabletas.',
            'Con independencia del canal de acceso (web o móvil), aplican las mismas reglas de tratamiento de datos personales previstas en esta política y en la Ley 1581 de 2012. Al ingresar, autenticarse o utilizar la plataforma desde cualquier dispositivo, el usuario reconoce que la información suministrada y la generada por el uso del servicio (incluida información técnica de sesión, dispositivo o conectividad necesaria para la operación y seguridad) será tratada conforme a las finalidades aquí descritas. COMFACA podrá actualizar controles de seguridad, versiones de la aplicación o requisitos técnicos del navegador/dispositivo para preservar la integridad, disponibilidad y confidencialidad del servicio, sin que ello implique una disminución de los derechos del Titular.',
        ],
    },
    {
        title: '12. Transferencia y transmisión de datos',
        body: [
            'COMFACA no venderá datos personales. Podrá compartir o transmitir datos a encargados del tratamiento, autoridades competentes o terceros cuando exista autorización, mandato legal, contractual o necesidad técnica para la prestación del servicio, siempre bajo obligaciones de confidencialidad y seguridad compatibles con la normativa vigente.',
        ],
    },
    {
        title: '13. Vigencia de la política y de las bases de datos',
        body: [
            'La presente política rige a partir de su publicación en la ruta /web/politica-privacidad-app y permanecerá vigente mientras COMFACA En Línea se encuentre en operación, sin perjuicio de actualizaciones que se publiquen en el mismo canal. Las bases de datos se conservarán por el tiempo necesario para cumplir las finalidades informadas y las obligaciones legales o contractuales aplicables.',
        ],
    },
    {
        title: '14. Contacto del área de protección de datos',
        body: [
            'Para consultas, reclamos o solicitudes relacionadas con el tratamiento de datos personales, el Titular puede comunicarse con COMFACA al correo afiliacionyregistro@comfaca.com, teléfono (608) 436 6300 EXT 1061, o en la sede principal ubicada en Cra. 11 No. 10-34, Florencia, Caquetá.',
        ],
    },
];

export default function PoliticaPrivacidadApp() {
    return (
        <WebLayout>
            <Head title="Política de Tratamiento de Datos Personales" />

            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-16">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <p className="text-sm font-medium text-emerald-100 mb-2">COMFACA En Línea</p>
                        <h1 className="text-3xl md:text-4xl font-bold mb-4">
                            Política de Tratamiento de Datos Personales
                        </h1>
                        <p className="text-emerald-100">
                            Conforme a la Ley 1581 de 2012 y normas complementarias. Aplicable al uso de la
                            plataforma mediante navegador web y dispositivos móviles.
                        </p>
                    </div>
                </div>
            </section>

            <section className="py-12 md:py-16">
                <div className="container mx-auto px-4 md:max-w-3xl space-y-10">
                    <p className="text-sm text-gray-500">
                        Última actualización: {new Date().toLocaleDateString('es-CO', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        })}
                    </p>

                    {sections.map((section) => (
                        <article key={section.title}>
                            <h2 className="text-xl font-semibold text-gray-900 mb-3">{section.title}</h2>
                            {section.body.map((paragraph) => (
                                <p key={paragraph.slice(0, 48)} className="text-gray-600 mb-3 leading-relaxed">
                                    {paragraph}
                                </p>
                            ))}
                        </article>
                    ))}

                    <div className="rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                        <p className="text-sm text-emerald-900 leading-relaxed">
                            Al utilizar COMFACA En Línea usted declara haber leído esta política. Si no está de
                            acuerdo con el tratamiento descrito, absténgase de usar la plataforma o ejerza sus
                            derechos a través de los canales indicados.
                        </p>
                    </div>

                    <div className="pt-2">
                        <Link href="/web/about" className="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Volver al inicio
                        </Link>
                    </div>
                </div>
            </section>
        </WebLayout>
    );
}
