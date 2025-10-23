import { $App } from '@/App';
import { RouterActualizadatos } from './RouterActualizadatos';
import FormClaveFirma from '../Principal/FormClaveFirma';

$(() => {
	$App.startApp(RouterActualizadatos, 'list', '#boneLayout');
	FormClaveFirma();
});
