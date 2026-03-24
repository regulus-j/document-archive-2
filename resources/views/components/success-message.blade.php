{{-- Success toast — Design-system: emerald accent, consistent rounded-lg --}}
@props(['message'])

@if(session('success'))
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="fixed top-4 right-4 z-50 flex items-center gap-3 p-4
            bg-white border border-emerald-200 rounded-lg shadow-lg"
     role="alert">
    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center
                rounded-full bg-emerald-100 text-emerald-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
    </div>
    <p class="text-sm font-medium text-slate-700">{{ session('success') }}</p>
    <button type="button" @click="show = false"
            class="ml-auto flex-shrink-0 rounded-lg p-1 text-slate-400
                   hover:text-slate-600 hover:bg-slate-100
                   transition-colors duration-150"
            aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif
