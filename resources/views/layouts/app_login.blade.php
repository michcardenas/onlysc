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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choices-custom.css') }}">
    <!-- En el <head> de tu HTML -->
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Choices.js JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
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
            desdeInput.value = 'Full Time';
            hastaInput.value = 'Full Time';
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

        // Asegurar que los valores se envíen incluso cuando los inputs estén deshabilitados
        updateHiddenInputs(dia);
    }
    
    function updateHiddenInputs(dia) {
        // Eliminar inputs ocultos anteriores si existen
        removeExistingHiddenInputs(dia);

        const desdeInput = document.getElementById(`desde-${dia}`);
        const hastaInput = document.getElementById(`hasta-${dia}`);
        const form = desdeInput.closest('form');

        // Solo crear inputs ocultos si los campos están deshabilitados (Full Time)
        if (desdeInput.disabled && hastaInput.disabled) {
            const hiddenDesde = document.createElement('input');
            hiddenDesde.type = 'hidden';
            hiddenDesde.name = `horario[${dia}][desde]`;
            hiddenDesde.value = desdeInput.value;
            form.appendChild(hiddenDesde);

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

    // Event listeners para los checkboxes de full time
    document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const dia = this.id.replace('fulltime-', '');
            const diaCheckbox = document.getElementById(`admin-dia-${dia}`);
            
            if (checkbox.checked && !diaCheckbox.checked) {
                diaCheckbox.checked = true; // Asegurarse que el día esté marcado al seleccionar Full Time
            }
            
            toggleFullTime(this, dia);
        });
    });

    // Event listener para el envío del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
                const dia = checkbox.id.replace('fulltime-', '');
                const diaCheckbox = document.getElementById(`admin-dia-${dia}`);
                
                // Solo actualizar los inputs ocultos si tanto el día como Full Time están marcados
                if (checkbox.checked && diaCheckbox.checked) {
                    updateHiddenInputs(dia);
                }
            });
        });
    }

    // Inicializar estado de los inputs al cargar
    document.querySelectorAll('[id^="fulltime-"]').forEach(checkbox => {
        if (checkbox.checked) {
            const dia = checkbox.id.replace('fulltime-', '');
            const diaCheckbox = document.getElementById(`admin-dia-${dia}`);
            
            if (diaCheckbox.checked) {
                toggleFullTime(checkbox, dia);
            }
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
    const isFixedCheckbox = document.getElementById('is_fixed');
    
    form.reset();
    
    if (modo === 'crear') {
        tituloForm.textContent = 'Nuevo Post';
        form.action = '{{ route('foroadmin.storepost') }}';
        methodInput.value = 'POST';
        isFixedCheckbox.checked = false;
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
        if (isFixedCheckbox) {
            isFixedCheckbox.checked = data.is_fixed || false;
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

// JavaScript actualizado
function toggleFixed(postId) {
    // Mostrar indicador de carga
    const button = document.querySelector(`button[onclick="toggleFixed(${postId})"]`);
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    fetch(`/posts/${postId}/toggle-fixed`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Actualizar la UI sin recargar la página
            const row = button.closest('tr');
            const statusBadge = row.querySelector('.badge');
            const pinIcon = row.querySelector('.fa-thumbtack');
            
            // Actualizar estado visual
            if (data.is_fixed) {
                statusBadge.textContent = 'Fijado';
                statusBadge.classList.remove('bg-secondary');
                statusBadge.classList.add('bg-primary');
                row.classList.add('bg-light');
                if (!pinIcon) {
                    const titleCell = row.querySelector('td:nth-child(2)');
                    titleCell.innerHTML += ' <i class="fas fa-thumbtack text-primary ml-2" title="Post Fijado"></i>';
                }
            } else {
                statusBadge.textContent = 'Normal';
                statusBadge.classList.remove('bg-primary');
                statusBadge.classList.add('bg-secondary');
                row.classList.remove('bg-light');
                if (pinIcon) {
                    pinIcon.remove();
                }
            }
            
            // Mostrar mensaje de éxito
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.section-content').insertBefore(alert, document.querySelector('.table-admin'));
            
            // Remover alerta después de 3 segundos
            setTimeout(() => alert.remove(), 3000);
            
            // Reordenar las filas si es necesario
            const tbody = row.parentElement;
            const rows = Array.from(tbody.children);
            rows.sort((a, b) => {
                const aFixed = a.querySelector('.badge').textContent === 'Fijado' ? 1 : 0;
                const bFixed = b.querySelector('.badge').textContent === 'Fijado' ? 1 : 0;
                return bFixed - aFixed;
            });
            rows.forEach(row => tbody.appendChild(row));
        } else {
            throw new Error(data.message || 'Error al actualizar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado del post: ' + error.message);
    })
    .finally(() => {
        // Restaurar el botón
        button.innerHTML = originalContent;
        button.disabled = false;
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

function verPost(idBlog, idPost) {
    window.location.href = `/foros/${idBlog}/${idPost}`;
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

<script>
// Variables globales para Choices.js
let categoriaChoices = null;
let tagsChoices = null;

// Inicialización de componentes
function initializeEditor() {
    const contenidoTextarea = document.querySelector('#contenido');
    if (!contenidoTextarea) return;

    return tinymce.init({
    selector: '#contenido',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: [
        'undo redo | formatselect | styles | bold italic | alignleft aligncenter alignright alignjustify',
        'bullist numlist outdent indent | media image link | removeformat'
    ],
    media_live_embeds: true, // Habilita vistas previas en vivo
    media_alt_source: false,
    media_poster: false,
    convert_urls: false,
    extended_valid_elements: 'iframe[src|frameborder|style|scrolling|class|width|height|name|align|allowfullscreen]',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});

}
function initializeChoices() {
    try {
        // Inicializar categorías
        const categoriaSelect = document.querySelector('#categoria');
        if (categoriaSelect && typeof Choices !== 'undefined') {
            categoriaChoices = new Choices(categoriaSelect, {
                removeItemButton: true,
                maxItemCount: 5,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'Selecciona las categorías',
                duplicateItemsAllowed: false // Evitar duplicados
            });
        }

        // Inicializar tags
        const tagsSelect = document.querySelector('#tags');
        if (tagsSelect && typeof Choices !== 'undefined') {
            tagsChoices = new Choices(tagsSelect, {
                removeItemButton: true,
                maxItemCount: 10,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'Selecciona los tags',
                duplicateItemsAllowed: false, // Evitar duplicados
                removeItems: true, // Asegurar que se pueden remover items
                removeItemButton: true // Mostrar botón de eliminar
            });
        }
    } catch (error) {
        console.error('Error al inicializar Choices.js:', error);
    }
}

// Funciones de gestión del formulario
function blogMostrarFormulario(tipo, id = null) {
    console.log('Mostrando formulario:', tipo, 'ID:', id);
    
    document.getElementById('listadoArticulos').style.display = 'none';
    document.getElementById('formularioArticulo').style.display = 'block';
    
    const formularioTitulo = document.getElementById('formularioTitulo');
    const form = document.getElementById('articuloForm');
    
    if (tipo === 'crear') {
        formularioTitulo.textContent = 'Nuevo Artículo';
        form.reset();
        form.action = '/blogadmin/store';
        document.getElementById('formMethod').value = 'POST';
        
        // Limpiar editor
        if (tinymce.get('contenido')) {
            tinymce.get('contenido').setContent('');
        }

        // Limpiar Choices
        if (categoriaChoices) categoriaChoices.removeActiveItems();
        if (tagsChoices) tagsChoices.removeActiveItems();

        // Limpiar imagen
        limpiarImagenPreview();
    } else {
        formularioTitulo.textContent = 'Editar Artículo';
        blogFetchArticleData(id);
    }
}

function limpiarImagenPreview() {
    const imagenInput = document.getElementById('imagen');
    if (imagenInput) {
        const preview = imagenInput.parentNode.querySelector('.imagen-preview');
        if (preview) preview.remove();
    }
}

function mostrarImagenPreview(imagenUrl) {
    if (!imagenUrl) return;

    const imagenInput = document.getElementById('imagen');
    const existingPreview = imagenInput.parentNode.querySelector('.imagen-preview');
    if (existingPreview) existingPreview.remove();

    const imagenPreview = document.createElement('div');
    imagenPreview.className = 'mt-2 imagen-preview';
    imagenPreview.innerHTML = `
        <p class="mb-2">Imagen actual:</p>
        <img src="${imagenUrl}" alt="Imagen actual" style="max-width: 200px; max-height: 200px;" class="mb-2">
        <input type="hidden" name="imagen_actual" value="${imagenUrl}">
    `;
    
    imagenInput.parentNode.appendChild(imagenPreview);
}

// Funciones de navegación
function blogMostrarListado() {
    document.getElementById('formularioArticulo').style.display = 'none';
    document.getElementById('listadoArticulos').style.display = 'block';
}

function blogVerArticulo(id) {
    window.location.href = `/blog/${id}`;
}

function blogEditarArticulo(id) {
    blogMostrarFormulario('editar', id);
}


// Función para cargar datos del artículo
async function blogFetchArticleData(id) {
    try {
        const response = await fetch(`/blogadmin/edit/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        // Actualizar campos básicos
        document.getElementById('articuloId').value = data.id;
        document.getElementById('titulo').value = data.titulo;
        document.getElementById('estado').value = data.estado || 'borrador';
        document.getElementById('destacado').checked = Boolean(data.destacado);

        // Actualizar editor
        if (tinymce.get('contenido')) {
            tinymce.get('contenido').setContent(data.contenido || '');
        }

        // Actualizar categorías
        if (categoriaChoices && data.categories) {
            categoriaChoices.removeActiveItems();
            const categoryIds = data.categories.map(cat => cat.id.toString());
            categoriaChoices.setChoiceByValue(categoryIds);
        }

        // Actualizar tags
        if (tagsChoices && data.tags) {
            tagsChoices.removeActiveItems();
            const tagIds = data.tags.map(tag => tag.id.toString());
            console.log('Tags a establecer:', tagIds); // Debug
            tagsChoices.setValue(tagIds); // Usar setValue en lugar de setChoiceByValue
        }

        // Actualizar formulario
        const form = document.getElementById('articuloForm');
        form.action = `/blogadmin/update/${data.id}`;
        document.getElementById('formMethod').value = 'PUT';

        // Mostrar imagen
        mostrarImagenPreview(data.imagen);

    } catch (error) {
        console.error('Error completo:', error);
        alert(`Error al cargar los datos del artículo: ${error.message}`);
    }
}

// Funciones de acciones
function blogConfirmarEliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este artículo?')) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/blogadmin/delete/${id}`;
        deleteForm.submit();
    }
}

async function blogToggleDestacado(id) {
    try {
        // Usar la URL definida en la vista
        const url = toggleDestacadoUrl.replace(':id', id);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al actualizar el estado destacado');
        }
        
        const data = await response.json();
        const button = document.querySelector(`button[onclick="blogToggleDestacado(${id})"]`);
        
        if (button) {
            // Cambiar el ícono
            if (data.destacado) {
                button.innerHTML = '<i class="fas fa-star"></i>';
                button.setAttribute('title', 'Quitar de destacados');
            } else {
                button.innerHTML = '<i class="fas fa-star-o"></i>';
                button.setAttribute('title', 'Marcar como destacado');
            }
        }
        
        // Mostrar mensaje de éxito
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.section-content');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remover la alerta después de 3 segundos
        setTimeout(() => alertDiv.remove(), 3000);
        
    } catch (error) {
        console.error('Error:', error);
        
        // Mostrar mensaje de error
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            Error al actualizar el estado destacado
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.section-content');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remover la alerta después de 3 segundos
        setTimeout(() => alertDiv.remove(), 3000);
    }
}

// Manejador del formulario
function setupFormHandler() {
    const form = document.getElementById('articuloForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            const method = document.getElementById('formMethod').value;

            // Obtener y validar contenido del editor
            if (tinymce.get('contenido')) {
                const contenido = tinymce.get('contenido').getContent();
                if (!contenido.trim()) {
                    alert('El contenido es requerido');
                    return;
                }
                formData.set('contenido', contenido);
            }

            // Asegurar que se recojan todos los tags seleccionados
            if (tagsChoices) {
                const selectedTags = tagsChoices.getValue();
                // Limpiar tags anteriores del FormData
                formData.delete('tags[]');
                // Agregar cada tag seleccionado
                selectedTags.forEach(tag => {
                    formData.append('tags[]', tag.value);
                });
            }

            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Error al procesar el formulario');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar el formulario');
        }
    });
}


// Inicialización cuando el documento está listo
document.addEventListener('DOMContentLoaded', async function() {
    await initializeEditor();
    initializeChoices();
    setupFormHandler();
});
</script>
<script>
    const toggleDestacadoUrl = "{{ route('blogadmin.toggle-destacado', ':id') }}";
</script>

<script>
// Funciones para tags
function mostrarFormularioTag(tipo, id = null) {
    const form = document.getElementById('formularioTag');
    const titulo = document.getElementById('formularioTagTitulo');
    const tagForm = document.getElementById('tagForm');
    
    form.style.display = 'block';
    if (tipo === 'crear') {
        titulo.textContent = 'Nuevo Tag';
        tagForm.reset();
        tagForm.action = '/blogadmin/tags/store';
        document.getElementById('tagMergeMethod').value = 'POST';
    } else {
        titulo.textContent = 'Editar Tag';
        cargarDatosTag(id);
    }
}

function ocultarFormularioTag() {
    document.getElementById('formularioTag').style.display = 'none';
}

function cargarDatosTag(id) {
    fetch(`/blogadmin/tags/edit/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('nombreTag').value = data.nombre;
            document.getElementById('tagMergeMethod').value = 'PUT';
            document.getElementById('tagForm').action = `/blogadmin/tags/update/${id}`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del tag');
        });
}

function manejarSubmitTag(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: formData.get('_method') || 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error en la operación');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error en la operación');
    });
}

function confirmarEliminarTag(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este tag?')) {
        fetch(`/blogadmin/tags/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error al eliminar el tag');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el tag');
        });
    }
}

// Funciones para categorías
function mostrarFormularioCategoria(tipo, id = null) {
    const form = document.getElementById('formularioCategoria');
    const titulo = document.getElementById('formularioCategoriaTitulo');
    const categoriaForm = document.getElementById('categoriaForm');
    
    form.style.display = 'block';
    if (tipo === 'crear') {
        titulo.textContent = 'Nueva Categoría';
        categoriaForm.reset();
        categoriaForm.action = '/blogadmin/categories/store';
        document.getElementById('categoriaMergeMethod').value = 'POST';
    } else {
        titulo.textContent = 'Editar Categoría';
        cargarDatosCategoria(id);
    }
}

function ocultarFormularioCategoria() {
    document.getElementById('formularioCategoria').style.display = 'none';
}

function cargarDatosCategoria(id) {
    fetch(`/blogadmin/categories/edit/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('nombreCategoria').value = data.nombre;
            document.getElementById('descripcionCategoria').value = data.descripcion;
            document.getElementById('categoriaMergeMethod').value = 'PUT';
            document.getElementById('categoriaForm').action = `/blogadmin/categories/update/${id}`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos de la categoría');
        });
}

function manejarSubmitCategoria(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: formData.get('_method') || 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error en la operación');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error en la operación');
    });
}

function confirmarEliminarCategoria(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        fetch(`/blogadmin/categories/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error al eliminar la categoría');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la categoría');
        });
    }
}
</script>

    <!-- Justo antes de cerrar el </body> -->
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.tiny.cloud/1/kql75cq5qezo4szqfurmv0g3uxe4cpksh2e1f4zzk1autj96/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#contenido',
    plugins: 'emoticons lists media',
    toolbar: [
        'undo redo | blocks | formatselect | bold italic underline',
        'alignleft aligncenter alignright | bullist numlist | emoticons | media'
    ],
    menubar: false,
    height: 300,
    forced_root_block: 'p',
    
    block_formats: 'Párrafo=p; Título 1=h1; Título 2=h2',
    
    formats: {
        h1: { 
            block: 'h1',
            clear_formatting: true
        },
        h2: { 
            block: 'h2',
            clear_formatting: true
        },
        p: { 
            block: 'p',
            clear_formatting: true
        },
        bold: { inline: 'strong' },
        italic: { inline: 'em' },
        underline: { inline: 'span', styles: { 'text-decoration': 'underline' } }
    },
    
    style_formats: [
        {
            title: 'Bloque',
            items: [
                { title: 'Párrafo', format: 'p' },
                { title: 'Título 1', format: 'h1' },
                { title: 'Título 2', format: 'h2' }
            ]
        }
    ],
    
    verify_html: true,
    cleanup: true,
    paste_as_text: true,
    
    media_live_embeds: true,
    extended_valid_elements: 'iframe[src|frameborder|style|scrolling|class|width|height|name|align|allowfullscreen]',
    
    setup: function (editor) {
        editor.on('init', function () {
            let initialContent = editor.getElement().value;
            if (initialContent) {
                editor.setContent(initialContent);
            }
        });

        editor.on('change', function () {
            let contenido = editor.getContent();
            editor.targetElm.value = contenido;
        });
        
        editor.on('NewBlock', function(e) {
            if (e.newBlock) {
                e.newBlock.nodeName = 'P';
                editor.formatter.remove('h1', e.newBlock);
                editor.formatter.remove('h2', e.newBlock);
            }
        });
    },
    
    skin: 'oxide',
    content_css: false,
    content_style: `
        body {
            background-color: #2b2b2b;
            color: #e0e0e0;
            font-family: 'Montserrat', sans-serif;
            padding: 10px;
            border-radius: 0.375rem;
            min-height: 200px;
        }
        
        /* Estilos para el selector de formato y menú desplegable */
        .tox .tox-tbtn[aria-haspopup="true"],
        .tox .tox-tbtn--select,
        .tox .tox-tbtn[aria-label="Blocks"],
        .tox-listbox__select-label {
            background-color: #2b2b2b !important;
            border: none !important;
            color: #ffffff !important;
        }
        
        /* Estilo para el menú desplegable */
        .tox .tox-collection--list,
        .tox .tox-menu {
            background-color: #2b2b2b !important;
            border: 1px solid #3b3b3b !important;
        }
        
        .tox .tox-collection__item {
            background-color: #2b2b2b !important;
            color: #ffffff !important;
        }
        
        .tox .tox-collection__item:hover {
            background-color: #3b3b3b !important;
        }
        
        .tox .tox-collection__item-label {
            color: #ffffff !important;
        }
        
        /* Resto de los estilos */
        a { color: #1e90ff; }
        h1, h2, h3, h4, h5, h6 {
            color: #e0e0e0 !important;
            margin: 16px 0;
            font-weight: bold;
            display: block !important;
            visibility: visible !important;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        h2 {
            font-size: 20px;
            font-weight: bold;
            margin: 16px 0;
        }
        p {
            margin: 10px 0;
            line-height: 1.6;
            color: #e0e0e0;
        }
        * {
            color: #e0e0e0;
        }
        .emoji {
            font-size: 1.2em;
            vertical-align: middle;
        }
        iframe {
            background-color: transparent;
            width: 100%;
            max-width: 560px;
            height: 315px;
            border: none;
            margin: 0 auto;
            display: block;
        }
    `,
    
    init_instance_callback: function (editor) {
        let elementsToUpdate = editor.getContainer().querySelectorAll(
            '.tox-tinymce, .tox-editor-header, .tox-toolbar, .tox-toolbar__primary, ' +
            '.tox-toolbar__group, .tox-button, .tox-statusbar, .tox-editor-container, ' +
            '.tox-edit-area, .tox-tbtn[aria-label="Blocks"], select'
        );

        elementsToUpdate.forEach(function(element) {
            element.style.backgroundColor = '#e00037';
            element.style.border = 'none';
            element.style.boxShadow = 'none';
        });

        let formatSelect = editor.getContainer().querySelector('.tox-tbtn[aria-label="Blocks"]');
        if (formatSelect) {
            formatSelect.style.backgroundColor = '#2b2b2b';
            formatSelect.style.color = '#ffffff';
            formatSelect.style.border = 'none';
        }

        let mainContainer = editor.getContainer();
        mainContainer.style.border = '1px solid #2b2b2b';
        mainContainer.style.boxShadow = 'none';
        mainContainer.style.borderRadius = '0.375rem';

        let buttons = editor.getContainer().querySelectorAll('.tox-button span, .tox-toolbar__group button');
        buttons.forEach(function(button) {
            button.style.color = '#ffffff';
        });

        editor.getElement().addEventListener('focus', function() {
            mainContainer.style.borderColor = '#3b82f6';
            mainContainer.style.boxShadow = '0 0 0 1px #3b82f6';
        });

        editor.getElement().addEventListener('blur', function() {
            mainContainer.style.borderColor = '#2b2b2b';
            mainContainer.style.boxShadow = 'none';
        });
    }
});

