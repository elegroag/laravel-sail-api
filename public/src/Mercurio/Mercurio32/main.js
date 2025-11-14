import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterConyuges } from './RouterConyuges';
window.App = $App;

$(() => {
    window.App.startApp(RouterConyuges, 'list', '#boneLayout');
    FormClaveFirma();
});
