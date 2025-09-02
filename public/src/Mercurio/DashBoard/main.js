import { $App } from '@/App';

import {
	TraerAportesEmpresa,
	TraerCategoriasEmpresa,
	TraerGiroEmpresa,
} from './DashBoardServices';

$(() => {
	$App.initialize();
	TraerAportesEmpresa();
	TraerCategoriasEmpresa();
	TraerGiroEmpresa();
});
