import { $App } from '@/App';
import { RouterDatosTrabajador } from './RouterDatosTrabajador';

$(function () {
	$App.startApp(RouterDatosTrabajador, 'list', '#boneLayout');
});
