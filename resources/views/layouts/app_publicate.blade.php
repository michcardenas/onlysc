<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Título de la pestaña -->
    <title>Escorts</title>
    
    <!-- Icono de la pestaña (favicon) -->
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('css/publicate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .styled-select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    color: #333;
    outline: none;
    transition: border-color 0.3s ease;
}

.styled-select:focus {
    border-color: #f00; /* Ajusta el color según el diseño */
}

.publicate-input-wrapper {
    position: relative;
    margin-bottom: 20px;
}

.publicate-input-wrapper label {
    position: absolute;
    top: -10px;
    left: 10px;
    font-size: 14px;
    pointer-events: none;
}
@media (max-width: 768px) {
    .publicate-input-wrapper label {
        top: -15px;
        left: 15px;
        font-size: 16px;
    }
    .publicate-image-section {
     display: none;
    }
    .publicate-logo{
        width: 100%;
    }
    .publicate-form-section{
        width: 100%;
    }
}

    </style>
</head>
<body class="publicate-body">


    <main class="publicate-main">
        <!-- Aquí va el contenido específico de cada vista -->
        @yield('content')
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');
    const uploadLink = dropZone.querySelector('.publicate-upload-link');

    // Prevenir el comportamiento por defecto del navegador
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Funciones de highlight
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Manejar el drop
    dropZone.addEventListener('drop', handleDrop, false);
    
    // Manejar click en el link
    uploadLink.addEventListener('click', () => fileInput.click());
    
    // Manejar selección de archivos
    fileInput.addEventListener('change', handleFiles);

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('drag-over');
    }

    function unhighlight(e) {
        dropZone.classList.remove('drag-over');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
    }

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
            img.className = 'publicate-preview-image';
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'publicate-remove-button';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                preview.remove();
            };
            
            preview.appendChild(img);
            preview.appendChild(removeBtn);
            previewContainer.appendChild(preview);
        };
    }
});
</script>
</body>
</html>
