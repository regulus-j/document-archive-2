{{-- Text input — Design-system: slate border, indigo-500 focus ring --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-lg shadow-sm transition-colors duration-150 disabled:bg-slate-50 disabled:text-slate-500']) }}>
