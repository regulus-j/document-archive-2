{{-- Input label — Design-system: slate-700 text, medium weight --}}
@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'ds-label']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500" aria-hidden="true">*</span>
        <span class="sr-only">{{ __('Required') }}</span>
    @endif
</label>
