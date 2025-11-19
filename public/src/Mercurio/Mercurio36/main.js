import { $App } from '@/App';
import FormClaveFirma from '@/Mercurio/Principal/FormClaveFirma';
import '@/style.scss';
import { RouterFacultativos } from './RouterFacultativos';

window.App = $App;

$(function () {
    window.App.startApp(RouterFacultativos, 'list', '#boneLayout');
    FormClaveFirma();
});
