import { $App } from '@/App';
import { RouterIndependientes } from './RouterIndependientes';

window.App = $App;
$(() => {
	window.App.startApp(RouterIndependientes, 'list', '#boneLayout');
});
