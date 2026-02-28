{{-- Input label — Design-system: slate-700 text, medium weight --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-slate-700']) }}>
    {{ $value ?? $slot }}
</label>