</script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('preview-foto').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCE-YA3ZXTQ0uMGWjENmAG274nUWOM7-Kc&libraries=places&callback=initMap">
</script>
<script>
let map;
let marker;
let searchBox;

// Agregar el estilo directamente
const style = document.createElement('style');
style.textContent = `
    .pac-container {
        position: fixed !important;
        top: 345px !important;
        width: 1400px !important;
    }
`;
document.head.appendChild(style);

function initMap() {
    try {
        // Coordenadas iniciales (centro de Santiago de Chile si no hay ubicación)
        const initialLocation = {
            lat: {{ old('latitud', $usuario->location->latitud ?? -33.4489) }},
            lng: {{ old('longitud', $usuario->location->longitud ?? -70.6693) }}
        };

        // Opciones del mapa
        const mapOptions = {
            zoom: 15,
            center: initialLocation,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        };

        // Crear el mapa
        map = new google.maps.Map(document.getElementById('map-container'), mapOptions);

        // Crear el marcador 
        marker = new google.maps.Marker({
            position: initialLocation,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Configurar búsqueda
        const input = document.getElementById('search-input');
        searchBox = new google.maps.places.SearchBox(input);
        
        // Bias hacia la vista actual del mapa
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        // Escuchar eventos de la búsqueda
        searchBox.addListener('places_changed', function() {
            const places = searchBox.getPlaces();
            if (places.length === 0) return;

            const place = places[0];
            if (!place.geometry) return;

            // Centrar mapa en la ubicación seleccionada
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);

            // Actualizar campos
            updateFields(place.geometry.location, place.formatted_address);
        });

        // Actualizar campos cuando se mueve el marcador
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            const geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({ location: position }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    updateFields(position, results[0].formatted_address);
                }
            });
        });

        // Click en el mapa para mover el marcador
        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: e.latLng }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    updateFields(e.latLng, results[0].formatted_address);
                }
            });
        });

    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
    }
}

