import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterTrabajadores } from './RouterTrabajadores';

window.App = $App;

$(() => {
    window.App.startApp(RouterTrabajadores, 'list', '#boneLayout');
    FormClaveFirma();
});
