/**
 * Estado compartido del modulo ComprasPendientes.
 *
 * Objeto mutable unico (singleton del bundle) que reemplaza al estado privado
 * del closure original, preservando exactamente el comportamiento.
 */
const store = {
    trabajadorData: null,
    precomprasData: [],
    serviciosData: [],
    precompraSeleccionada: null,
    modalDesestimar: null,
    modalDetalleCompra: null,
    routes: {},
};

export default store;
