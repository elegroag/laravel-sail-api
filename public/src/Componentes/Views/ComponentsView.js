import Choices from 'choices.js';
import { ModelView } from '@/Common/ModelView';

class SelectComponent extends ModelView {
	constructor(options = {}) {
		super(options);

		this.template = _.template(`
			<select
				type="<%=type%>"
				class="form-control <%=className%>"
				toggle-entity="_entitie_form"
				toggle-event="<%=(event)? event : '' %>"
				id="<%=name%>"
				name="<%=name%>"
				target="<%= target%>"
				placeholder="<%=placeholder%>"
				value="<%= valor%>"
				<%=(readonly)? 'readonly': ''%>
				<%=(disabled)? 'disabled': ''%>>
				<option value="">Pendiente seleccionar...</option>
				<% data = (searchType == 'local')? this.collection[search]: new Array()
				if(_.size(data) > 0){
					_.each(data, function(row, index){ 	%>
					<% selected = (index == valor)? 'selected': '' %>
					<option <%=selected%> value="<%= index%>"><%= row%></option>
				<% })
				} else { %>
					<option value="">...</option>
				<% } %>
			</select>
			<label toggle-error="<%=name%>" id="<%=name%>-error" class="error" for="<%=name%>"></label>
		`);
	}
}

class RadioComponent extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(`
            <div class="form-check form-check-inline mt-2">
                <input
                    id="<%=name%>1"
                    name="<%=name%>"
                    toggle-entity="entitie_form"
                    target="<%= target%>"
                    class="form-check-input mt-0"
                    toggle-event="<%=(event)? event :'' %>"
                    type="radio"
                    value='S'
                    <%=(valor == 'S')? 'checked':''%>
                    <%=(readonly)? 'readonly': ''%>
                    <%=(disabled)? 'disabled': ''%>
                />
                <label class="form-check-label mt-0" for="flexRadioDefault1">
                    SI
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input
                    id="<%=name%>2"
                    name="<%=name%>"
                    toggle-entity="entitie_form"
                    target="<%= target%>"
                    class="form-check-input mt-0"
                    toggle-event="<%=(event)? event.name:'' %>"
                    type="radio"
                    value='N'
                    <%=(valor != 'S')? 'checked':''%>
                    <%=(readonly)? 'readonly': ''%>
                    <%=(disabled)? 'disabled': ''%>
                />
                <label class="form-check-label mt-0" for="flexRadioDefault2">
                    NO
                </label>
            </div>
             <label toggle-error="<%=name%>" id="<%=name%>-error" class="error" for="<%=name%>"></label>
            `);
	}

	get className() {
		return 'form-group';
	}
}

class DateComponent extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = `<input
            type="<%=type%>"
            class="form-control datepicker mt-2 ddate"
            toggle-entity="entitie_form"
            toggle-event="<%= (event)? event : '' %>"
            id="<%=name%>"
            name="<%=name%>"
            target="<%= target%>"
            placeholder="<%=(placeholder)? placeholder : label %>"
            value="<%= valor%>"
            for="<%= name%>"
            title="<%=(placeholder)? placeholder : label %>"
            <%=(disabled)? 'disabled': ''%>
            <%=(readonly)? 'readonly': ''%>
	    />`;
	}

	get className() {
		return 'form-group';
	}
}

class TextComponent extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = `<textarea
		type="<%=type%>"
		toggle-entity="entitie_form"
		class="form-control  mt-2"
		toggle-event="<%=(event)? event : '' %>"
		id="<%=name%>"
		name="<%=name%>"
		target="<%= target%>"
		placeholder="<%=(placeholder)? placeholder : label %>"
		maxlength="<%= longitud %>"
		rows="<%=(rows)? rows : 1 %>"
		<%=(readonly)? 'readonly': ''%>
		<%=(disabled)? 'disabled': ''%>><%= valor%></textarea>
      <label toggle-error="<%=name%>" id="<%=name%>-error" class="error" for="<%=name%>"></label>
      `;
	}

	get className() {
		return 'form-group';
	}
}

class DialogComponent extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(`<div class="input-group  mt-2">
            <input
                type="text"
                toggle-entity="entitie_form"
                class="d-none"
                id="<%= name%>"
                name="<%= name%>"
                value="<%= valor%>"
                target="-1"
            />
            <% if(type === 'text'){ %>
                <input type="<%=type%>"
                placeholder="<%=(placeholder)? placeholder : label %>"
                ondblclick="document.querySelector('.search_<%=name%>').click()"
                id="<%= name%>_detalle"
                name="<%= name%>_detalle"
                class="form-control"
                value="<%= detalle%>"
                target="-1"
                readonly
            />
            <% } %>
            <% if(type === 'textarea'){ %>
                <textarea
                rows='1'
                placeholder="<%=(placeholder)? placeholder : label %>"
                ondblclick="document.querySelector('.search_<%=name%>').click()"
                id="<%= name%>_detalle"
                name="<%= name%>_detalle"
                class="form-control"
                target="-1"
                readonly ><%= detalle%></textarea>
            <% } %>

            <span class="input-group-text">
                <% if(searchType === "ajax"){ %>
                    <button type="button"
                    target= "<%= target%>"
                    data-search="<%=search%>"
                    data-tag="<%=name%>"
                    data-size="<%=(size)? size : 100 %>"
                    data-detalle="<%=name%>_detalle"
                    onclick="buscarDataAjax(this)"
                    class="btn btn-sm bg-gray search_<%=name%>"
                    <%=(disabled)? 'disabled': ''%>
                    >
                        <i class="fa fa-search"></i>
                </button>
                <% } else { %>
                    <button type="button"
                    target= "<%= target%>"
                    data-search="<%=search%>"
                    data-tag="<%=name%>"
                    data-size="<%=(size)? size : 100 %>"
                    data-detalle="<%=name%>_detalle"
                    onclick="buscarData(this, Colletions)"
                    class="btn btn-sm bg-gray search_<%=name%>"
                    <%=(disabled)? 'disabled': ''%>
                    >
                        <i class="fa fa-search"></i>
                </button>
                <% } %>
            </span>
        </div>
        <p id="<%=name%>_hp" class="d-none text-danger"></p>`);
	}

	get className() {
		return 'form-group';
	}
}

