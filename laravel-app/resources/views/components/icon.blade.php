@php
    $iconPath = resource_path(
        "icons/tabler/{$variant}/{$name}.svg"
    );
@endphp

@if (file_exists($iconPath))
    {!! file_get_contents($iconPath) !!}
@endif