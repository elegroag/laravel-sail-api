import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/pages/Auth/components/auth-welcome";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert";
import useNotyEmailController from "./controllers/use-noty-email-controller";

export default function NotyEmail({ errors }: any) {
  const {
    alertMessage,
    successMessage,
    processing,
    tipoAfiliado,
    setTipoAfiliado,
    documentType,
    setDocumentType,
    documento,
    setDocumento,
    nombre,
    setNombre,
    telefono,
    setTelefono,
    email,
    setEmail,
    novedad,
    setNovedad,
    documentTypeOptions,
    tipoAfiliadoOptions,
    handleSubmit,
    handleNewRequest,
  } = useNotyEmailController({
    errors,
  });

  return (
    <AuthLayout
      title="Solicitud de cambio de correo"
      description="Usa este formulario para solicitar el cambio de correo electrónico asociado a tu cuenta de Comfaca En Línea."
    >
      {/* Panel izquierdo - Bienvenida */}
      <div
        id="welcome"
        className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden"
      >
        <AuthWelcome
          title="CAMBIO DE CORREO"
          tagline="Comfaca En Línea"
          description={
            <>
              <p>
                Diligencia el siguiente formulario para solicitar el cambio del
                correo electrónico registrado en tu cuenta. Esta solicitud será
                revisada por nuestro equipo antes de aplicar el cambio.
              </p>
              <p>
                Por seguridad, es importante que la información ingresada sea
                veraz y esté actualizada.
              </p>
            </>
          }
          backHref={route("login")}
          backText="Volver al inicio de sesión"
        />
      </div>

      {/* Panel derecho - Formulario de solicitud */}
      <div className="lg:w-1/2 p-12 flex flex-col justify-center relative">
        <AuthBackgroundShapes />

        <div className="max-w-md mx-auto w-full space-y-6">
          <form
            onSubmit={handleSubmit}
            className={`space-y-4 ${successMessage ? 'hidden' : ''}`}
          >
            <div>
              <label className="block text-sm font-medium text-gray-700">
                Tipo de afiliado
              </label>
              <select
                className="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={tipoAfiliado}
                onChange={(e) => setTipoAfiliado(e.target.value as any)}
                required
              >
                <option value="">Seleccione una opción</option>
                {tipoAfiliadoOptions.map((opt) => (
                  <option key={opt.value} value={opt.value}>
                    {opt.label}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Tipo de documento
              </label>
              <select
                className="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={documentType}
                onChange={(e) => setDocumentType(e.target.value)}
                required
              >
                <option value="">Seleccione una opción</option>
                {documentTypeOptions.map((opt) => (
                  <option key={opt.value} value={opt.value}>
                    {opt.label}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Documento
              </label>
              <input
                type="number"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={documento}
                onChange={(e) => setDocumento(e.target.value)}
                placeholder="Número de documento"
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Nombre completo
              </label>
              <input
                type="text"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={nombre}
                onChange={(e) => setNombre(e.target.value)}
                placeholder="Nombre y apellidos"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Teléfono de contacto
              </label>
              <input
                type="number"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={telefono}
                onChange={(e) => setTelefono(e.target.value)}
                placeholder="Número de teléfono"
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Correo electrónico actual / real
              </label>
              <input
                type="email"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="correo@ejemplo.com"
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Novedad a reportar
              </label>
              <textarea
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                rows={4}
                value={novedad}
                onChange={(e) => setNovedad(e.target.value)}
                placeholder="Describe la novedad o motivo del cambio de correo"
                required
              />
            </div>

            <div className="pt-2">
              <button
                type="submit"
                disabled={processing}
                className="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70"
              >
                {processing ? 'Enviando solicitud...' : 'Solicitar cambio de correo'}
              </button>
            </div>
          </form>

          {/* Alert de éxito */}
          {successMessage && (
            <div className="mt-4">
              <Alert className="w-100 mx-auto border-emerald-200 bg-emerald-50">
                <AlertTitle className="text-emerald-800">Solicitud registrada</AlertTitle>
                <AlertDescription className="text-gray-700 space-y-2">
                  <p>{successMessage}</p>
                  <p className="text-sm text-gray-600">
                    Hemos recibido tu solicitud con la siguiente información:<br />
                    Documento: <span className="font-medium">{documento}</span><br />
                    Correo reportado: <span className="font-medium">{email}</span>
                  </p>
                  <button
                    type="button"
                    onClick={handleNewRequest}
                    className="mt-2 inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                  >
                    Registrar otra solicitud
                  </button>
                </AlertDescription>
              </Alert>
            </div>
          )}

          {/* Alert de error */}
          {alertMessage && (
            <div className="mt-4">
              <Alert
                variant="destructive"
                className="w-100 mx-auto border-red-200"
              >
                <AlertTitle>Error</AlertTitle>
                <AlertDescription className="text-gray-500">
                  {alertMessage}
                </AlertDescription>
              </Alert>
            </div>
          )}
        </div>
      </div>
    </AuthLayout>
  );
}