class OpenAddress extends Backbone.View {
	constructor(options = {}) {
		super(options);
	}

	initialize() {
		this.Modal = new bootstrap.Modal(document.getElementById('modal_generic'), {
			keyboard: true,
			backdrop: 'static',
		});
	}

	render() {
		const template = _.template(document.getElementById('tmp_direction').innerHTML);
		this.$el.html(
			template({
				adress: this.collection,
			}),
		);
		$('#size_modal_generic').addClass('modal-lg');
		this.Modal.show();
		document.getElementById('modal_generic').addEventListener('hidden.bs.modal', (e) => {
			if ($('.modal:visible').length == 0) this.remove();
		});
		return this;
	}

	get events() {
		return {
			'blur [data-toggle="valida_caracteres"]': 'validaCaracteres',
			'click #button_address_modal': 'addressModal',
			'change #address_zona': 'addressZona',
			'click #address_one': 'addressOne',
			'click [data-dismiss="modal"]': 'closeModal',
		};
	}

	closeModal(e) {
		e.preventDefault();
		this.Modal.hide();
	}

	validaCaracteres(e) {
		let target = $(e.currentTarget);
		if (/[^a-zA-Z\ 0-9]/g.test(target.val())) {
			target.val('');
		}
	}

	addressModal(event) {
		event.preventDefault();
		let barrio = '';
		let address;

		if (this.$el.find('#address_five').val() !== '') {
			barrio = ' BRR ' + $('#address_five').val();
		}
		if (this.$el.find('#address_one').val() == null && $('#address_two').val() == '') {
			address = 'BRR';
		} else {
			address =
				this.$el.find('#address_one').val() +
				' ' +
				this.$el.find('#address_two').val() +
				' ' +
				this.$el.find('#address_four').val() +
				' ' +
				barrio;
		}
		let target = document.getElementById(this.model.name)
		target.value = address;
		this.Modal.hide();
		if(address){
			target.classList.add('is-valid');
			target.classList.remove('is-invalid');
			document.getElementById(this.model.name + '-error').textContent = '';
		}
	}

	addressZona(event) {
		event.preventDefault();
		const valor = $(event.currentTarget).val();
		let lista;
		if (valor === 'R') {
			lista = _.filter(this.collection, (row) => {
				return row.tipo_rural === 'S' || row.tipo_rural === 'V';
			});
			this.$el.find('#address_barrio').fadeOut();
			this.$el.find('#show_address_four').fadeOut();
			this.$el.find('#address_nombre_optional').text('Nombre ubicación');
			this.$el.find('#show_address_two').attr('class', 'col-md-4');
			this.$el.find('#address_one').removeAttr('disabled');

			new Choices(this.$el.find('#address_one')[0]);
		} else if (valor === 'U') {
			lista = _.filter(this.collection, (row) => {
				return row.tipo_rural === 'N';
			});
			this.$el.find('#show_address_four').fadeIn();
			this.$el.find('#address_barrio').fadeIn();
			this.$el.find('#address_nombre_optional').text('Número ');
			this.$el.find('#show_address_two').attr('class', 'col-md-2');
			this.$el.find('#address_one').removeAttr('disabled');

			new Choices(this.$el.find('#address_one')[0]);
		} else {
			lista = [];
			$('#address_one').attr('disabled', 'true');
		}

		let html = '';
		const template = _.template(`<option value="<%=estado%>"><%=detalle%></option>`);
		_.each(lista, (adres) => {
			html += template(adres);
		});
		this.$el.find('#address_one').html(html);
	}

	addressOne(event) {
		event.preventDefault();
		if (this.$el.find('#address_zona').val() == '') {
			this.$el.find('#address_zona').focus();
			this.$el.find('#address_one').val('');
		}
	}
}

class InputComponent extends ModelView {
	constructor(options = {}) {
		super(options);

		this.template = _.template(`
			<input
				type="<%=type%>"
				class="form-control <%=className%>"
				toggle-entity="_entitie_form"
				toggle-event="<%=(event)? event : '' %>"
				id="<%=name%>"
				name="<%=name%>"
				target="<%= target%>"
				placeholder="<%=placeholder%>"
				value="<%= valor%>"
				<%=(readonly)? 'readonly': ''%>
				<%=(disabled)? 'disabled': ''%>
				/>
			<label toggle-error="<%=name%>" id="<%=name%>-error" class="error" for="<%=name%>"></label>
		`);
	}
}

export {
	SelectComponent,
	RadioComponent,
	DateComponent,
	TextComponent,
	DialogComponent,
	OpenAddress,
	InputComponent,
};
