<!-- Meta Title -->
<div class="form-group template-group">
    <label for="meta_title_{{ $prefix }}" class="text-white">Título Meta</label>
    <input type="text"
        class="form-control"
        id="meta_title_{{ $prefix }}"
        name="meta_title"
        placeholder="Ingrese el título meta"
        required>
</div>

<!-- Meta Description -->
<div class="form-group template-group">
    <label for="meta_description_{{ $prefix }}" class="text-white">Descripción Meta</label>
    <textarea class="form-control custom-textarea"
        id="meta_description_{{ $prefix }}"
        name="meta_description"
        rows="3"
        placeholder="Ingrese la descripción meta"
        required></textarea>
</div>

<!-- Meta Keywords -->
<div class="form-group template-group">
    <label for="meta_keywords_{{ $prefix }}" class="text-white">Palabras Clave Meta</label>
    <input type="text"
        class="form-control"
        id="meta_keywords_{{ $prefix }}"
        name="meta_keywords"
        placeholder="Ingrese las palabras clave separadas por comas">
</div>

<!-- Canonical URL -->
<div class="form-group template-group">
    <label for="canonical_url_{{ $prefix }}" class="text-white">URL Canónica</label>
    <input type="url"
        class="form-control"
        id="canonical_url_{{ $prefix }}"
        name="canonical_url"
        placeholder="Ingrese la URL canónica (opcional)">
</div>

<!-- Meta Robots -->
<div class="form-group template-group">
    <label for="meta_robots_{{ $prefix }}" class="text-white">Meta Robots</label>
    <select class="form-control" id="meta_robots_{{ $prefix }}" name="meta_robots" required>
        <option value="index, follow">Index, Follow</option>
        <option value="noindex, nofollow">No Index, No Follow</option>
        <option value="index, nofollow">Index, No Follow</option>
        <option value="noindex, follow">No Index, Follow</option>
    </select>
</div>

<!-- Heading H1 -->
<div class="form-group template-group">
    <label for="heading_h1_{{ $prefix }}" class="text-white">Encabezado H1</label>
    <input type="text"
        class="form-control"
        id="heading_h1_{{ $prefix }}"
        name="heading_h1"
        placeholder="Ingrese el encabezado H1">
</div>

<!-- Heading H2 -->
<div class="form-group template-group">
    <label for="heading_h2_{{ $prefix }}" class="text-white">Encabezado H2</label>
    <input type="text"
        class="form-control"
        id="heading_h2_{{ $prefix }}"
        name="heading_h2"
        placeholder="Ingrese el encabezado H2">
</div>

<!-- Additional Text -->
<div class="form-group template-group">
    <label for="additional_text_{{ $prefix }}" class="text-white">Texto Adicional</label>
    <textarea class="form-control"
        id="additional_text_{{ $prefix }}"
        name="additional_text"
        rows="4"
        placeholder="Ingrese texto adicional"></textarea>
</div>

<div class="form-group text-right">
    <button type="submit" class="btn custom-button">Guardar Cambios</button>
    <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
</div>