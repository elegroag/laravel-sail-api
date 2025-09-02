import { $App } from '@/App';
import { RouterConyuges } from './RouterConyuges';

$(() => {
	$App.startApp(RouterConyuges, 'list', '#boneLayout');
});
