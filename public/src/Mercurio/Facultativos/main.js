import { $App } from '@/App';
import { RouterFacultativos } from './RouterFacultativos';
import FormClaveFirma from '../Principal/FormClaveFirma';

window.App = $App;

$(function () {
	window.App.startApp(RouterFacultativos, 'list', '#boneLayout');
	FormClaveFirma();
});
