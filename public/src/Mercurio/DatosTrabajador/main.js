import { $App } from '@/App';
import { RouterDatosTrabajador } from './RouterDatosTrabajador';
import FormClaveFirma from '../Principal/FormClaveFirma';
window.App = $App;

$(() => {
	window.App.startApp(RouterDatosTrabajador, 'list', '#boneLayout');
	FormClaveFirma();
});
