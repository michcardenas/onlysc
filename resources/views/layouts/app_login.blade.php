<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

      <!-- Título de la pestaña -->
      <title>Escorts</title>
    
    <!-- Icono de la pestaña (favicon) -->
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Poppins" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

</head>
<body class="login-page">

    <div id="app">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
  document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');

        // Manejar la selección de archivos
        fileInput.addEventListener('change', handleFiles);

        function handleFiles(e) {
            const files = [...e.target.files];
            files.forEach(previewFile);
        }

        function previewFile(file) {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onload = function() {
                const preview = document.createElement('div');
                preview.className = 'publicate-preview-item';
                
                const img = document.createElement('img');
                img.src = reader.result;
                img.className = 'foto-preview';
                
                const removeBtn = document.createElement('button');
                removeBtn.className = 'publicate-remove-button';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = function() {
                    preview.remove();
                };
                
                preview.appendChild(img);
                preview.appendChild(removeBtn);
                previewContainer.appendChild(preview);
            };
        }

        window.removeExistingPhoto = function(foto, button) {
            // Eliminar el elemento del DOM
            button.parentElement.remove();

            // Enviar solicitud para actualizar la base de datos
            fetch('/eliminar-foto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    usuario_id: '{{ $usuario->id }}',
                    foto: foto
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Foto eliminada exitosamente');
                } else {
                    console.error('Error al eliminar la foto');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>
</body>
</html>
