import loading from '@/Componentes/Views/Loading';
import { Utils } from '@/Utils';

const AutenticarCajas = (event) => {
    loading.show();
    var nerr = 0;
    var _password = $('#password').val();
    var _user = $('#user').val();
    var _comfirmar_politica = document.getElementById('comfirmar_politica').checked ? '1' : '0';

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
        let url = Utils.getKumbiaURL('login/autenticar/' + _comfirmar_politica);
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
