import { $App } from '@/App';
import { RouterBeneficiarios } from './RouterBeneficiarios';

$(() => {
	$App.startApp(RouterBeneficiarios, 'list', '#boneLayout');
});