function updateFields(location, address) {
    document.getElementById('latitud').value = location.lat().toFixed(6);
    document.getElementById('longitud').value = location.lng().toFixed(6);
    document.getElementById('direccion_mapa').value = address;
}

// Cargar el mapa cuando Google Maps esté listo
window.initMap = initMap;
</script>


<script>
            document.getElementById('fotos').addEventListener('change', function(e) {
                const fileCount = e.target.files.length;
                document.getElementById('fotosFileNameDisplay').textContent = 
                    fileCount > 0 ? `${fileCount} ${fileCount === 1 ? 'archivo seleccionado' : 'archivos seleccionados'}` : '';
            });
            </script>
</body>

<!-- Scripts -->
<script>
function confirmarEliminarPerfil(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este perfil? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/perfil/${id}/eliminar`;
        form.submit();
    }
}
</script>


<script>
document.getElementById('seoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const ciudadSeleccionada = document.getElementById('ciudad').value;
    const page = 'inicio-' + ciudadSeleccionada;
    
    // Construir la URL correctamente
    const baseUrl = '{{ route('seo.update', ':page') }}';
    const finalUrl = baseUrl.replace(':page', page);
    
    // Actualizar la acción del formulario
    this.action = finalUrl;
    
    // Enviar el formulario
    this.submit();
});


function toggleContentBlock(button) {
    const previewItem = button.closest('.publicate-preview-item');
    const overlay = previewItem.querySelector('.content-overlay');
    const userId = previewItem.dataset.userId;
    const foto = previewItem.dataset.foto;
    
    const isCurrentlyBlocked = overlay.style.display === 'flex';
    
    fetch('/usuarios-publicate/toggle-image-block', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_id: userId,
            image: foto,
            blocked: !isCurrentlyBlocked // Invertimos el estado actual
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (overlay.style.display === 'none' || overlay.style.display === '') {
                overlay.style.display = 'flex';
                button.textContent = 'Desbloquear';
                button.classList.add('active');
            } else {
                overlay.style.display = 'none';
                button.textContent = 'Bloquear';
                button.classList.remove('active');
            }
            console.log('Estado actual:', data); // Para debug
        } else {
            alert('Error al actualizar el estado de bloqueo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}

</script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('templatesForm');
    const ciudadSelect = document.getElementById('global_ciudad');
    const selectedCiudadInput = document.getElementById('selected_ciudad_id');
    // Manejar el envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar que se haya seleccionado una ciudad
        if (!ciudadSelect.value) {
            alert('Por favor, seleccione una ciudad');
            return;
        }
        // Actualizar el ID de la ciudad seleccionada
        selectedCiudadInput.value = ciudadSelect.value;
        // Recopilar todos los templates
        const templates = [];
        document.querySelectorAll('textarea[data-tipo]').forEach(textarea => {
            if (!textarea.dataset.tipo.includes('title') && textarea.value.trim()) {
                // Buscar el título correspondiente
                const titleTextarea = document.querySelector(`textarea[data-tipo="${textarea.dataset.tipo}_title"]`);
                templates.push({
                    tipo: textarea.dataset.tipo,
                    titulo: titleTextarea ? titleTextarea.value : '',
                    description_template: textarea.value,
                });
            }
        });
        // Enviar los datos usando fetch
        fetch('/seo/templates/update-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ciudad_id: ciudadSelect.value,
                templates: templates
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Templates guardados correctamente');
            } else {
                alert(data.message || 'Error al guardar los templates');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar los templates');
        });
    });
    // Inicializar previews al cargar la página
    document.querySelectorAll('textarea[data-tipo]').forEach(textarea => {
        const tipo = textarea.dataset.tipo;
        updatePreview(tipo);
    });
    // Actualizar previews cuando cambia la ciudad
    ciudadSelect.addEventListener('change', function() {
        const ciudadId = this.value;
        selectedCiudadInput.value = ciudadId;
        
        if (ciudadId) {
            loadTemplatesByCiudad(ciudadId);
        }
    });
});
// Función para actualizar la vista previa de cualquier template
function updatePreview(tipo) {
    // Obtener el textarea y el contenedor de vista previa
    const textarea = document.getElementById(`${tipo}_template`);
    const previewContainer = document.getElementById(`${tipo}_preview`);
    
    if (!textarea || !previewContainer) return;
    // Obtener la ciudad seleccionada
    const ciudadSelect = document.getElementById('global_ciudad');
    const ciudadNombre = ciudadSelect.options[ciudadSelect.selectedIndex]?.text || 'Ciudad';
    // Datos de ejemplo para la vista previa
    const previewData = {
        ciudad: ciudadNombre,
        sector: 'Centro',
        nacionalidad: 'brasileñas',
        edad_min: '18',
        edad_max: '35',
        precio_min: '50000',
        precio_max: '150000',
        atributos: 'altura 170cm, peso 55kg, medidas 90-60-90',
        servicios: 'masajes, compañía VIP',
        disponible: 'disponible ahora',
        resena: 'con reseñas verificadas',
        categorias: 'VIP, Premium'
    };
    // Obtener el template actual
    let preview = textarea.value;
    // Reemplazar todas las variables en el template
    Object.entries(previewData).forEach(([key, value]) => {
        const regex = new RegExp(`{${key}}`, 'g');
        preview = preview.replace(regex, value);
    });
    // Actualizar el contenedor de vista previa
    previewContainer.textContent = preview || 'Vista previa del template...';
}
// Inicializar los event listeners cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listeners a todos los textareas
    const textareas = document.querySelectorAll('textarea[data-tipo]');
    textareas.forEach(textarea => {
        const tipo = textarea.dataset.tipo;
        
        // Actualizar preview cuando se escribe
        textarea.addEventListener('input', () => updatePreview(tipo));
        
        // Actualizar preview inicial
        updatePreview(tipo);
    });
    // Actualizar todas las previews cuando cambia la ciudad
    const ciudadSelect = document.getElementById('global_ciudad');
    if (ciudadSelect) {
        ciudadSelect.addEventListener('change', () => {
            textareas.forEach(textarea => {
                updatePreview(textarea.dataset.tipo);
            });
        });
    }
});
// Función para cargar templates desde el servidor
function loadTemplatesByCiudad(ciudadId) {
    if (!ciudadId) return;
    fetch(`/seo/templates/${ciudadId}`)
        .then(response => response.json())
        .then(data => {
            // Cargar templates generales
            ['single', 'multiple', 'complex'].forEach(tipo => {
                const textarea = document.getElementById(`${tipo}_template`);
                if (textarea && data[tipo]) {
                    textarea.value = data[tipo];
                    updatePreview(tipo);
                }
            });
            // Cargar templates unitarios
            ['ciudad', 'nacionalidad', 'edad', 'precio', 'atributos', 
             'servicios', 'disponible', 'resena', 'categorias'].forEach(filtro => {
                const textarea = document.getElementById(`${filtro}_template`);
                if (textarea && data.filtros && data.filtros[filtro]) {
                    textarea.value = data.filtros[filtro];
                    updatePreview(filtro);
                }
            });
        })
        .catch(error => {
            console.error('Error cargando templates:', error);
        });
}
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '.tinymce-editor',
        height: 150,
        min_height: 150,
        max_height: 300,
        resize: true,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
            'anchor', 'searchreplace', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist | ' +
            'removeformat',
        content_css: false,
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; margin: 0; background-color: #1a1a1a; color: white; }',
        forced_root_block: 'p',
        protect: [
            /\<\/?(if|endif)\>/g,  // Protect conditional comments
            /\<xsl\:[^>]+\>/g,     // Protect XSL tags
        ],
        setup: function(editor) {
            editor.on('init', function() {
                editor.getContainer().style.transition = "none";
                this.getBody().style.maxHeight = "300px";
            });
            
            editor.on('change', function() {
                // Obtenemos el contenido y procesamos los saltos de línea
                const content = editor.getContent({format: 'html'});
                const previewId = editor.targetElm.getAttribute('data-tipo') + '_preview';
                const previewDiv = document.getElementById(previewId);
                
                // Aplicamos el contenido a la vista previa
                previewDiv.innerHTML = content;
                
                // Copiamos los estilos aplicados en el editor
                const editorContent = editor.getBody();
                const previewContent = previewDiv.getElementsByTagName('p')[0];
                if(previewContent && editorContent) {
                    const computedStyle = window.getComputedStyle(editorContent);
                    previewContent.style.cssText = computedStyle.cssText;
                }
            });
            // Modo de visualización sin código
            editor.on('BeforeSetContent', function(e) {
                // Mantener el HTML pero mostrar solo el texto formateado
                if(e.content) {
                    e.content = e.content.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                }
            });
        },
        init_instance_callback: function(editor) {
            // Configurar el modo de visualización inicial
            editor.getBody().setAttribute('contenteditable', true);
            editor.getBody().setAttribute('data-gramm', false);
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Selectores principales
    const servicioSelect = document.getElementById('servicio_select');
    const atributoSelect = document.getElementById('atributo_select');
    const nacionalidadSelect = document.getElementById('nacionalidad_select');
    const sectorSelect = document.getElementById('sector_select');
    const categoriaSelect = document.getElementById('categoria_select');
    const ciudadSelect = document.getElementById('ciudad_select');
    // Selectores de formularios
    const servicioForm = document.getElementById('servicioForm');
    const atributoForm = document.getElementById('atributoForm');
    const nacionalidadForm = document.getElementById('nacionalidadForm');
    const sectorForm = document.getElementById('sectorForm');
    const disponibilidadForm = document.getElementById('disponibilidadForm');
    const resenasForm = document.getElementById('resenasForm');
    const categoriaForm = document.getElementById('categoriaForm');
    // Función para actualizar los inputs hidden de ciudad
    function actualizarCiudadInputs() {
        const ciudadId = ciudadSelect.value;
        document.querySelectorAll('.ciudad-input').forEach(input => {
            input.value = ciudadId;
        });
    }
    // Mapeo de tipos a nombres de rutas
    const routeNames = {
        'servicio': 'api.servicios.seo',
        'atributo': 'api.atributos.seo',
        'nacionalidad': 'api.nacionalidades.seo',
        'sector': 'api.sectores.seo',
        'categoria': 'api.categorias.seo',
        'disponibilidad': 'api.disponibilidad.seo',
        'resenas': 'api.resenas.seo'
    };
    const updateRouteNames = {
        'servicio': 'seo.servicios.update',
        'atributo': 'seo.atributos.update',
        'nacionalidad': 'seo.nacionalidades.update',
        'sector': 'seo.sectores.update',
        'categoria': 'seo.categorias.update',
        'disponibilidad': 'seo.disponibilidad.update',
        'resenas': 'seo.resenas.update'
    };
    
    // Función para obtener la URL correcta
    function getUrl(tipo, id = null, isUpdate = false) {
        const urlType = isUpdate ? 'update' : 'get';
        let url = routeUrls[tipo][urlType];
        
        if (!isUpdate && id && tipo !== 'disponibilidad' && tipo !== 'resenas') {
            url = url.replace(':id', id);
        }
        
        return url;
    }
    // Función genérica para cargar datos SEO
    function cargarDatosSEO(tipo, id) {
        if (!id) return;
        const ciudadId = ciudadSelect.value;
        document.getElementById(`${tipo}_id_input`).value = id;
        let url = getUrl(tipo, id);
        if (ciudadId) {
            url += (url.includes('?') ? '&' : '?') + 'ciudad_id=' + ciudadId;
        }
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar los datos');
                return response.json();
            })
            .then(data => {
                const campos = [
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'canonical_url',
                    'meta_robots',
                    'heading_h1',
                    'heading_h2',
                    'additional_text'
                ];
                campos.forEach(campo => {
                    const elemento = document.getElementById(`${campo}_${tipo}`);
                    if (elemento) {
                        elemento.value = data[campo] || '';
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`Error al cargar los datos del ${tipo}`);
            });
    }
    // Función genérica para manejar el envío de formularios
    function manejarEnvioFormulario(e, tipo) {
        e.preventDefault();
        const id = document.getElementById(`${tipo}_id_input`)?.value;
        const ciudadId = ciudadSelect.value;
        if (!id && tipo !== 'disponibilidad' && tipo !== 'resenas') {
            alert(`Por favor, seleccione un ${tipo}`);
            return;
        }
        if (!ciudadId) {
            alert('Por favor, seleccione una ciudad');
            return;
        }
        const formData = new FormData(e.target);
        
        fetch(getUrl(tipo, null, true), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`SEO de ${tipo} actualizado correctamente`);
                if (tipo !== 'disponibilidad' && tipo !== 'resenas') {
                    cargarDatosSEO(tipo, id);
                }
            } else {
                throw new Error(data.message || 'Error al actualizar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || `Error al actualizar el SEO de ${tipo}`);
        });
    }
    // Event Listeners para los selects
    const selectores = {
        'servicio': servicioSelect,
        'atributo': atributoSelect,
        'nacionalidad': nacionalidadSelect,
        'sector': sectorSelect,
        'categoria': categoriaSelect
    };
    Object.entries(selectores).forEach(([tipo, selector]) => {
        if (selector) {
            selector.addEventListener('change', function() {
                cargarDatosSEO(tipo, this.value);
            });
        }
    });
    // Event Listeners para los formularios
    const formularios = {
        'servicio': servicioForm,
        'atributo': atributoForm,
        'nacionalidad': nacionalidadForm,
        'sector': sectorForm,
        'disponibilidad': disponibilidadForm,
        'resenas': resenasForm,
        'categoria': categoriaForm
    };
    Object.entries(formularios).forEach(([tipo, formulario]) => {
        if (formulario) {
            formulario.addEventListener('submit', (e) => manejarEnvioFormulario(e, tipo));
        }
    });
    // Event listener para el select de ciudad
    if (ciudadSelect) {
        ciudadSelect.addEventListener('change', function() {
            actualizarCiudadInputs();
            // Recargar datos del formulario activo
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab) {
                const select = activeTab.querySelector('select');
                if (select && select.value) {
                    cargarDatosSEO(select.id.replace('_select', ''), select.value);
                }
            }
        });
    }
    // Event listener para cambios de tab
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            const activeTab = document.querySelector(e.target.getAttribute('data-bs-target'));
            const select = activeTab.querySelector('select');
            if (select && select.value) {
                cargarDatosSEO(select.id.replace('_select', ''), select.value);
            }
        });
    });
    // Inicialización
    actualizarCiudadInputs();
});
    </script>
<script>
    const routeUrls = {
        'servicio': {
            'get': "{{ route('api.servicios.seo', ['servicio' => ':id']) }}",
            'update': "{{ route('seo.servicios.update') }}"
        },
        'atributo': {
            'get': "{{ route('api.atributos.seo', ['atributo' => ':id']) }}",
            'update': "{{ route('seo.atributos.update') }}"
        },
        'nacionalidad': {
            'get': "{{ route('api.nacionalidades.seo', ['nacionalidad' => ':id']) }}",
            'update': "{{ route('seo.nacionalidades.update') }}"
        },
        'sector': {
            'get': "{{ route('api.sectores.seo', ['sector' => ':id']) }}",
            'update': "{{ route('seo.sectores.update') }}"
        },
        'categoria': {
            'get': "{{ route('api.categorias.seo', ['categoria' => ':id']) }}",
            'update': "{{ route('seo.categorias.update') }}"
        },
        'disponibilidad': {
            'get': "{{ route('api.disponibilidad.seo') }}",
            'update': "{{ route('seo.disponibilidad.update') }}"
        },
        'resenas': {
            'get': "{{ route('api.resenas.seo') }}",
            'update': "{{ route('seo.resenas.update') }}"
        }
    };
</script>
</html>