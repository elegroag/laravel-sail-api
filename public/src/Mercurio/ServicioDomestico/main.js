import { $App } from '@/App';
import { RouterServicioDomestico } from './RouterServicioDomestico';

$(function () {
	$App.startApp(RouterServicioDomestico, 'list', '#boneLayout');
});
