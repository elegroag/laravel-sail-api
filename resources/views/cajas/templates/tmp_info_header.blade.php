<div class="ml-3">
	<div class='row justify-content-start'>
		<div id="botones" class='d-flex justify-content-end'>
			<% if( estado == 'A' && option.deshacer) {%>
			<button
				type='button'
				info='Deshacer afiliación'
				data-cid="<%=id%>"
				class='btn btn-sm btn-only btn-danger text-white mr-2'
				toggle-event="deshacer">
				<i class="fa fa-undo" aria-hidden="true"></i> DESHACER
			</button>
			<button
				type='button'
				info='Reaprobar afiliación'
				data-cid="<%=id%>"
				class='btn btn-sm btn-only bg-green text-white mr-2'
				toggle-event="reaprobar">
				<i class="fa fa-undo" aria-hidden="true"></i> RE-APROBAR
			</button>
			<%}%>
            <% if(estado == 'A' &&  option.editar) {%>
			<button
				type='button'
				info="Editar empresa"
				class='btn btn-sm bg-purple text-white mr-2'
				toggle-event="editar"
				data-cid="<%=id%>">
				<i class="fa fa-edit" aria-hidden="true"></i> EDITAR
			</button>
			<%}%>
            <% if(option.aportes) {%>
			<button
				type='button'
				info="Ver aportes"
				class='btn btn-sm btn-primary text-white mr-2'
				toggle-event="aportes"
				data-cid="<%=id%>">
				<i class="fa fa-money-bill" aria-hidden="true"></i> APORTES
			</button>
			<%}%>

            <% if(option.notificar) {%>
			<button
				type='button'
				info="Notificar por correspondencia"
				class='btn btn-sm bg-pink text-white mr-2'
				toggle-event="notificar"
				data-cid="<%=id%>">
				<i class="fa fa-envelope" aria-hidden="true"></i> NOTIFICAR
			</button>
			<%}%>

            <% if(option.info) {%>
			<button
				type='button'
				info="Ficha"
				class='btn btn-sm bg-gradient-dark mr-2 text-white'
				toggle-event="info"
				data-cid="<%=id%>">
				<i class="fa fa-envelope" aria-hidden="true"></i> FICHA
			</button>
			<%}%>

			<% if(option.trayectoria) {%>
			<button
				type='button'
				info="Trayectoria"
				class='btn btn-sm bg-gradient-orange text-white mr-2'
				toggle-event="trayectoria"
				data-cid="<%=id%>">
				<i class="fa fa-money-bill" aria-hidden="true"></i> TRAYECTORIA
			</button>
			<%}%>

            <% if( option.volver ) {%>
			<button
				type='button'
				info='Volver a la lista'
				class='btn btn-icon-only rounded-circle btn-info text-white'
				toggle-event="volver">
				<i class="fa fa-arrow-left" aria-hidden="true"></i>
			</button>
			<%}%>
        </div>
    </div>
</div>