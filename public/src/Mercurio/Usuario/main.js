import { $App } from '@/App';
import { RouterUsuario } from './RouterUsuario';

$(() => {
	$App.startApp(RouterUsuario, 'datos', '#boneLayout');
});
