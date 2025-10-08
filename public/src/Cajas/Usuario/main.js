import { $App } from '@/App';
import { RouterUsuario } from './RouterUsuario';

window.App = $App;

$(() => {
	window.App.startApp(RouterUsuario, 'list', '#boneLayout');
});
