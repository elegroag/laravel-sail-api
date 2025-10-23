import { $App } from '@/App';
import { RouterTrabajadores } from './RouterTrabajadores';
import FormClaveFirma from '../Principal/FormClaveFirma';

window.App = $App;

$(() => {
    window.App.startApp(RouterTrabajadores, 'list', '#boneLayout');
    FormClaveFirma();
});
