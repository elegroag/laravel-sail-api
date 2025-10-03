import { $App } from '@/App';
import { RouterServicioDomestico } from './RouterServicioDomestico';

window.App = $App;

$(function () {
	window.App.startApp(RouterServicioDomestico, 'list', '#boneLayout');
});
