import { $App } from '@/App';
import { RouterMoraPresunta } from './RouterMoraPresunta';

// Inicializar la aplicación cuando el DOM esté listo
$(() => {
	$App.startApp(RouterMoraPresunta, 'list', '#boneLayout');
});
