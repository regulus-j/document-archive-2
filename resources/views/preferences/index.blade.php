<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Preferences') }}
            </h2>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Barcode Default Settings Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Barcode Overlay Default Settings</h3>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-6">
                        Configure your default barcode position and size. These settings will be automatically applied when you upload documents with barcode overlay.
                        @if($hasCustomDefaults)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                Custom defaults active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                Using system defaults
                            </span>
                        @endif
                    </p>

                    {{-- Quick Presets --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Presets</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($barcodePresets as $key => $preset)
                                <form method="POST" action="{{ route('preferences.barcode.apply-preset') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="preset" value="{{ $key }}">
                                    <button type="submit" 
                                            class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-all text-left group">
                                        <div class="font-medium text-sm text-gray-900 group-hover:text-indigo-700">
                                            {{ $preset['name'] }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $preset['description'] }}
                                        </div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    {{-- Current Settings Form --}}
                    <form method="POST" action="{{ route('preferences.barcode.update') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Position Settings --}}
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Position (% of document)</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="x_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                            X Position (%)
                                        </label>
                                        <input type="number" 
                                               id="x_percent" 
                                               name="x_percent" 
                                               value="{{ old('x_percent', $barcodeDefaults['x_percent']) }}" 
                                               min="0" 
                                               max="100" 
                                               step="0.1" 
                                               required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('x_percent')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="y_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                            Y Position (%)
                                        </label>
                                        <input type="number" 
                                               id="y_percent" 
                                               name="y_percent" 
                                               value="{{ old('y_percent', $barcodeDefaults['y_percent']) }}" 
                                               min="0" 
                                               max="100" 
                                               step="0.1" 
                                               required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('y_percent')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Size Settings --}}
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Size (% of document)</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="width_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                            Width (%)
                                        </label>
                                        <input type="number" 
                                               id="width_percent" 
                                               name="width_percent" 
                                               value="{{ old('width_percent', $barcodeDefaults['width_percent']) }}" 
                                               min="5" 
                                               max="100" 
                                               step="0.1" 
                                               required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('width_percent')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="height_percent" class="block text-sm font-medium text-gray-700 mb-1">
                                            Height (%)
                                        </label>
                                        <input type="number" 
                                               id="height_percent" 
                                               name="height_percent" 
                                               value="{{ old('height_percent', $barcodeDefaults['height_percent']) }}" 
                                               min="2" 
                                               max="50" 
                                               step="0.1" 
                                               required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('height_percent')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Options --}}
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Additional Options</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="show_text" 
                                               name="show_text" 
                                               value="1"
                                               {{ old('show_text', $barcodeDefaults['show_text'] ?? true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="show_text" class="ml-2 block text-sm text-gray-700">
                                            Show tracking number text below barcode
                                        </label>
                                    </div>

                                    <div>
                                        <label for="page" class="block text-sm font-medium text-gray-700 mb-1">
                                            Target Page
                                        </label>
                                        <select id="page" 
                                                name="page" 
                                                class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="1" {{ old('page', $barcodeDefaults['page'] ?? 1) == 1 ? 'selected' : '' }}>First page only</option>
                                            <option value="0" {{ old('page', $barcodeDefaults['page'] ?? 1) == 0 ? 'selected' : '' }}>All pages</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200">
                            @if($hasCustomDefaults)
                                <form method="POST" action="{{ route('preferences.barcode.clear') }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Reset to System Defaults
                                    </button>
                                </form>
                            @else
                                <div></div>
                            @endif

                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Default Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Information Card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="h-6 w-6 text-blue-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 mb-2">How Default Settings Work</h4>
                        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                            <li>Your default settings are automatically loaded when you upload documents with barcode overlay</li>
                            <li>You can still adjust the position for individual documents in the barcode preview modal</li>
                            <li>Use "Save as Default" button in the preview modal to quickly save your preferred position</li>
                            <li>Percentage-based positioning ensures barcodes appear at the same relative position on all document sizes</li>
                            <li>Barcode overlay only works with PDF and image files (JPG, PNG, GIF, WebP, BMP)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
