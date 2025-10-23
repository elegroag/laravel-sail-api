<table class="table table-hover align-items-center table-bordered">
  <thead>
    <tr>
      <th scope="col">Beneficiario</th>
      <th scope="col">Certificado</th>
      <th scope="col">Estado</th>
      <th scope="col">Fecha Estado</th>
      <th scope="col">Motivo</th>
    </tr>
  </thead>
  <tbody class="list">
    @forelse ($items as $row)
      <tr>
        <td>{{ $row->nombre }}</td>
        <td>{{ $row->nomcer }}</td>
        <td>{{ $row->estadoDetalle }}</td>
        <td>{{ $row->fecest }}</td>
        <td>{{ $row->motivo }}</td>
      </tr>
    @empty
      <tr align="center">
        <td colspan="5"><p>No hay datos para mostrar</p></td>
      </tr>
    @endforelse
  </tbody>
</table>