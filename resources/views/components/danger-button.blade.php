{{-- Danger button — Design-system: red-600 destructive actions --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ds-btn ds-btn-danger']) }}>
    {{ $slot }}
</button>
