import { $App } from '@/App';
import { RouterPensionados } from './RouterPensionados';

$(() => {
	$App.startApp(RouterPensionados, 'list', '#boneLayout');
});
