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
