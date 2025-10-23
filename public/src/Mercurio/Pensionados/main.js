import { $App } from '@/App';
import { RouterPensionados } from './RouterPensionados';
import FormClaveFirma from '../Principal/FormClaveFirma';

window.App = $App;

$(() => {
	window.App.startApp(RouterPensionados, 'list', '#boneLayout');
	FormClaveFirma();
});
