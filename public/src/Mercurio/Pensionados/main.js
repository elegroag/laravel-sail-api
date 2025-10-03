import { $App } from '@/App';
import { RouterPensionados } from './RouterPensionados';

window.App = $App;

$(() => {
	window.App.startApp(RouterPensionados, 'list', '#boneLayout');
});
