<table class="table table-hover align-items-center table-bordered">
  <thead>
    <tr>
      <th scope="col">Cédula</th>
      <th scope="col">Nombre</th>
      <th scope="col">Estado</th>
      <th scope="col">Fecha Estado</th>
      <th scope="col">Motivo</th>
    </tr>
  </thead>
  <tbody class="list">
    @forelse ($items as $row)
      <tr>
        <td>{{ $row->cedcon }}</td>
        <td>{{ $row->priape }} {{ $row->prinom }}</td>
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