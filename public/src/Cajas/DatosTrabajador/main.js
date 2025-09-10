import { $App } from '@/App';
import { RouterDatosTrabajadores } from './RouterDatosTrabajadores';

$(function () {
	$App.startApp(RouterDatosTrabajadores, 'list', '#boneLayout');
});
