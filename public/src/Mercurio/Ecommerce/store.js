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
    /** @type {Array<{codben:string,nombre:string,tipben:string,valser:number,categoria:string,cupos_disponibles:number}>} */
    items: [],
    /** true mientras se revalidan ítems al cambiar de servicio */
    revalidandoServicio: false,
    routes: {},
};

export default store;
