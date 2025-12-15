import { $App } from '@/App';

const CertificadoAfiliacion = () => {
    if (!$('#form').valid()) {
        return;
    }
    $('#form').submit();
};

$(() => {
    window.App = $App;
    window.App.initialize();

    $('#form').validate({
        rules: {
            cedtra: { required: true },
            tipo: { required: true },
        },
    });

    $(document).on('click', '#bt_certificado_afiliacion', CertificadoAfiliacion);
});
