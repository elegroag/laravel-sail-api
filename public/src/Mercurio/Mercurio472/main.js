import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterDatosTrabajador } from './RouterDatosTrabajador';
window.App = $App;

$(() => {
    window.App.startApp(RouterDatosTrabajador, 'list', '#boneLayout');
    FormClaveFirma();
});
