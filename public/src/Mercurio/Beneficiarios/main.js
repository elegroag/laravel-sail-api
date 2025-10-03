import { $App } from '@/App';
import { RouterBeneficiarios } from './RouterBeneficiarios';
window.App = $App;

$(() => {
	window.App.startApp(RouterBeneficiarios, 'list', '#boneLayout');
});
