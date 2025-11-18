import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterActualizadatos } from './RouterActualizadatos';

window.App = $App;
$(() => {
    window.App.startApp(RouterActualizadatos, 'list', '#boneLayout');
    FormClaveFirma();
});
