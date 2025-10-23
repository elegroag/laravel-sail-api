import { $App } from '@/App';
import { RouterBeneficiarios } from './RouterBeneficiarios';
import FormClaveFirma from '../Principal/FormClaveFirma';
window.App = $App;

$(() => {
	window.App.startApp(RouterBeneficiarios, 'list', '#boneLayout');
	FormClaveFirma();
});
