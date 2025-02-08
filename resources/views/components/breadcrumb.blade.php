<style>
.breadcrumb-container {
    margin: 0.5rem 0.5rem 1.5rem 1.5rem;
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    list-style: none;
    align-items: center;
}

.breadcrumb-item {
    display: inline-flex;
    align-items: center;
    color: #6c757d;
    font-size: 16px;
}

/* Sobrescribir el estilo de Bootstrap para el separador */
.breadcrumb-item + .breadcrumb-item::before {
    content: "/" !important; /* Forzar nuestro separador */
    color: #777;
    padding: 0 0.5rem;
    float: none;
}

/* Remover el padding por defecto de Bootstrap */
.breadcrumb-item + .breadcrumb-item {
    padding-left: 0;
}

.breadcrumb-link {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.2s ease-in-out;
}

.breadcrumb-link:hover {
    color: #e00037;
}

.breadcrumb-current {
    color: #333;
    font-weight: 500;
}
</style>

@if(isset($breadcrumb) && count($breadcrumb) > 0)
    <nav class="breadcrumb-container" aria-label="Breadcrumb">
        <ol class="breadcrumb-list">
            @foreach($breadcrumb as $index => $item)
                <li class="breadcrumb-item">
                    @if($item['url'])
                        <a href="{{ $item['url'] }}" class="breadcrumb-link">
                            {{ ltrim($item['text'], '/') }}
                        </a>
                    @else
                        <span class="breadcrumb-current">{{ ltrim($item['text'], '/') }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif