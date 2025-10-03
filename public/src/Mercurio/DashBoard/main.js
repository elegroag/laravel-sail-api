import { $App } from '@/App';

import {
	TraerAportesEmpresa,
	TraerCategoriasEmpresa,
	TraerGiroEmpresa,
} from './DashBoardServices';

window.App = $App;

$(() => {
	window.App.initialize();
	TraerAportesEmpresa();
	TraerCategoriasEmpresa();
	TraerGiroEmpresa();
});
