/**
 * Acceso a datos en memoria: busqueda de servicios y precompras cargadas.
 */
import store from './store.js';

export function buscarServicio(codser, numero) {
    for (var i = 0; i < store.serviciosData.length; i++) {
        var srv = store.serviciosData[i];
        if (String(srv.codser) === String(codser) && String(srv.numero) === String(numero)) {
            return srv;
        }
    }
    return null;
}

export function nombreServicio(precompra) {
    var srv = buscarServicio(precompra.codser, precompra.numero);
    if (srv && srv.nombre) {
        return srv.nombre;
    }
    return 'Servicio ' + precompra.codser + ' - ' + precompra.numero;
}

export function buscarPrecompra(id) {
    for (var i = 0; i < store.precomprasData.length; i++) {
        if (String(store.precomprasData[i].id) === String(id)) {
            return store.precomprasData[i];
        }
    }
    return null;
}
