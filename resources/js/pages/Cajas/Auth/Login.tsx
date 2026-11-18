import logoComfaca from '@/assets/comfaca-logo.png';
import { cajasFormBtnPrimary, cajasFormInputClass, cajasFormLabelClass } from '@/pages/Cajas/styles/cajas-classes';
import { Head, router, usePage } from '@inertiajs/react';
import { Eye, EyeOff, Loader2, RefreshCw, ShieldCheck } from 'lucide-react';
import { useEffect, useMemo, useState, type FormEvent } from 'react';

type Props = {
    captchaUrl: string;
    authError?: string | null;
    authErrorCode?: number | null;
    old?: { user?: string };
};

function resolveFlashError(value: unknown): string | null {
    if (!value) return null;
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null && 'msj' in value) {
        const msj = (value as { msj?: unknown }).msj;
        return typeof msj === 'string' && msj !== '' ? msj : null;
    }
    return null;
}

function withCacheBust(url: string): string {
    const base = url.split('?')[0];
    return `${base}?v=${Date.now()}`;
}

export default function Login({ captchaUrl, authError = null, old }: Props) {
    const page = usePage<{ flash?: { error?: unknown; success?: unknown } }>();

    const [user, setUser] = useState(old?.user ?? '');
    const [password, setPassword] = useState('');
    const [captcha, setCaptcha] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [captchaSrc, setCaptchaSrc] = useState(() => withCacheBust(captchaUrl));
    const [localError, setLocalError] = useState<string | null>(null);

    const serverError = useMemo(() => {
        return authError || resolveFlashError(page.props.flash?.error) || null;
    }, [authError, page.props.flash?.error]);

    const displayError = localError || serverError;

    useEffect(() => {
        if (serverError) {
            setCaptcha('');
            setCaptchaSrc(withCacheBust(captchaUrl));
        }
    }, [serverError, captchaUrl]);

    const reloadCaptcha = () => {
        setCaptcha('');
        setLocalError(null);
        setCaptchaSrc(withCacheBust(captchaUrl));
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        setLocalError(null);

        const trimmedUser = user.trim();
        const trimmedPassword = password.trim();
        const trimmedCaptcha = captcha.trim();

        if (!trimmedUser) {
            setLocalError('El campo usuario es un valor requerido.');
            return;
        }
        if (!trimmedPassword) {
            setLocalError('El campo clave es un valor requerido.');
            return;
        }
        if (trimmedPassword.length < 3) {
            setLocalError('La clave no puede ser menor a 3 caracteres.');
            return;
        }
        if (!trimmedCaptcha) {
            setLocalError('Por favor ingrese el codigo captcha.');
            return;
        }
        if (trimmedCaptcha.length < 3) {
            setLocalError('El codigo captcha debe tener al menos 3 caracteres.');
            return;
        }

        setProcessing(true);
        router.post(
            '/cajas/autenticar',
            {
                user: trimmedUser,
                password: trimmedPassword,
                captcha: trimmedCaptcha,
            },
            {
                preserveScroll: true,
                onFinish: () => {
                    setProcessing(false);
                    setCaptcha('');
                    setCaptchaSrc(withCacheBust(captchaUrl));
                },
                onError: () => {
                    setCaptcha('');
                    setCaptchaSrc(withCacheBust(captchaUrl));
                },
            },
        );
    };

    return (
        <>
            <Head title="Iniciar sesión — Cajas" />

            <div className="flex min-h-screen flex-col bg-[#2f3438]">
                <div className="flex flex-1 items-center justify-center px-4 py-8">
                    <div className="grid w-full max-w-5xl gap-6 lg:grid-cols-[1.35fr_0.9fr]">
                        <section className="rounded-none bg-[#444] px-7 py-8 text-[#e6f1e7] shadow-lg shadow-black/25">
                            <div className="mb-4 flex justify-center">
                                <img src={logoComfaca} alt="Comfaca" className="h-20 w-auto" />
                            </div>
                            <h1 className="mb-2 text-center text-xl font-bold text-[#78b64b]">
                                COMFACA EN LÍNEA
                                <span className="mt-1 block text-sm font-medium text-[#78b64b]/90">(Administrativo)</span>
                            </h1>
                            <h2 className="mb-3 text-sm font-semibold tracking-wide text-[#78b64b]">
                                POLÍTICA TRATAMIENTO DE DATOS:
                            </h2>
                            <div className="space-y-3 text-justify text-sm leading-relaxed text-[#e6f1e7]/95">
                                <p>
                                    COMFACA identificado con Nit 891.190.047-2 es responsable del tratamiento de datos
                                    personales de su población afiliada incluyendo trabajadores, beneficiarios y
                                    empleadores, y en tal virtud informamos que al ingresar al sistema SISUWEB, usted
                                    como funcionario debe velar por la seguridad y confidencialidad de los datos,
                                    recuerde que la información debe ser protegida de acuerdo con nuestras políticas de
                                    protección de datos personales conforme a la Ley 1581 del 2012 y el decreto 1074 del
                                    2015.
                                </p>
                                <p>
                                    Todos los reportes generados con su usuario quedaran registrados bajo el mismo y
                                    usted asume la responsabilidad de acuerdo con lo declarado anteriormente.
                                </p>
                                <p>
                                    Para más información de la política puede visitar nuestro sitio web{' '}
                                    <a
                                        className="font-medium text-[#00a3df] underline-offset-2 hover:text-[#7ad3f5] hover:underline"
                                        href="https://comfaca.dataprotected.co"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        comfaca.dataprotected.co
                                    </a>
                                </p>
                            </div>
                        </section>

                        <section className="rounded-none bg-[#444] px-7 py-8 text-white shadow-lg shadow-black/25">
                            <div className="mb-5 text-center">
                                <h2 className="text-xl font-bold text-[#78b64b]">Iniciar sesión</h2>
                                <p className="mt-2 text-sm text-white/90">
                                    Los siguientes campos son requeridos para el proceso de autenticación.
                                </p>
                            </div>

                            {displayError ? (
                                <div
                                    role="alert"
                                    className="mb-4 rounded-md border border-cajas-danger/40 bg-cajas-danger/15 px-3 py-2 text-sm text-[#fa9191]"
                                >
                                    {displayError}
                                </div>
                            ) : null}

                            <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off" noValidate>
                                <div>
                                    <label htmlFor="user" className={`${cajasFormLabelClass} text-white/90`}>
                                        Usuario
                                    </label>
                                    <input
                                        id="user"
                                        name="user"
                                        type="text"
                                        className={`${cajasFormInputClass} mt-1`}
                                        value={user}
                                        disabled={processing}
                                        autoFocus
                                        onChange={(e) => setUser(e.target.value)}
                                        placeholder="Usuario"
                                    />
                                </div>

                                <div>
                                    <label htmlFor="password" className={`${cajasFormLabelClass} text-white/90`}>
                                        Clave
                                    </label>
                                    <div className="relative mt-1">
                                        <input
                                            id="password"
                                            name="password"
                                            type={showPassword ? 'text' : 'password'}
                                            className={`${cajasFormInputClass} pr-10`}
                                            value={password}
                                            disabled={processing}
                                            onChange={(e) => setPassword(e.target.value)}
                                            placeholder="Clave"
                                        />
                                        <button
                                            type="button"
                                            className="absolute top-1/2 right-2 -translate-y-1/2 rounded p-1 text-muted-foreground hover:text-foreground"
                                            onClick={() => setShowPassword((v) => !v)}
                                            disabled={processing}
                                            aria-label={showPassword ? 'Ocultar clave' : 'Mostrar clave'}
                                        >
                                            {showPassword ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label htmlFor="captcha" className={`${cajasFormLabelClass} text-white/90`}>
                                        Código de verificación
                                    </label>
                                    <div className="mt-1 flex flex-wrap items-center gap-2">
                                        <img
                                            src={captchaSrc}
                                            alt="Código de verificación"
                                            className="h-10 w-auto min-w-[100px] rounded border border-white/20 bg-white"
                                        />
                                        <button
                                            type="button"
                                            className="inline-flex items-center justify-center rounded-md border border-[#78b64b]/50 p-2 text-[#78b64b] transition-colors hover:bg-[#78b64b]/15"
                                            onClick={reloadCaptcha}
                                            disabled={processing}
                                            title="Generar nuevo código"
                                            aria-label="Recargar código captcha"
                                        >
                                            <RefreshCw className="size-4" />
                                        </button>
                                        <input
                                            id="captcha"
                                            name="captcha"
                                            type="text"
                                            className={`${cajasFormInputClass} min-w-[120px] flex-1`}
                                            value={captcha}
                                            disabled={processing}
                                            maxLength={8}
                                            autoComplete="off"
                                            onChange={(e) => setCaptcha(e.target.value.toUpperCase())}
                                            placeholder="Código"
                                            aria-label="Ingresar código captcha"
                                        />
                                    </div>
                                    <p className="mt-1 flex items-center gap-1 text-xs text-white/60">
                                        <ShieldCheck className="size-3.5" />
                                        Captcha de imagen con vigencia limitada
                                    </p>
                                </div>

                                <button type="submit" className={`${cajasFormBtnPrimary} w-full`} disabled={processing}>
                                    {processing ? <Loader2 className="mr-1.5 size-4 animate-spin" /> : null}
                                    Autenticar
                                </button>
                            </form>
                        </section>
                    </div>
                </div>

                <footer className="pb-5 text-center text-sm text-white/80">
                    &copy; {new Date().getFullYear()}{' '}
                    <a
                        href="http://comfaca.com/master"
                        className="font-semibold text-white hover:underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        COMFACA.COM
                    </a>
                </footer>
            </div>
        </>
    );
}
