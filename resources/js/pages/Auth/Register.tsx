import AuthLayout from "@/layouts/auth-layout";
import AuthWelcome from "@/pages/Auth/components/auth-welcome";
import AuthUserTypeSelector from "@/pages/Auth/components/auth-user-type-selector";
import CompanyRegisterForm from "@/pages/Auth/components/company-register-form";
import PersonRegisterForm from "@/pages/Auth/components/person-register-form";
import imageLogo from "@/assets/comfaca-logo.png";
import { userTypes } from "@/constants/auth";
import type { FormState, LoginProps} from "@/types/auth";
import AuthBackgroundShapes from "@/components/ui/auth-background-shapes";
import useRegisterController from "@/pages/Auth/controllers/use-register-controller";


export default function Register(props: LoginProps){

  const { 
    dispatch, 
    state,
    collections,
    events,
    domRef,
    toast,
    setToast,
    step
  } = useRegisterController(props);

  return (
    <>
    <AuthLayout title="REGISTRO COMFACA EN LÍNEA" description="Crea tu cuenta para acceder a todos los servicios y beneficios que Comfaca tiene para ofrecerte. Un proceso simple y seguro para comenzar tu experiencia.">
      <div id="welcome" className="lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 flex flex-col justify-center relative overflow-hidden">
        <AuthWelcome
          title="REGISTRO"
          tagline="Únete a Comfaca En Línea"
          description="Crea tu cuenta para acceder a todos los servicios y beneficios que Comfaca tiene para ofrecerte. Un proceso simple y seguro para comenzar tu experiencia."
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
              onSelect={(id) => events.handleUserTypeSelect(id)}
            />
          ) : (
            state.selectedUserType === 'empresa' ? (
              <CompanyRegisterForm
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
                  contributionRate: state.contributionRate,
                  repName: state.repName,
                  repIdentification: state.repIdentification,
                  repEmail: state.repEmail,
                  repPhone: state.repPhone,
                  documentTypeUser: state.documentTypeUser,
                  documentTypeRep: state.documentTypeRep
                }}
                errors={state.errors}
                isSubmitting={state.isSubmitting}
                documentTypes={collections.documentTypeOptions}
                cityOptions={collections.cityOptions}
                societyOptions={collections.societyOptions}
                categoryOptions={collections.companyCategoryOptions}
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
            )
          )}
        </div>
      </div>
    </AuthLayout>

    {/* Toast simple */}
    {toast && (
      <div
        className={`fixed bottom-4 right-4 z-50 min-w-[260px] max-w-[360px] px-4 py-3 rounded shadow-lg text-sm transition-all ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`}
      >
        {toast.message}
        <button
          type="button"
          className="ml-3 underline text-white/90 hover:text-white"
          onClick={() => setToast(null)}
        >
          Cerrar
        </button>
      </div>
    )}
    </>
  )
}
