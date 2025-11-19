import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterIndependientes } from './RouterIndependientes';
window.App = $App;

$(() => {
    window.App.startApp(RouterIndependientes, 'list', '#boneLayout');
    FormClaveFirma();
});
