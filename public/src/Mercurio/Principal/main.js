import { $App } from '@/App';
import { RouterPrincipal } from './RouterPrincipal';

window.App = $App;

$(() => {
    window.App.startApp(RouterPrincipal, 'list', '#boneLayout');
});
