import loading from '@/Componentes/Views/Loading';
import { $App } from '@/App.js';
import { RouterLogin } from './RouterLogin';

$(() => {
	loading.show(false, { addClass: 'loader-white' });
	$('body').addClass('bg-gradient-primary');
	$App.startApp(RouterLogin, 'auth', '#boneLayout');
});
