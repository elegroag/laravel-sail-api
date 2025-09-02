import { $App } from '@/App';
import { RouterPrincipal } from './RouterPrincipal';

$(() => {
	$App.startApp(RouterPrincipal, 'list', '#boneLayout');
});
