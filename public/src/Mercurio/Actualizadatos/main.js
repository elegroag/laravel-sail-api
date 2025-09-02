import { $App } from '@/App';
import { RouterActualizadatos } from './RouterActualizadatos';

$(() => {
	$App.startApp(RouterActualizadatos, 'list', '#boneLayout');
});
