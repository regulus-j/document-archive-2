{{-- Secondary button — Design-system: slate borders, indigo focus ring --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'ds-btn ds-btn-secondary']) }}>
    {{ $slot }}
</button>
