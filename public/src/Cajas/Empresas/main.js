import { $App } from '@/App';
import { RouterEmpresas } from './RouterEmpresas';

window.App = $App;
$(() => {
	window.App.startApp(RouterEmpresas, 'list', '#boneLayout');
});
