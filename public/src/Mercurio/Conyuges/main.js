import { $App } from '@/App';
import { RouterConyuges } from './RouterConyuges';
import FormClaveFirma from '../Principal/FormClaveFirma';
window.App = $App;

$(() => {
	window.App.startApp(RouterConyuges, 'list', '#boneLayout');
	FormClaveFirma();
});
