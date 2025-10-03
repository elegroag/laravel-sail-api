import { $App } from '@/App.js';
import loading from '@/Componentes/Views/Loading';
import { RouterLogin } from './RouterLogin';

window.App = $App;

$(() => {
    loading.show(false, { addClass: 'loader-white' });
    window.App.startApp(RouterLogin, 'auth', '#boneLayout');
});
