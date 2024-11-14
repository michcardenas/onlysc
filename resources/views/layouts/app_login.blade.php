<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            const maxFileSize = 2 * 1024 * 1024; // 2MB en bytes
            const userId = previewContainer.dataset.userId;

            // Validar tipo y tamaño de archivo
            function validateFile(file) {
                if (!file.type.startsWith('image/')) {
                    alert('Por favor, seleccione solo archivos de imagen');
                    return false;
                }

                if (file.size > maxFileSize) {
                    alert(`El archivo ${file.name} excede el tamaño máximo permitido de 2MB`);
                    return false;
                }

                return true;
            }

            // Manejar la selección de nuevos archivos
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    if (validateFile(file)) {
                        previewFile(file);
                    }
                });
            });

            function previewFile(file) {
                const reader = new FileReader();

                reader.onload = function() {
                    const preview = document.createElement('div');
                    preview.className = 'publicate-preview-item';
                    preview.dataset.userId = userId;

                    const img = document.createElement('img');
                    img.src = reader.result;
                    img.className = 'foto-preview';
                    img.alt = 'Vista previa';

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'publicate-remove-button';
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = function() {
                        preview.remove();
                    };

                    preview.appendChild(img);
                    preview.appendChild(removeBtn);
                    previewContainer.appendChild(preview);
                };

                reader.onerror = function() {
                    console.error('Error al leer el archivo');
                    alert('Error al procesar la imagen');
                };

                reader.readAsDataURL(file);
            }

            // Función global para eliminar fotos existentes
            window.removeExistingPhoto = function(foto, button) {
                if (!confirm('¿Estás seguro de que deseas eliminar esta foto?')) {
                    return;
                }

                const item = button.closest('.publicate-preview-item');
                const userId = item.dataset.userId;

                fetch('/usuarios-publicate/eliminar-foto', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            usuario_id: userId,
                            foto: foto
                        })

                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recargar la página con un mensaje de éxito
                            window.location.reload();
                        } else {
                            alert('Error al eliminar la foto: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la foto. Por favor, intente nuevamente.');
                    });
            };

            // Prevenir envío del formulario si hay errores
            document.querySelector('form').addEventListener('submit', function(e) {
                const files = fileInput.files;
                for (let file of files) {
                    if (!validateFile(file)) {
                        e.preventDefault();
                        return;
                    }
                }
            });
        });

        const precioInput = document.querySelector('.precio-input');
        const form = precioInput.closest('form');

        form.addEventListener('submit', function(e) {
            let value = precioInput.value.replace(/[^\d]/g, '');
            precioInput.value = value;
        });

        precioInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                value = 'CLP $' + parseInt(value).toLocaleString('es-CL');
                this.value = value;
            }
        });
    </script>
</body>

</html>