import { $App } from '@/App';
import { RouterIndependientes } from './RouterIndependientes';
import FormClaveFirma from '../Principal/FormClaveFirma';

window.App = $App;

$(() => {
	window.App.startApp(RouterIndependientes, 'list', '#boneLayout');
	FormClaveFirma();
});
