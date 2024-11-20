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
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- En el <head> de tu HTML -->
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">




</head>

<body class="login-page">
    <div id="app">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const fotoDestacada = document.getElementById('fotoDestacada');
    const fotosAdicionales = document.getElementById('fotosAdicionales');
    const previewDestacadaContainer = document.getElementById('previewDestacada');
    const previewContainer = document.getElementById('previewContainer');
    const maxFileSize = 2 * 1024 * 1024; // 2MB en bytes

    let fotosExistentes = []; // Array para mantener las fotos existentes
    let nuevaFotoDestacada = null; // Para almacenar la nueva foto destacada
    let nuevasFotos = []; // Para almacenar las nuevas fotos adicionales

    // Inicializar fotos existentes
    const initializeFotos = () => {
        fotosExistentes = [];
        document.querySelectorAll('.publicate-preview-item[data-foto]').forEach(item => {
            fotosExistentes.push(item.dataset.foto);
        });
    };

    initializeFotos();

    // Validar tipo y tamaño de archivo
    function validateFile(file) {
        if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
            alert('Por favor, seleccione solo archivos de imagen o video');
            return false;
        }

        if (file.size > maxFileSize) {
            alert(`El archivo ${file.name} excede el tamaño máximo permitido de 2MB`);
            return false;
        }

        return true;
    }

    // Manejar la selección de foto destacada
    fotoDestacada?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && validateFile(file)) {
            nuevaFotoDestacada = file;
            previewDestacadaFile(file);
        }
    });

    // Manejar la selección de fotos adicionales
    fotosAdicionales?.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (validateFile(file)) {
                nuevasFotos.push(file);
                previewAdditionalFile(file);
            }
        });
    });

    function previewDestacadaFile(file) {
        const reader = new FileReader();

        reader.onload = function() {
            previewDestacadaContainer.innerHTML = ''; // Limpiar preview anterior
            
            const preview = document.createElement('div');
            preview.className = 'publicate-preview-item destacada';

            const mediaElement = file.type.startsWith('video/') ? 
                document.createElement('video') : 
                document.createElement('img');

            mediaElement.src = reader.result;
            mediaElement.className = 'foto-preview-destacada';
            
            if (file.type.startsWith('video/')) {
                mediaElement.controls = true;
            }

            const removeBtn = document.createElement('button');
            removeBtn.className = 'publicate-remove-button';
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = function() {
                preview.remove();
                fotoDestacada.value = '';
                nuevaFotoDestacada = null;
            };

            preview.appendChild(mediaElement);
            preview.appendChild(removeBtn);
            previewDestacadaContainer.appendChild(preview);
        };

        reader.onerror = function() {
            console.error('Error al leer el archivo');
            alert('Error al procesar el archivo');
        };

        reader.readAsDataURL(file);
    }

    function previewAdditionalFile(file) {
        const reader = new FileReader();

        reader.onload = function() {
            const preview = document.createElement('div');
            preview.className = 'publicate-preview-item';

            const mediaElement = file.type.startsWith('video/') ? 
                document.createElement('video') : 
                document.createElement('img');

            mediaElement.src = reader.result;
            mediaElement.className = 'foto-preview';
            
            if (file.type.startsWith('video/')) {
                mediaElement.controls = true;
            }

            const removeBtn = document.createElement('button');
            removeBtn.className = 'publicate-remove-button';
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = function() {
                preview.remove();
                nuevasFotos = nuevasFotos.filter(f => f !== file);
            };

            preview.appendChild(mediaElement);
            preview.appendChild(removeBtn);
            previewContainer.appendChild(preview);
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
                item.remove();
                fotosExistentes = fotosExistentes.filter(f => f !== foto);
            } else {
                alert('Error al eliminar la foto: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la foto. Por favor, intente nuevamente.');
        });
    };

    // Función para eliminar foto destacada
    window.removeDestacada = function(button) {
        if (!confirm('¿Estás seguro de que deseas eliminar la foto destacada?')) {
            return;
        }

        const item = button.closest('.publicate-preview-item');
        const userId = item.dataset.userId;
        const foto = item.dataset.foto;

        fetch('/usuarios-publicate/eliminar-foto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                usuario_id: userId,
                foto: foto,
                es_destacada: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.remove();
                fotosExistentes = fotosExistentes.filter(f => f !== foto);
            } else {
                alert('Error al eliminar la foto destacada: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la foto destacada. Por favor, intente nuevamente.');
        });
    };

    // Función para guardar todos los cambios
    window.guardarCambiosFotos = function() {
        const formData = new FormData();
        const userId = document.querySelector('.publicate-preview-item').dataset.userId;

        // Si hay una nueva foto destacada, agregarla primero
        if (nuevaFotoDestacada) {
            formData.append('foto_destacada', nuevaFotoDestacada);
            formData.append('es_destacada', 'true');
        }

        // Agregar nuevas fotos adicionales
        nuevasFotos.forEach((foto, index) => {
            formData.append(`fotos[]`, foto);
        });

        // Agregar array de fotos existentes
        formData.append('fotos_actuales', JSON.stringify(fotosExistentes));
        formData.append('usuario_id', userId);
        
        // Agregar el token CSRF
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(`/usuarios-publicate/update/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error al guardar los cambios: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar los cambios. Por favor, intente nuevamente.');
        });
    };
});
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


        document.addEventListener('DOMContentLoaded', function() {
            function toggleFullTime(checkbox, dia) {
    const desdeInput = document.getElementById(`desde-${dia}`);
    const hastaInput = document.getElementById(`hasta-${dia}`);
    const diaCheckbox = document.getElementById(`admin-dia-${dia}`);

    if (checkbox.checked && diaCheckbox.checked) {
        desdeInput.value = '00:00';
        hastaInput.value = '23:59';
        desdeInput.disabled = true;
        hastaInput.disabled = true;
    } else {
        desdeInput.disabled = false;
        hastaInput.disabled = false;
        
        if (!checkbox.checked) {
            desdeInput.value = '10:00'; // Hora de inicio predeterminada en formato 24h
            hastaInput.value = '22:00'; // Hora de fin predeterminada en formato 24h
        }
    }


        // Importante: Agregar inputs ocultos para asegurar que los valores se envíen
        // incluso cuando los inputs estén deshabilitados
        updateHiddenInputs(dia);
    }

    function updateHiddenInputs(dia) {
        // Eliminar inputs ocultos anteriores si existen
        removeExistingHiddenInputs(dia);

        const desdeInput = document.getElementById(`desde-${dia}`);
        const hastaInput = document.getElementById(`hasta-${dia}`);
        const form = desdeInput.closest('form');

        if (desdeInput.disabled) {
            const hiddenDesde = document.createElement('input');
            hiddenDesde.type = 'hidden';
            hiddenDesde.name = `horario[${dia}][desde]`;
            hiddenDesde.value = desdeInput.value;
            form.appendChild(hiddenDesde);
        }

        if (hastaInput.disabled) {
            const hiddenHasta = document.createElement('input');
            hiddenHasta.type = 'hidden';
            hiddenHasta.name = `horario[${dia}][hasta]`;
            hiddenHasta.value = hastaInput.value;
            form.appendChild(hiddenHasta);
        }
    }

    function removeExistingHiddenInputs(dia) {
        const form = document.querySelector('form');
        const hiddenInputs = form.querySelectorAll(`input[type="hidden"][name^="horario[${dia}]"]`);
        hiddenInputs.forEach(input => input.remove());
    }

    // Agregar event listeners a los checkboxes de full time
    document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const dia = this.id.replace('fulltime-', '');
            toggleFullTime(this, dia);
        });
    });

    // Agregar event listeners para el envío del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        // Asegurarse de que todos los valores se envíen
        document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
            const dia = checkbox.id.replace('fulltime-', '');
            if (checkbox.checked) {
                updateHiddenInputs(dia);
            }
        });
    });

    // Inicializar estado de los inputs al cargar
    document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
        if (checkbox.checked) {
            const dia = checkbox.id.replace('fulltime-', '');
            toggleFullTime(checkbox, dia);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const fantasiaInput = document.querySelector('input[name="fantasia"]');
    const ubicacionSelect = document.querySelector('select[name="ubicacion"]');
    
    async function validateFantasia() {
        const fantasia = fantasiaInput.value;
        const ubicacion = ubicacionSelect.value;
        
        if (!fantasia || !ubicacion) return;
        
        try {
            const response = await fetch('/api/validate-fantasia', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    fantasia,
                    ubicacion,
                    userId
                })
            });
            
            const data = await response.json();
            
            if (!data.valid) {
                fantasiaInput.setCustomValidity('Ya existe una persona con este nombre de fantasía en esta ciudad.');
                fantasiaInput.reportValidity();
            } else {
                fantasiaInput.setCustomValidity('');
            }
        } catch (error) {
            console.error('Error validando fantasía:', error);
        }
    }
    
    fantasiaInput.addEventListener('blur', validateFantasia);
    ubicacionSelect.addEventListener('change', validateFantasia);
});

// Función para actualizar la posición en la base de datos
function updateImagePosition(position) {
    const destacada = document.querySelector('.destacada');
    if (!destacada) return;

    const userId = destacada.dataset.userId;
    const fotoName = destacada.dataset.foto;
    
    fetch('/actualizar-posicion-foto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_id: userId,
            foto: fotoName,
            position: position
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const mediaElement = destacada.querySelector('.foto-preview-destacada');
            
            // Actualizar clases de posición
            mediaElement.classList.remove('image-left', 'image-center', 'image-right');
            mediaElement.classList.add(`image-${position}`);
            
            // Actualizar botones
            const buttons = document.querySelectorAll('.position-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-position') === position) {
                    btn.classList.add('active');
                }
            });
            
            destacada.setAttribute('data-position', position);
        } else {
            console.error('Error al actualizar la posición');
            alert('Error al actualizar la posición de la imagen');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al comunicarse con el servidor');
    });
}

// Event listener para los botones de posición
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('position-btn')) {
            const position = e.target.getAttribute('data-position');
            if (position) {
                updateImagePosition(position);
            }
        }
    });
});

function mostrarFormulario(modo, data = null) {
    document.getElementById('listadoPosts').style.display = 'none';
    document.getElementById('formularioPost').style.display = 'block';
    
    const form = document.getElementById('postForm');
    const tituloForm = document.getElementById('formularioTitulo');
    const methodInput = document.getElementById('formMethod');
    
    form.reset();
    
    if (modo === 'crear') {
        tituloForm.textContent = 'Nuevo Post';
        form.action = '{{ route('foroadmin.storepost') }}';
        methodInput.value = 'POST';
    } else if (modo === 'editar' && data) {
        tituloForm.textContent = 'Editar Post';
        form.action = `/foroadmin/post/${data.id}`;
        methodInput.value = 'PUT';
        
        if (document.getElementById('postId')) {
            document.getElementById('postId').value = data.id;
        }
        if (document.getElementById('titulo')) {
            document.getElementById('titulo').value = data.titulo || '';
        }
    }
}

function mostrarListado() {
    document.getElementById('formularioPost').style.display = 'none';
    document.getElementById('listadoPosts').style.display = 'block';
}

function editarPost(id) {
    // Corregida la URL para coincidir con la ruta del controlador
    fetch(`/foroadmin/post/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        mostrarFormulario('editar', data);
    })
    .catch(error => {
        console.error('Error en editarPost:', error);
    });
}

