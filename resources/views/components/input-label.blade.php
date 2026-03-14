{{-- Input label — Design-system: slate-700 text, medium weight --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'ds-label']) }}>
    {{ $value ?? $slot }}
</label>
