{{-- Text input — Design-system: slate border, indigo-500 focus ring --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ds-input']) }}>
