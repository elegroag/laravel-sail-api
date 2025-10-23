import { $App } from '@/App';
import { RouterPrincipal } from './RouterPrincipal';
import FormClaveFirma from './FormClaveFirma';

window.App = $App;

$(() => {
	window.App.startApp(RouterPrincipal, 'list', '#boneLayout');
	FormClaveFirma();
});
