<table class="table table-hover align-items-center table-bordered">
  <thead>
    <tr>
      <th scope="col">Campo</th>
      <th scope="col">Valor Anterior</th>
      <th scope="col">Valor Nuevo</th>
      <th scope="col">Estado</th>
      <th scope="col">Fecha Estado</th>
      <th scope="col">Motivo</th>
    </tr>
  </thead>
  <tbody class="list">
    @forelse ($items as $row)
      <tr>
        <td>{{ $row->campo_detalle }}</td>
        <td>{{ $row->antval }}</td>
        <td>{{ $row->valor }}</td>
        <td>{{ $row->getEstadoDetalle() }}</td>
        <td>{{ $row->fecest }}</td>
        <td>{{ $row->motivo }}</td>
      </tr>
    @empty
      <tr align="center">
        <td colspan="6"><p>No hay datos para mostrar</p></td>
      </tr>
    @endforelse
  </tbody>
</table>