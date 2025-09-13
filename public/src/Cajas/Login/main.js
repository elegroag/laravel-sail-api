import { AutenticarCajas } from './login';

$(() => {
    const toggle = document.getElementById('btnToggle');

    toggle.addEventListener(
        'click',
        (event) => {
            {
                event.preventDefault();
                var passwordInput = $('#password');
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                } else {
                    passwordInput.attr('type', 'password');
                }
            }
        },
        false,
    );
    $('#user').focus();

    document.getElementById('bt_autenticar').addEventListener('click', AutenticarCajas);

    document.getElementById('password').addEventListener('keydown', function (evt) {
        evt = evt || window.event;
        var charCode = evt.keyCode || evt.which;
        if (parseInt(charCode) == 13) {
            document.getElementById('bt_autenticar').click();
        }
    });
});
