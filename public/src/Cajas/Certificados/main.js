import { $App } from '@/App';
import { RouterCertificados } from './RouterCertificados';

$(() => {
	$App.startApp(RouterCertificados, 'list', '#boneLayout');
});
