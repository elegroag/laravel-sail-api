import { $App } from '@/App';
import { RouterFacultativos } from './RouterFacultativos';

$(function () {
	$App.startApp(RouterFacultativos, 'list', '#boneLayout');
});
