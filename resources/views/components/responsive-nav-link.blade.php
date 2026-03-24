{{-- Responsive nav link — Design-system: indigo-600 active, slate neutrals --}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-500 text-start text-sm font-medium text-indigo-700 bg-indigo-50/70 rounded-r-lg focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 hover:border-slate-300 rounded-r-lg focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