function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de querer eliminar este post?')) {
        fetch(`/foroadmin/post/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => {
            location.reload();
        });
    }
}

function verPost(id) {
    window.location.href = `/foros/${id_blog}/${id}`;
}

// Evento al cargar el documento
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el envío del formulario
    const form = document.getElementById('postForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const method = document.getElementById('formMethod').value;
            
            fetch(form.action, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Error al guardar:', error);
            });
        });
    }

    // Manejar alertas
    const alertList = document.querySelectorAll('.alert');
    alertList.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    });
});
    </script>

    <!-- Justo antes de cerrar el </body> -->
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.tiny.cloud/1/z94ao1xzansr93pi0qe5kfxgddo1f4ltb8q7qa8pw9g52txs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
    selector: '#contenido',
    plugins: 'emoticons',
    toolbar: 'undo redo | bold italic underline | emoticons',
    menubar: false,
    height: 300,
    forced_root_block: false,
    
    // Configuración para prevenir etiquetas HTML
    verify_html: false,
    cleanup: true,
    paste_as_text: true,
    
    // Mantener el valor anterior si existe
    setup: function (editor) {
        editor.on('init', function () {
            // Asegura que el valor inicial se mantenga
            let initialContent = editor.getElement().value;
            if (initialContent) {
                editor.setContent(initialContent);
            }
        });

        editor.on('change', function () {
            // Obtener el texto plano y actualizar el textarea
            let contenidoLimpio = editor.getBody().innerText;
            editor.targetElm.value = contenidoLimpio;
        });
        
        // Manejar la validación de Laravel
        editor.on('blur', function() {
            if (editor.getContent().trim().length === 0) {
                editor.getElement().classList.add('border-red-500');
            } else {
                editor.getElement().classList.remove('border-red-500');
            }
        });
    },
    
    // Estilo visual adaptado a tu diseño Tailwind
    skin: 'oxide',
    content_css: false,
    content_style: `
        body {
            background-color: #2b2b2b;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            padding: 10px;
            border-radius: 0.375rem;
            min-height: 200px;
        }
        a { color: #1e90ff; }
    `,
    
    init_instance_callback: function (editor) {
        // Obtener todos los elementos de la interfaz de TinyMCE
        let elementsToUpdate = editor.getContainer().querySelectorAll(
            '.tox-tinymce, .tox-editor-header, .tox-toolbar, .tox-toolbar__primary, ' +
            '.tox-toolbar__group, .tox-button, .tox-statusbar, .tox-editor-container, ' +
            '.tox-edit-area'
        );

        // Aplicar estilos consistentes con tu diseño
        elementsToUpdate.forEach(function(element) {
            element.style.backgroundColor = '#ad002a';
            element.style.border = 'none';
            element.style.boxShadow = 'none';
        });

        // Estilizar el contenedor principal
        let mainContainer = editor.getContainer();
        mainContainer.style.border = '1px solid #2b2b2b';
        mainContainer.style.boxShadow = 'none';
        mainContainer.style.borderRadius = '0.375rem'; // rounded-md en Tailwind

        // Estilizar los botones
        let buttons = editor.getContainer().querySelectorAll('.tox-button span, .tox-toolbar__group button');
        buttons.forEach(function(button) {
            button.style.color = '#ffffff';
        });

        // Agregar clases de Tailwind para focus
        editor.getElement().addEventListener('focus', function() {
            mainContainer.style.borderColor = '#3b82f6'; // focus:border-blue-500
            mainContainer.style.boxShadow = '0 0 0 1px #3b82f6'; // focus:ring-blue-500
        });

        editor.getElement().addEventListener('blur', function() {
            mainContainer.style.borderColor = '#2b2b2b';
            mainContainer.style.boxShadow = 'none';
        });
    }
});
</script>
</body>

</html>