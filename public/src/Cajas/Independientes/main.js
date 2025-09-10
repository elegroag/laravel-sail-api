import { $App } from '@/App';
import { RouterIndependientes } from './RouterIndependientes';

$(() => {
	$App.startApp(RouterIndependientes, 'list', '#boneLayout');
});
