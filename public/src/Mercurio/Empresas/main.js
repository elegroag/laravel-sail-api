import { $App } from '@/App';
import loading from '@/Componentes/Views/Loading';
import { RouterEmpresas } from './RouterEmpresas';

$(() => {
	loading.show(false, { addClass: 'loader-white' });
	$App.startApp(RouterEmpresas, 'list', '#boneLayout');
});
