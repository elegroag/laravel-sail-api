import loading from '@/Componentes/Views/Loading';
import { Utils } from '@/Utils';

const AutenticarCajas = (event) => {
    loading.show();
    const nerr = 0;
    const _password = $('#password').val();
    const _user = $('#user').val();

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
    if (nerr == 0) {
        $('#password').val(_password.trim());
        const url = Utils.getKumbiaURL('autenticar');
        document.getElementById('form_autenticar').setAttribute('action', url);
        $('#form_autenticar').submit();
    } else {
        loading.hide();
        setTimeout(function () {
            document.querySelector('.error_user').innerHTML = '';
            document.querySelector('.error_clave').innerHTML = '';
        }, 6000);
        return false;
    }
};

export { AutenticarCajas };
