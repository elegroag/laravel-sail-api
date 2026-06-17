import loading from '@/Componentes/Views/Loading';

const clearCaptchaError = () => {
    const el = document.querySelector('.error_captcha');
    if (el) el.innerHTML = '';
};

const reloadCaptchaImage = () => {
    const img = document.getElementById('captcha_image');
    if (!img) return;
    const base = (img.getAttribute('data-src') || img.src).split('?')[0];
    img.src = `${base}?v=${Date.now()}`;
    const input = document.getElementById('captcha');
    if (input) input.value = '';
    clearCaptchaError();
};

const AutenticarCajas = (event) => {
    loading.show();
    let nerr = 0;
    const _password = $('#password').val();
    const _user = $('#user').val();
    const _captcha = ($('#captcha').val() || '').trim();

    if (_user == '') {
        document.querySelector('.error_user').innerHTML = '<span>El campo usuario es un valor requerido.</span>';
        nerr++;
    }

    if (_password == '') {
        document.querySelector('.error_clave').innerHTML = '<span>El campo clave es un valor requerido.</span>';
        nerr++;
    } else {
        if (_password.length < 3) {
            nerr++;
            document.querySelector('.error_clave').innerHTML = '<span>La clave no puede ser menor a 3 caracteres.</span>';
        }
    }

    if (_captcha === '') {
        const el = document.querySelector('.error_captcha');
        if (el) el.innerHTML = '<span>Por favor ingrese el codigo captcha.</span>';
        nerr++;
    } else if (_captcha.length < 3) {
        const el = document.querySelector('.error_captcha');
        if (el) el.innerHTML = '<span>El codigo captcha debe tener al menos 3 caracteres.</span>';
        nerr++;
    }

    if (nerr == 0) {
        $('#password').val(_password.trim());
        $('#form_autenticar').submit();
    } else {
        loading.hide();
        if (nerr > 0 && document.getElementById('captcha')) {
            reloadCaptchaImage();
        }
        setTimeout(function () {
            document.querySelector('.error_user').innerHTML = '';
            document.querySelector('.error_clave').innerHTML = '';
            clearCaptchaError();
        }, 6000);
        return false;
    }
};

export { AutenticarCajas, reloadCaptchaImage, clearCaptchaError };
