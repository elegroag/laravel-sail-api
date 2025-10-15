const CSRF = $("[name='csrf-token']");
const $Kumbia = {
    path: CSRF.attr('path'),
    app: CSRF.attr('app'),
};

class Utils {
    static getKumbiaURL(url = '') {
        if ($Kumbia.app != '') {
            return $Kumbia.path + '/' + $Kumbia.app + '/' + url;
        } else {
            return $Kumbia.path + '/' + url;
        }
    }

    static getAppURL(url) {
        if (typeof url == 'undefined') url = '';
        return $Kumbia.path + url;
    }

    static getURL(url) {
        if (typeof url == 'undefined') {
            return $Kumbia.path;
        } else {
            return $Kumbia.path + url;
        }
    }

    static redirectParentToAction(url) {
        new Utils.redirectToAction(url, window.parent);
    }

    static redirectOpenerToAction(url) {
        new Utils.redirectToAction(url, window.opener);
    }

    static redirectToAction(url, win) {
        win = win ? win : window;
        win.location.href = Utils.getKumbiaURL() + url;
    }

    static upperCaseFirst(str) {
        let first = str.substring(0, 1).toUpperCase();
        return first + str.substr(1, str.length - 1);
    }

    static numberFormat(number = 0, decimals = 0, dec_point = undefined, thousands_sep = undefined) {
        let n = !isFinite(+number) ? 0 : +number;
        let prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        let sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep;
        let dec = typeof dec_point === 'undefined' ? '.' : dec_point;
        let s = undefined;

        const toFixedFix = (n, prec) => {
            let k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    static validOnlyLetter(evt) {
        evt = evt ? evt : window.event ? window.event : null;
        let kc = evt.keyCode;
        let ev = (kc >= 65 && kc <= 90) || kc == 8 || kc == 9 || kc == 32;
        if (ev == false) {
            evt.preventDefault();
            evt.stopPropagation();
            evt.stopped = true;
        }
    }

    static openFile({nomarc, data, cb}) {
        const ext = (nomarc && nomarc.includes('.')) ? nomarc.split('.').pop().toLowerCase() : '';
        const mimeMap = {
            jpg: 'image/jpeg',
            jpeg: 'image/jpeg',
            png: 'image/png',
            gif: 'image/gif',
            webp: 'image/webp',
            bmp: 'image/bmp',
            svg: 'image/svg+xml',
            pdf: 'application/pdf'
        };
        const guessedType = mimeMap[ext] || data.type || 'application/octet-stream';
        const blob = data.type ? data : new Blob([data], { type: guessedType });
        const url = URL.createObjectURL(blob);

        if (guessedType.startsWith('image/')) {
            const win = window.open('', nomarc, 'width=900,height=750,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes');
            if (win) {
                win.document.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><title>${nomarc}</title><style>html,body{margin:0;height:100%;}img{max-width:100%;max-height:100%;display:block;margin:auto;}#wrap{height:100%;display:flex;align-items:center;justify-content:center;background:#111}</style></head><body><div id="wrap"><img src="${url}" alt="${nomarc}"></div></body></html>`);
                win.document.close();
                win.addEventListener('beforeunload', () => URL.revokeObjectURL(url));
            } else {
                URL.revokeObjectURL(url);
                return cb('El navegador bloqueó la ventana emergente');
            }
        } else {
            window.open(url, nomarc, 'width=900,height=750,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes');
        }
        return cb(null);
    }
}

class Messages {
    static display(message, type, timeout = 8000) {
        new Noty({
            text: message,
            layout: 'topRight',
            theme: 'relax',
            type: type,
            timeout: timeout,
        }).show();
    }
}

export { $Kumbia, Messages, Utils };
