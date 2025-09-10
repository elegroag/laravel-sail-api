import { $App } from '@/App';
import { RouterDomestico } from './RouterDomestico';

$(() => {
	$App.startApp(RouterDomestico, 'list', '#boneLayout');
});
