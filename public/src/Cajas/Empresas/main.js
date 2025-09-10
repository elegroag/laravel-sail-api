import { $App } from '@/App';
import { RouterEmpresas } from './RouterEmpresas';

$(() => {
	$App.startApp(RouterEmpresas, 'list', '#boneLayout');
});
