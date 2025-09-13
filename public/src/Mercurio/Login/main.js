import { $App } from '@/App.js';
import loading from '@/Componentes/Views/Loading';
import { RouterLogin } from './RouterLogin';

$(() => {
    loading.show(false, { addClass: 'loader-white' });
    $App.startApp(RouterLogin, 'auth', '#boneLayout');
});
