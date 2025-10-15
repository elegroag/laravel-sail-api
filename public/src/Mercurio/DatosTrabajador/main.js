import { $App } from '@/App';
import { RouterDatosTrabajador } from './RouterDatosTrabajador';
window.App = $App;

$(() => {
	window.App.startApp(RouterDatosTrabajador, 'list', '#boneLayout');
});
