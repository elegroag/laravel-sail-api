import { $App } from '@/App';
import { RouterTrabajadores } from './RouterTrabajadores';

window.App = $App;

$(() => {
    window.App.startApp(RouterTrabajadores, 'list', '#boneLayout');
});
