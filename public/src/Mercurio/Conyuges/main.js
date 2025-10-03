import { $App } from '@/App';
import { RouterConyuges } from './RouterConyuges';
window.App = $App;

$(() => {
	window.App.startApp(RouterConyuges, 'list', '#boneLayout');
});
