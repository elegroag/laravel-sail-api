import { $App } from '@/App';
import { RouterTrabajadores } from './RouterTrabajadores';

$(() => {
    $App.startApp(RouterTrabajadores, 'list', '#boneLayout');
});
