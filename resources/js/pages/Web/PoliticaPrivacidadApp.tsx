import { Head, Link } from '@inertiajs/react';
import WebLayout from './WebLayout';

export default function PoliticaPrivacidadApp() {
    return (
        <WebLayout>
            <Head title="Política de uso de la plataforma COMFACA En Línea" />

            <section className="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white py-16">
                <div className="container mx-auto px-4 md:max-w-7xl">
                    <div className="max-w-3xl">
                        <p className="text-sm font-medium text-emerald-100 mb-2">COMFACA</p>
                        <p className="text-sm text-emerald-100 mb-4">Caja de Compensación Familiar del Caquetá</p>
                        <h1 className="text-3xl md:text-4xl font-bold mb-3">Política de uso de la plataforma</h1>
                        <p className="text-lg text-emerald-100">“COMFACA EN LÍNEA”</p>
                    </div>
                </div>
            </section>

            <section className="py-12 md:py-16">
                <div className="container mx-auto px-4 md:max-w-3xl space-y-10 text-gray-600 leading-relaxed">
                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">1. Objetivo</h2>
                        <p>
                            Establecer las condiciones, responsabilidades y lineamientos de obligatorio cumplimiento para el acceso, registro y uso de la plataforma “Comfaca en línea”, portal virtual de autogestión de la Caja de Compensación Familiar del Caquetá (COMFACA), dirigido a empresas, empleadores, trabajadores dependientes e independientes, aportantes, pensionados, facultativos y particulares, con el fin de garantizar un uso seguro, responsable y conforme a la normatividad vigente.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">2. Alcance</h2>
                        <p>
                            La presente política aplica a todas las personas naturales o jurídicas que se registren y/o accedan a la plataforma “Comfaca en línea”, cualquiera que sea el rol bajo el cual ingresen (Empresa o Empleador, Trabajador Dependiente, Trabajador Independiente–Aportante, Pensionado, Facultativo o Particular), y comprende la totalidad de los trámites, consultas, descargas y transacciones que se realicen a través del portal.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">3. Definiciones</h2>
                        <ul className="list-disc pl-6 space-y-2">
                            <li>
                                <span className="font-medium text-gray-800">Comfaca en línea:</span> portal web de autogestión dispuesto por COMFACA para la realización de trámites y consultas relacionados con la afiliación y los servicios que ofrece la Caja.
                            </li>
                            <li>
                                <span className="font-medium text-gray-800">Usuario:</span> persona natural o jurídica registrada en la plataforma, que actúa en nombre propio o en representación de una empresa afiliada.
                            </li>
                            <li>
                                <span className="font-medium text-gray-800">Contraseña:</span> clave secreta, personal e intransferible, asociada al usuario, que permite el acceso a la plataforma.
                            </li>
                            <li>
                                <span className="font-medium text-gray-800">Firma electrónica:</span> código de validación personal requerido para autenticar y dar validez a los trámites realizados a través de la plataforma.
                            </li>
                            <li>
                                <span className="font-medium text-gray-800">Datos personales:</span> cualquier información vinculada o que pueda asociarse a una o varias personas naturales determinadas o determinables, tratada conforme a la Ley 1581 de 2012 y sus decretos reglamentarios.
                            </li>
                        </ul>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">4. Roles de acceso a la plataforma</h2>
                        <p className="mb-3">
                            La plataforma “Comfaca en línea” permite el ingreso bajo los siguientes roles, cada uno con funcionalidades específicas de acuerdo con su calidad frente a COMFACA:
                        </p>
                        <ul className="list-disc pl-6 space-y-2 mb-3">
                            <li>Empresa o Empleador.</li>
                            <li>Trabajador Dependiente.</li>
                            <li>Trabajador Independiente – Aportante.</li>
                            <li>Pensionado.</li>
                            <li>Facultativo.</li>
                            <li>Particular.</li>
                        </ul>
                        <p>
                            El usuario deberá seleccionar el rol que corresponda a su condición real frente a la Caja. El registro bajo un rol distinto al que le corresponde podrá acarrear la restricción o suspensión del acceso.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">5. Servicios disponibles en la plataforma</h2>
                        <p className="mb-3">
                            A través de “Comfaca en línea” los usuarios podrán realizar, entre otros, los siguientes trámites y consultas:
                        </p>
                        <ul className="list-disc pl-6 space-y-2">
                            <li>Afiliación de empresas, trabajadores dependientes, independientes y beneficiarios.</li>
                            <li>Reporte de novedades de afiliación.</li>
                            <li>Consulta de información de aportes, trabajadores afiliados, giro de cuota monetaria y beneficiarios.</li>
                            <li>Descarga de certificaciones del estado de afiliación.</li>
                            <li>Compra de los servicios ofrecidos por COMFACA.</li>
                            <li>Los demás trámites y servicios institucionales que la Caja disponga a través del portal.</li>
                        </ul>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">6. Registro, acceso y credenciales</h2>

                        <h3 className="text-base font-semibold text-gray-900 mt-4 mb-2">6.1 Creación de la cuenta</h3>
                        <p>
                            Para acceder a los servicios de la plataforma, el usuario deberá crear una cuenta suministrando información veraz, completa y actualizada, y deberá contar con un nombre de usuario, una contraseña y el código de firma electrónica, este último necesario para la validación de los trámites realizados.
                        </p>

                        <h3 className="text-base font-semibold text-gray-900 mt-4 mb-2">6.2 Carácter personal e intransferible</h3>
                        <p>
                            El usuario y la contraseña, así como la firma electrónica, son de carácter personal e intransferible, tanto para quienes actúan bajo el rol de empresa como para quienes actúan bajo el rol de trabajador o cualquier otro rol habilitado en la plataforma. Su uso es responsabilidad exclusiva de la persona titular de la cuenta o de quien esta haya autorizado formalmente para su manejo, en cuyo caso dicho responsable asumirá igualmente las obligaciones aquí establecidas.
                        </p>

                        <h3 className="text-base font-semibold text-gray-900 mt-4 mb-2">6.3 Recuperación y actualización de credenciales</h3>
                        <p>
                            La plataforma dispone de las opciones “Olvidé mi clave” y “Solicitar cambio de correo” para la recuperación y actualización de credenciales. El usuario deberá mantener actualizada la información de contacto asociada a su cuenta para garantizar el correcto uso de dichas opciones.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">7. Responsabilidades del usuario</h2>
                        <p className="mb-3">El usuario de la plataforma “Comfaca en línea” se compromete a:</p>
                        <ol className="list-decimal pl-6 space-y-2">
                            <li>Custodiar de manera diligente su usuario, contraseña y firma electrónica, absteniéndose de compartirlos, cederlos o divulgarlos a terceros.</li>
                            <li>Utilizar la plataforma exclusivamente para los fines para los cuales fue dispuesta por COMFACA.</li>
                            <li>Suministrar información veraz, completa y actualizada en los formularios y documentos que adjunte al sistema.</li>
                            <li>Notificar de manera inmediata a COMFACA cualquier uso no autorizado de sus credenciales o cualquier situación que comprometa la seguridad de su cuenta.</li>
                            <li>Asumir la responsabilidad por los trámites y transacciones realizados con su usuario, contraseña y firma electrónica, los cuales se entienden efectuados por el titular de la cuenta o por quien este haya autorizado.</li>
                            <li>Abstenerse de realizar cualquier práctica que atente contra la seguridad, disponibilidad o integridad de la plataforma.</li>
                            <li>Cerrar la sesión al finalizar el uso de la plataforma, especialmente cuando se acceda desde equipos de uso compartido o público.</li>
                        </ol>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">8. Tratamiento de datos personales</h2>
                        <p className="mb-3">
                            Toda la información que el usuario ingrese en los formularios de la plataforma, así como los documentos que adjunte, está protegida bajo los principios y disposiciones de la Ley 1581 de 2012 y sus decretos reglamentarios, relativos al Régimen General de Protección de Datos Personales.
                        </p>
                        <p className="mb-3">
                            COMFACA tratará los datos personales recolectados a través de “Comfaca en línea” con las finalidades propias de la gestión de afiliación, prestación de servicios y cumplimiento de obligaciones legales y contractuales, garantizando su confidencialidad, seguridad y uso adecuado, de conformidad con la Política de Tratamiento de Datos Personales de la Caja.
                        </p>
                        <p>
                            El usuario podrá ejercer los derechos de acceso, actualización, rectificación y supresión de sus datos personales, así como revocar la autorización otorgada, en los términos previstos por la ley y por la política de tratamiento de datos personales de COMFACA.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">9. Aceptación de la política</h2>
                        <p className="mb-3">
                            El acceso y uso de la plataforma “Comfaca en línea” requiere la aceptación previa, expresa e inequívoca de la presente política de uso. La aceptación se entenderá otorgada al momento en que el usuario marque la opción correspondiente y continúe con el proceso de ingreso o registro en la plataforma.
                        </p>
                        <p>La no aceptación de la política impedirá el acceso a los servicios dispuestos en el portal.</p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">10. Restricciones de uso</h2>
                        <p className="mb-3">Queda prohibido el uso de la plataforma para:</p>
                        <ul className="list-disc pl-6 space-y-2">
                            <li>Suministrar información falsa, inexacta o de terceros sin la debida autorización.</li>
                            <li>Suplantar la identidad de otro usuario o de un tercero.</li>
                            <li>Realizar actividades que vulneren la normatividad vigente o los derechos de terceros.</li>
                            <li>Intentar vulnerar, alterar o afectar la seguridad, el funcionamiento o la infraestructura tecnológica de la plataforma.</li>
                        </ul>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">11. Vigencia y modificaciones</h2>
                        <p>
                            COMFACA podrá modificar la presente política en cualquier momento, con el fin de ajustarla a cambios normativos, tecnológicos u operativos. Las modificaciones serán publicadas en la plataforma y se entenderán aceptadas por el usuario al continuar haciendo uso de “Comfaca en línea” con posterioridad a su publicación.
                        </p>
                    </article>

                    <article>
                        <h2 className="text-xl font-semibold text-gray-900 mb-3">12. Contacto</h2>
                        <p>
                            Para consultas, solicitudes o inquietudes relacionadas con el uso de la plataforma “Comfaca en línea” o con el tratamiento de datos personales, el usuario podrá comunicarse a través de los canales de atención dispuestos oficialmente por la Caja de Compensación Familiar del Caquetá (COMFACA).
                        </p>
                    </article>

                    <p className="text-sm italic text-gray-500">
                        Documento sujeto a la aceptación obligatoria del usuario como condición de acceso a la plataforma “Comfaca en línea”.
                    </p>

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
