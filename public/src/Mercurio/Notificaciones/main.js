import { $App } from '../../App';
import { Region } from '../../Common/Region';
import RegisterNotyView from './RegisterNotyView';

$(() => {
	$App.initialize();
	const view = new RegisterNotyView({ App: $App});
	const region = new Region({ el: '#boneLayout' });
	region.show(view);
});
