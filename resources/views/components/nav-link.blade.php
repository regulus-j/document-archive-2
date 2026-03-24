{{-- Nav link — Design-system: indigo-600 active, slate neutrals --}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50/70 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
