{{-- Primary button — Design-system: indigo-600 primary --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ds-btn ds-btn-primary']) }}>
    {{ $slot }}
</button>
