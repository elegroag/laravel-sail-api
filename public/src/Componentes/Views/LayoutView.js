import { $App } from '@/App';
import { Layout } from '@/Common/Layout';

class LayoutView extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: options.regions || {
				header: '#header_group_button',
				subheader: '#render_subeader',
				body: '#app',
			},
		});
	}

	get events() {
		return {
			"click [data-toggle='linkFilter']": 'linkFilter',
			"click [data-toggle='create']": 'linkCreate',
			"click [data-toggle='masivo']": 'linkMasivo',
		};
	}

	linkFilter(event) {
		event.preventDefault();
		const valor = $(event.currentTarget).attr('data-valor');
		if (_.isUndefined(valor) == true || valor == '') {
			$App.router.navigate('list', { trigger: true });
		} else {
			$App.router.navigate('list/' + valor, { trigger: true });
		}
	}

	linkCreate(e) {
		e.preventDefault();
		const target = $(e.currentTarget);
		if (target.hasClass('disabled') === true) return false;
		target.attr('disabled', 'true');

		$App.trigger('confirma', {
			message: 'Confirma que desea crear una nueva solicitud',
			callback: (status) => {
				if (status) {
					$App.router.navigate('create', { trigger: true });
				} else {
					target.removeAttr('disabled');
				}
			},
		});
	}

	linkMasivo(event) {
		event.preventDefault();
		$App.router.navigate('masivo', { trigger: true });
	}
}

export { LayoutView };
