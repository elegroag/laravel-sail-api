import { $App } from '@/App';
import { RouterFacultativos } from './RouterFacultativos';

window.App = $App;

$(function () {
	window.App.startApp(RouterFacultativos, 'list', '#boneLayout');
});
