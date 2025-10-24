import TextLink from '@/components/text-link';
import AuthBackgroundShapes from '@/components/ui/auth-background-shapes';
import { Button } from '@/components/ui/button';
import { userTypes } from '@/constants/auth';
import AuthLayout from '@/layouts/auth-layout';
import { CheckCircle } from 'lucide-react';
import imageLogo from '../../assets/comfaca-logo.png';
import AuthUserTypeSelector from './components/auth-user-type-selector';
import AuthWelcome from './components/auth-welcome';
import ResetPasswordForm from './components/reset-password-form';
import useRecoveryController from './controllers/use-recovery-controller';

interface ResetPasswordProps {
    Coddoc: Record<string, string>;
}

export default function ResetPassword({Coddoc}: ResetPasswordProps) {
    const { 
        events, 
        selectedUserType, 
        formState, 
        domRef, 
        toast, 
        setToast, 
        documentTypeOptions 
    } = useRecoveryController({ Coddoc});

    if (formState.isSuccess) {
        return (
            <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
                <div className="max-w-md bg-white rounded-3xl shadow-2xl p-8 w-full text-center">
                    <div className="mb-6">
                        <CheckCircle className="w-16 h-16 text-emerald-600 mb-4 mx-auto" />
                        <h2 className="text-2xl font-semibold text-gray-800 mb-2">¡Solicitud enviada!</h2>
                        <p className="text-gray-600">Hemos enviado las instrucciones para restablecer tu clave a tu correo electrónico.</p>
                    </div>
                    <div className="space-y-4">
                        <TextLink href={route('login')}>
                            <Button className="from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white w-full bg-gradient-to-r">
                                Volver al inicio de sesión
                            </Button>
                        </TextLink>
                    </div>
                </div>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
            <div
                id="welcome"
                className="lg:w-1/2 from-emerald-600 via-emerald-700 to-teal-700 text-white p-12 relative flex flex-col justify-center overflow-hidden bg-gradient-to-br"
            >
                {/* Left Panel - Welcome Section */}
                <AuthWelcome
                    title="RECUPERAR"
                    tagline="Comfaca En Línea"
                    description="Ingresa tu información para recibir las instrucciones de recuperación de clave en tu correo electrónico."
                    backHref={route('login')}
                    backText="¿Ya tienes cuenta? Inicia sesión"
                />
            </div>
            {/* Right Panel - Forgot Password Form */}
            <div className="lg:w-1/2 p-12 relative flex flex-col justify-center">
                <AuthBackgroundShapes />
                <div className="max-w-md mx-auto w-full">
                    {!selectedUserType ? (
                        <AuthUserTypeSelector
                            title="Recuperar clave"
                            subtitle="Selecciona tu tipo de usuario"
                            logoSrc={imageLogo}
                            logoAlt="Comfaca Logo"
                            userTypes={userTypes}
                            onSelect={(id) => events.handleUserTypeSelect(id)}
                        />
                    ) : (
                        <ResetPasswordForm
                            selectedUserType={selectedUserType}
                            formState={{
                                documentType: formState.documentType,
                                identification: formState.identification,
                                email: formState.email ?? '',
                                whatsapp: formState.whatsapp ?? '',
                                delivery_method: formState.delivery_method,
                                errors: formState.errors as Record<string, string>,
                                isSubmitting: formState.isSubmitting,
                            }}
                            onBack={events.handleBack}
                            onFieldChange={(field, value) => events.handleFieldChange(field, value)}
                            onSubmit={events.handleSubmit}
                            documentTypeRef={domRef.documentTypeRef}
                            identificationRef={domRef.identificationRef}
                            emailRef={domRef.emailRef}
                            whatsappRef={domRef.whatsappRef}
                            loginHref={route('login')}
                            documentTypeOptions={documentTypeOptions}
                        />
                    )}
                </div>
            </div>
            {/* Toast simple */}
            {toast && (
                <div
                    className={`bottom-4 right-4 px-4 py-3 rounded shadow-lg text-sm fixed z-50 max-w-[360px] min-w-[260px] transition-all ${toast.type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`}
                >
                    {toast.message}
                    <button type="button" className="ml-3 text-white/90 hover:text-white underline" onClick={() => setToast(null)}>
                        Cerrar
                    </button>
                </div>
            )}
        </AuthLayout>
    );
}
