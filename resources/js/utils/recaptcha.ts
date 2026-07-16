// Helper para cargar y usar Google reCAPTCHA v2 (checkbox) sin dependencias npm.
// El script oficial se inyecta una sola vez y se reutiliza entre montajes del componente.

interface Grecaptcha {
    ready: (callback: () => void) => void;
    render: (
        container: HTMLElement,
        params: {
            sitekey: string;
            callback?: (token: string) => void;
            'expired-callback'?: () => void;
            'error-callback'?: () => void;
        },
    ) => number;
    reset: (widgetId?: number) => void;
}

declare global {
    interface Window {
        grecaptcha?: Grecaptcha;
        __onRecaptchaApiLoad?: () => void;
    }
}

const SCRIPT_ID = 'google-recaptcha-v2';

let loadPromise: Promise<Grecaptcha> | null = null;

export function loadRecaptcha(): Promise<Grecaptcha> {
    if (loadPromise) return loadPromise;

    loadPromise = new Promise<Grecaptcha>((resolve, reject) => {
        const resolveWhenReady = () => {
            const grecaptcha = window.grecaptcha;
            if (!grecaptcha) {
                reject(new Error('grecaptcha no disponible tras cargar el script'));
                return;
            }
            grecaptcha.ready(() => resolve(grecaptcha));
        };

        if (window.grecaptcha) {
            resolveWhenReady();
            return;
        }

        window.__onRecaptchaApiLoad = resolveWhenReady;

        if (!document.getElementById(SCRIPT_ID)) {
            const script = document.createElement('script');
            script.id = SCRIPT_ID;
            script.src = 'https://www.google.com/recaptcha/api.js?onload=__onRecaptchaApiLoad&render=explicit';
            script.async = true;
            script.defer = true;
            script.onerror = () => {
                loadPromise = null;
                reject(new Error('No fue posible cargar el script de reCAPTCHA'));
            };
            document.head.appendChild(script);
        }
    });

    return loadPromise;
}

export function resetRecaptcha(widgetId?: number | null) {
    if (!window.grecaptcha) return;
    if (widgetId === null || widgetId === undefined) {
        // Sin id resetea el primer widget creado (solo hay uno en el login)
        window.grecaptcha.reset();
    } else {
        window.grecaptcha.reset(widgetId);
    }
}
