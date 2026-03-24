@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'ds-alert-success']) }}>
        {{ $status }}
    </div>
@endif
