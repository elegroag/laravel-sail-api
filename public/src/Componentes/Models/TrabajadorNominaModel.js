'use strict';

import { Testeo } from '../../Core';

class TrabajadorNominaModel extends Backbone.Model {
	constructor(options = {}) {
		super(options);
	}

	get idAttribute() {
		return 'cedtra';
	}

	get defaults() {
		return {
			cedtra: null,
			nomtra: '',
			apetra: '',
			saltra: '',
			fectra: '',
			cartra: '',
			request: '',
		};
	}

	validate(attr = {}) {
		let _err = new Array();
		let erro;
		if (
			(erro = Testeo.vacio({
				attr: attr.cedtra,
				target: 'cedtra',
				label: 'cedula trabajador',
			}))
		) {
			_err.push(erro);
		}
		if (
			(erro = Testeo.vacio({
				attr: attr.nomtra,
				target: 'nomtra',
				label: 'nombres trabajador',
			}))
		) {
			_err.push(erro);
		}
		if (
			(erro = Testeo.vacio({
				attr: attr.apetra,
				target: 'apetra',
				label: 'apellidos trabajador',
			}))
		) {
			_err.push(erro);
		}
		if (
			(erro = Testeo.vacio({
				attr: attr.saltra,
				target: 'saltra',
				label: 'salario trabajador',
			}))
		) {
			_err.push(erro);
		}
		if (
			(erro = Testeo.vacio({
				attr: attr.fectra,
				target: 'fectra',
				label: 'fecha inicio laboral',
			}))
		) {
			_err.push(erro);
		} else {
			if (
				(erro = Testeo.date({
					attr: attr.fectra,
					target: 'fectra',
					label: 'fecha inicio laboral',
				}))
			) {
				_err.push(erro);
			}
		}
		if (
			(erro = Testeo.vacio({
				attr: attr.cartra,
				target: 'cartra',
				label: 'cargo del trabajador',
			}))
		) {
			_err.push(erro);
		}
		return _.isEmpty(_err) === true ? null : _err;
	}
}

export { TrabajadorNominaModel };
