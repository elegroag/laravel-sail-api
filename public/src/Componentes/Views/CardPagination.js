import { $App } from '@/App';

class Pagination {
	#urlPagina = '';
	#datos = [];
	#totalPaginas = 0;
	#totalRegistros = 0;
	#endpoint = '';
	#porPagina = 10;
	#targetFilter = '';

	constructor(options) {
		this.maxItems = 3;
		this.porPagina = 10;
		this.paginaActual = 1;
		this.collection = undefined;
		this.endpoint = '';
		this.targetFilter = '#formFilter';
		_.extend(this, options);

		$(document).on('change', '#por_pagina', (eve) => {
			eve.preventDefault();
			this.cargarDatos(1);
		});
	}

	filter() {
		const form = $(this.targetFilter).serializeArray();
		const filter = {};
		let $i = 0;
		while ($i < _.size(form)) {
			filter[form[$i].name] = form[$i].value;
			$i++;
		}
		return filter;
	}

	cargarDatos(transfer) {
		const { pagina = 1, porPagina = 10, filter = void 0 } = transfer;
		this.porPagina = $('#por_pagina').val() || porPagina;
		this.urlPagina = $App.url(this.endpoint);

		if (_.isNull(pagina) == false) {
			this.urlPagina += '/' + this.porPagina + '/' + pagina;
		} else {
			this.urlPagina += '/' + this.porPagina + '/1';
		}

		$App.trigger('syncro', {
			url: this.urlPagina,
			data: filter || this.filter(),
			callback: (response) => {
				if (response.success) {
					this.datos = response.data;
					this.paginaActual = response.page;
					this.totalPaginas = response.total_pages;
					this.totalRegistros = response.total_registros;
					this.collection.reset();
					this.collection.add(response.data, { merge: true });
					this.renderPagina();
					console.log('Página actual:', this.paginaActual);
					console.log('Total de páginas:', this.totalPaginas);
					// Volver a renderizar la tabla con los nuevos datos
				}
			},
		});
	}

	renderPagina() {
		let rango = _.range(this.paginaActual - 1, this.paginaActual + 4);
		const contenedor = document.getElementById('pagination');
		contenedor.innerHTML = '';

		const ul = document.createElement('ul');

		if (this.paginaActual > 1) {
			const liPrimero = document.createElement('li');
			liPrimero.setAttribute('class', 'page-item previous no');
			const aPrimero = document.createElement('a');
			aPrimero.textContent = 'Primero';
			aPrimero.setAttribute('class', 'page-link');
			aPrimero.onclick = () => {
				this.paginaActual = 1;
				this.cargarDatos({ pagina: this.paginaActual });
			};

			liPrimero.appendChild(aPrimero);
			ul.appendChild(liPrimero);

			const liAnterior = document.createElement('li');
			liAnterior.setAttribute('class', 'page-item previous no');
			const aAnterior = document.createElement('a');
			aAnterior.textContent = 'Anterior';
			aAnterior.setAttribute('class', 'page-link');
			aAnterior.onclick = () => {
				this.paginaActual--;
				this.cargarDatos({ pagina: this.paginaActual });
			};
			liAnterior.appendChild(aAnterior);
			ul.appendChild(liAnterior);
		}

		const cPaginas =
			this.totalPaginas >= this.maxItems ? this.maxItems : this.totalPaginas;
		const liItems = [];

		if (this.totalPaginas == this.paginaActual) {
			let active;
			const el = document.createElement('li');
			active = 'page-item active';
			el.setAttribute('class', active);
			const ael = document.createElement('a');
			ael.setAttribute('class', 'page-link');
			ael.textContent = this.paginaActual;
			ael.onclick = (e) => {
				this.paginaActual = e.target.text;
				this.cargarDatos({ pagina: this.paginaActual });
			};

			el.appendChild(ael);
			ul.appendChild(el);
			liItems.push(el);
		} else {
			let p = 0;
			while (p < cPaginas) {
				let valor;
				let active;

				const el = document.createElement('li');
				if (this.paginaActual > this.maxItems) {
					valor = rango[p];
					active = this.paginaActual == valor ? 'page-item active' : 'page-item no';
				} else {
					valor = p + 1;
					active = this.paginaActual == p + 1 ? 'page-item active' : 'page-item no';
				}

				el.setAttribute('class', active);
				let ael = document.createElement('a');
				ael.setAttribute('class', 'page-link');
				ael.textContent = valor;
				ael.onclick = (e) => {
					this.paginaActual = e.target.text;
					this.cargarDatos({ pagina: this.paginaActual });
				};

				el.appendChild(ael);
				ul.appendChild(el);
				liItems.push(el);
				p++;
			}
		}

		if (this.paginaActual < this.totalPaginas) {
			const liSiguiente = document.createElement('li');
			liSiguiente.setAttribute('class', 'page-item next no');
			const aSiguiente = document.createElement('a');
			aSiguiente.setAttribute('class', 'page-link');
			aSiguiente.textContent = 'Siguiente';
			aSiguiente.onclick = () => {
				this.paginaActual++;
				this.cargarDatos({ pagina: this.paginaActual });
			};

			liSiguiente.appendChild(aSiguiente);
			ul.appendChild(liSiguiente);

			const liUltimo = document.createElement('li');
			liUltimo.setAttribute('class', 'page-item next no');
			const aUltimo = document.createElement('a');
			aUltimo.setAttribute('class', 'page-link');
			aUltimo.textContent = 'Ultima';
			aUltimo.onclick = () => {
				this.paginaActual = this.totalPaginas;
				this.cargarDatos({ pagina: this.paginaActual });
			};
			liUltimo.appendChild(aUltimo);
			ul.appendChild(liUltimo);
		}
		contenedor.appendChild(ul);
	}
}
