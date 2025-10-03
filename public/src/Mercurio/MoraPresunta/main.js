import { $App } from '@/App';
import { RouterMoraPresunta } from './RouterMoraPresunta';
window.App = $App;

$(() => {
	window.App.startApp(RouterMoraPresunta, 'list', '#boneLayout');
});
