import { $App } from '@/App';
import loading from '@/Componentes/Views/Loading';
import { RouterEmpresas } from './RouterEmpresas';

window.App = $App;

$(() => {
	loading.show(false, { addClass: 'loader-white' });
	window.App.startApp(RouterEmpresas, 'list', '#boneLayout');
});
