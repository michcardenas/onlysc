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
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    }).catch(error => {
        console.error('Error al inicializar TinyMCE:', error);
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
<script src="https://cdn.tiny.cloud/1/z94ao1xzansr93pi0qe5kfxgddo1f4ltb8q7qa8pw9g52txs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#contenido',
    plugins: 'emoticons lists',
    toolbar: [
        'undo redo | blocks | formatselect | bold italic underline',
        'alignleft aligncenter alignright | bullist numlist | emoticons'
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
            font-family: 'Poppins', sans-serif;
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

        // Ajustar específicamente el selector de formato
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


<script>
            document.getElementById('fotos').addEventListener('change', function(e) {
                const fileCount = e.target.files.length;
                document.getElementById('fotosFileNameDisplay').textContent = 
                    fileCount > 0 ? `${fileCount} ${fileCount === 1 ? 'archivo seleccionado' : 'archivos seleccionados'}` : '';
            });
            </script>
</body>

</html>