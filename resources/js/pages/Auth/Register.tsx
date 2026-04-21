import AuthLayout from "@/layouts/AuthLayoutTemplate";
import AuthWelcome from "@/pages/Auth/components/generic/AuthWelcome";
import AuthUserTypeSelector from "@/pages/Auth/components/generic/AuthUserTypeSelector";
import PersonRegisterForm from "@/pages/Auth/components/register/DatosPersonalesRegister";
import LoadingAnimated from "@/components/loading-animated";
import imageLogo from "@/assets/comfaca-logo.png";
import { userTypes } from "@/constants/auth";
import type { FormState, LoginProps} from "@/types/auth";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";
import useRegisterController from "@/pages/Auth/hooks/useRegisterController";
import { router } from "@inertiajs/react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";

export default function Register(props: LoginProps){

  const {
    dispatch,
    state,
    collections,
    events,
    domRef,
    dialog,
    setDialog,
    step
  } = useRegisterController(props);

  return (
    <>
    <AuthLayout title="REGISTRO COMFACA EN LÍNEA" description="Crea tu cuenta para acceder a todos los servicios y beneficios que Comfaca tiene para ofrecerte. Un proceso simple y seguro para comenzar tu experiencia.">
      <div id="welcome" className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
        <AuthWelcome
          title="REGISTRO"
          tagline="Únete a Comfaca En Línea"
          description={
            <p>
              Cree su cuenta y acceda a COMFACA de forma segura y eficiente para la gestión de sus trámites y servicios.
            </p>
          }
          backHref={route('login')}
          backText="¿Ya tienes cuenta? Inicia sesión"
        />
      </div>
      <div id="register" className={`p-8 flex flex-col justify-center relative overflow-y-auto max-h-[700px] transition-all duration-500 ease-in-out ${state.selectedUserType ? 'lg:w-full' : 'lg:w-1/2'}`}>
        <AuthBackgroundShapes />
        <div className="max-w-xl mx-auto w-full">
          {!state.selectedUserType ? (
            <AuthUserTypeSelector
              title="Crear cuenta"
              subtitle="Selecciona tu tipo de usuario"
              logoSrc={imageLogo}
              logoAlt="Comfaca Logo"
              userTypes={userTypes}
              onSelect={(id) => {
                if (id === 'empresa') {
                  router.visit(route('register.company'))
                  return
                }
                if (id === 'trabajador') {
                  router.visit(route('register.worker'))
                  return
                }
                events.handleUserTypeSelect(id)
              }}
            />
          ) : (
            <PersonRegisterForm
              userTypeLabel={userTypes.find((ut) => ut.id === state.selectedUserType)?.label || ""}
              values={{
                documentType: state.documentType,
                identification: state.identification,
                firstName: state.firstName,
                lastName: state.lastName,
                email: state.email,
                phone: state.phone,
                password: state.password,
                confirmPassword: state.confirmPassword,
                companyName: state.companyName,
                companyNit: state.companyNit,
                address: state.address,
                city: state.city,
                societyType: state.societyType,
                companyCategory: state.companyCategory,
                userRole: state.userRole,
                position: state.position,
                repName: state.repName,
                repIdentification: state.repIdentification,
                repEmail: state.repEmail,
                repPhone: state.repPhone,
                contributionRate: state.contributionRate,
                documentTypeUser: state.documentTypeUser,
                documentTypeRep: state.documentTypeRep
              }}
              errors={state.errors}
              isSubmitting={state.isSubmitting}
              documentTypes={collections.documentTypeOptions}
              cityOptions={collections.cityOptions}
              societyOptions={collections.societyOptions}
              categoryOptions={collections.companyCategoryOptions}
              isWorkerType={state.selectedUserType === 'trabajador'}
              isIndependentType={state.selectedUserType === 'independiente'}
              isPensionerType={state.selectedUserType === 'pensionado'}
              onBack={events.handleBack}
              onChange={(field, value) =>
                dispatch({ type: "SET_FIELD", field: field as keyof FormState, value })
              }
              onSubmit={events.handleRegister}
              step={step}
              onNextStep={events.handleNextStep}
              onPrevStep={events.handlePrevStep}
              firstNameRef={domRef.firstNameRef}
              lastNameRef={domRef.lastNameRef}
              emailRef={domRef.emailRef}
              phoneRef={domRef.phoneRef}
              identificationRef={domRef.identificationRef}
              passwordRef={domRef.passwordRef}
              confirmPasswordRef={domRef.confirmPasswordRef}
              companyNameRef={domRef.companyNameRef}
              companyNitRef={domRef.companyNitRef}
              addressRef={domRef.addressRef}
            />
          )}
        </div>
      </div>
    </AuthLayout>

    {/* Loading animado durante el registro */}
    <LoadingAnimated show={state.isSubmitting} />

    {/* Modal dialog para mensajes */}
    <Dialog open={dialog !== null} onOpenChange={(open) => !open && setDialog(null)}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle className={dialog?.type === 'success' ? 'text-emerald-600' : 'text-red-600'}>
            {dialog?.type === 'success' ? 'Registro Exitoso' : 'Error en el Registro'}
          </DialogTitle>
        </DialogHeader>
        <div className="py-4">
          <p className="text-sm text-gray-700 whitespace-pre-line">{dialog?.message}</p>
        </div>
        <DialogFooter className="gap-2">
          <Button
            variant="outline"
            onClick={() => setDialog(null)}
          >
            Cerrar
          </Button>
          {dialog?.showLoginButton && (
            <Button
              onClick={() => router.visit(route('login'))}
              className="bg-emerald-600 hover:bg-emerald-700"
            >
              Ir al Login
            </Button>
          )}
        </DialogFooter>
      </DialogContent>
    </Dialog>
    </>
  )
}
