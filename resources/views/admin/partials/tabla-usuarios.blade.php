<table class="table-admin">
    <thead>
        <tr>
            <th>Fantasia</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Edad</th>
            <th>Categoría</th>
            <th>Posición</th>
        </tr>
    </thead>
    <tbody>
        @forelse($usuarios as $usuario)
            <tr>
                <td>
                    <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                        {{ $usuario->fantasia }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                        {{ $usuario->nombre }}
                    </a>
                </td>
                <td>{{ $usuario->ubicacion }}</td>
                <td>{{ $usuario->edad }}</td>
                <td>{{ ucfirst($usuario->categorias) }}</td>
                <td>{{ $usuario->posicion ?? 'Sin posición' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No hay usuarios en esta ciudad</td>
            </tr>
        @endforelse
    </tbody>
</table>