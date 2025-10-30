
const ValidaTipoPago = ({tippag, el}) => {

    if (tippag == '' || tippag == undefined) {
        el.find('#numcue').val('');
        el.find('#tipcue').val('');
        el.find('#codban').val('');
        return;
    }

    el.find('#numcue').prop('disabled', false);
    el.find('#tipcue').prop('disabled', false);
    el.find('#numcue').attr('placeholder', '');

    switch (tippag) {
        case 'B':
            el.find('#numcue').prop('disabled', true);
            el.find('#tipcue').prop('disabled', true);

            el.find('#numcue').val('');
            el.find('#tipcue').val('');

            el.find('#codban').rules('add', { required: false });
            el.find('#codban').prop('disabled', true);
            break;
        case 'E':
            el.find('#numcue').prop('disabled', true);
            el.find('#tipcue').prop('disabled', true);

            el.find('#numcue').val('');
            el.find('#tipcue').val('');

            el.find('#codban').rules('add', { required: false });
            el.find('#codban').prop('disabled', true);
            break;
        case 'T':
            el.find('#numcue').prop('disabled', true);
            el.find('#tipcue').prop('disabled', true);

            el.find('#numcue').val('');
            el.find('#tipcue').val('');

            el.find('#codban').rules('add', { required: false });
            el.find('#codban').prop('disabled', true);

            break;
        case 'A':
            el.find('#codban').removeAttr('disabled');
            el.find('#codban').rules('add', { required: true });
            break;
        case 'D':
            el.find('#codban').removeAttr('disabled');
            el.find('#numcue').removeAttr('disabled');
            el.find('#codban').val('51');
            el.find('#tipcue').val('A');
            el.find('#numcue').attr('placeholder', 'Número teléfono certificado');
            el.find('#numcue').rules('add', { required: true });
            el.find('#codban').rules('add', { required: true });
            break;
    }
}

export { ValidaTipoPago };
