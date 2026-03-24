{{-- Dropdown link — Design-system: consistent hover/focus states --}}
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 hover:bg-indigo-50/70 hover:text-indigo-600 focus:outline-none focus:bg-indigo-50/70 focus:text-indigo-600 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>
