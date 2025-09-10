import { $App } from '@/App';
import { RouterMadres } from './RouterMadres';

$(() => {
	$App.startApp(RouterMadres, 'list', '#boneLayout');
});
