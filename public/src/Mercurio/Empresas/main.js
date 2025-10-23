import { $App } from '@/App';
import loading from '@/Componentes/Views/Loading';
import { RouterEmpresas } from './RouterEmpresas';
import FormClaveFirma from '../Principal/FormClaveFirma';

window.App = $App;

$(() => {
	loading.show(false, { addClass: 'loader-white' });
	window.App.startApp(RouterEmpresas, 'list', '#boneLayout');
	FormClaveFirma();
});
