import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterBeneficiarios } from './RouterBeneficiarios';
window.App = $App;

$(() => {
    window.App.startApp(RouterBeneficiarios, 'list', '#boneLayout');
    FormClaveFirma();
});
