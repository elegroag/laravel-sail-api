/**
 * Estado compartido del modulo Ecommerce.
 *
 * Objeto mutable unico (singleton del bundle) que reemplaza al estado privado
 * que antes vivia en el closure del IIFE. Los modulos leen y escriben aqui para
 * preservar exactamente el comportamiento original.
 */
const store = {
    trabajadorData: null,
    nucleoFamiliar: [],
    beneficiarioSeleccionado: null,
    serviciosData: [],
    servicioSeleccionado: null,
    busquedaServicio: '',
    filtroCodserServicio: '',
    modalResumenCompra: null,
    routes: {},
};

export default store;
