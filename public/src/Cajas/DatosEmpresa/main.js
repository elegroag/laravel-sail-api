import { $App } from '@/App';
import { RouterDatosEmpresas } from './RouterDatosEmpresas';

$(function () {
	$App.startApp(RouterDatosEmpresas, 'list', '#boneLayout');
});
