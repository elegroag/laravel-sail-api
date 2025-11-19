import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterPensionados } from './RouterPensionados';
window.App = $App;

$(() => {
    window.App.startApp(RouterPensionados, 'list', '#boneLayout');
    FormClaveFirma();
});
