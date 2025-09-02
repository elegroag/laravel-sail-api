import { Testeo } from '@/Core';

const RulesValidator = (rules = [], attr = {}) => {
	const error = new Array();
	_.each(rules, (rule, item) => {
		if (rule !== undefined) {
			const {
				required = false,
				number = undefined,
				email = undefined,
				date = undefined,
				maxlength = undefined,
				minlength = undefined,
				rangelength = undefined,
				range = undefined,
			} = rule;

			let err = undefined;
			if (!!required) {
				err = Testeo.vacio({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
				});
			}

			if (!err && !_.isUndefined(email)) {
				err = Testeo.email({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
				});
			}

			if (!err && !_.isUndefined(number)) {
				err = Testeo.numerico({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
				});
			}

			if (!err && !_.isUndefined(date)) {
				err = Testeo.date({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
				});
			}

			if (!err && _.isUndefined(maxlength) && !_.isUndefined(minlength)) {
				err = Testeo.max({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
					min: minlength,
					max: maxlength,
				});
			}

			if (!err && !_.isUndefined(rangelength)) {
				err = Testeo.max({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
					min: rangelength[0],
					max: rangelength[1],
				});
			}

			if (!err && !_.isUndefined(range)) {
				err = Testeo.identi({
					attr: attr[item],
					target: item,
					label: item,
					out: true,
					min: range[0],
					max: range[1],
				});
			}

			if (err && err !== undefined) error.push(err);
		}
	});

	return !!_.isEmpty(error) ? null : error;
};

export { RulesValidator };
