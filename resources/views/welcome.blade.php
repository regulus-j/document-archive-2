<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTrack - Document Tracking & Archiving System</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/app.css')
</head>

<body class="font-sans bg-white text-slate-900">
    <!-- Navigation -->
    <nav class="bg-white border-b border-blue-100">
        <div class="container mx-auto px-6 py-2 flex justify-between items-center">
            <a href="#" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" alt="Document Tracking & Archiving" class="h-12 w-auto">
                <span class="text-xl font-semibold text-blue-900">DocTrack</span>
            </a>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-blue-700 hover:text-blue-800 transition-colors font-medium">Login</a>
                <a href="{{ route('register') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm hover:shadow transition-all duration-200 font-medium">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-blue-50 py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="w-full md:w-1/2 mb-10 md:mb-0 pr-0 md:pr-12">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 text-blue-900 leading-tight">Document Tracking & Archiving Made Easy</h1>
                    <p class="text-lg text-blue-700 mb-8 max-w-lg">
                        Structured solution designed to monitor, manage, and securely
                        store documents throughout their lifecycle, ensuring easy retrieval and secure access.
                    </p>
                    <a href="{{ route('dashboard') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg font-medium">
                        Get Started For Free
                    </a>
                </div>
                <div class="w-full md:w-1/2">
                    <div class="rounded-lg shadow-xl overflow-hidden">
                        <img src="{{ asset('images/DocTrack.png') }}" alt="Document Tracking & Archiving" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- The DocArchive Difference -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-blue-900">The DocTrack Difference</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-blue-800">Easy Upload</h3>
                    <p class="text-blue-700">Upload documents from your device or scan physical documents directly into the system.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-blue-800">Track Documents</h3>
                    <p class="text-blue-700">Monitor document locations, updates, approvals, and access history in real-time.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-blue-800">Quick Search</h3>
                    <p class="text-blue-700">Find documents instantly using keywords, tags, or by scanning physical documents.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="text-center">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-blue-800">Secure Storage</h3>
                    <p class="text-blue-700">Protect sensitive information with role-based access controls and secure archiving.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-10 bg-gray-100 border-y border-gray-200">
        <div class="container mx-auto px-6">
          
        </div>
    </section>

    <!-- Elevate Your Document Management -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-4 text-blue-900">Elevate Your Document Management</h2>
            <p class="text-center text-blue-700 max-w-2xl mx-auto mb-16">Our comprehensive document management solution helps organizations streamline workflows, enhance security, and improve collaboration.</p>
            
            <!-- Document Management Solutions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Solution 1: Document Tracking -->
                <div class="flex flex-col h-full">
                    <div class="bg-blue-800 p-8 text-white">
                        <h3 class="text-2xl font-semibold mb-3">Document Tracking</h3>
                        <p class="mb-6">Track document locations, updates, approvals, rejections, recording who accessed the document, when, and for how long.</p>
                        <a href="#" class="inline-block border border-white text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Learn More</a>
                    </div>
                    <div class="flex-grow">
                        <img src="{{ asset('images/DocuTrack.png') }}" alt="Document Tracking" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <!-- Solution 2: Document Archiving -->
                <div class="flex flex-col h-full">
                    <div class="flex-grow">
                        <img src="{{ asset('images/access.png') }}" alt="Document Archiving" class="w-full h-full object-cover">
                    </div>
                    <div class="bg-blue-700 p-8 text-white">
                        <h3 class="text-2xl font-semibold mb-3">Document Archiving</h3>
                        <p class="mb-6">Store documents securely to safeguard sensitive information, implementing structured roles and authorized access.</p>
                        <a href="#" class="inline-block border border-white text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">Learn More</a>
                    </div>
                </div>
                
                <!-- Solution 3: Document Upload -->
                <div class="flex flex-col h-full">
                    <div class="flex-grow">
                        <img src="{{ asset('images/upload.png') }}" alt="Document Upload" class="w-full h-full object-cover">
                    </div>
                    <div class="bg-blue-600 p-8 text-white">
                        <h3 class="text-2xl font-semibold mb-3">Document Upload</h3>
                        <p class="mb-6">Select files from your computer or device that you want to upload, or scan your physical documents directly.</p>
                        <a href="#" class="inline-block border border-white text-white px-4 py-2 rounded-md hover:bg-blue-500 transition-colors">Learn More</a>
                    </div>
                </div>
                
                <!-- Solution 4: Document Search -->
                <div class="flex flex-col h-full">
                    <div class="bg-blue-500 p-8 text-white">
                        <h3 class="text-2xl font-semibold mb-3">Document Search</h3>
                        <p class="mb-6">Enter specific keywords or tags to search documents, or scan your physical documents to find what you need quickly.</p>
                        <a href="#" class="inline-block border border-white text-white px-4 py-2 rounded-md hover:bg-blue-400 transition-colors">Learn More</a>
                    </div>
                    <div class="flex-grow">
                        <img src="{{ asset('images/DocuSearch.png') }}" alt="Document Search" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-blue-50" x-data="{ billingCycle: 'yearly', currentIndex: 0 }">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-blue-900">Plan & Pricing</h2>
                <p class="text-lg text-blue-700 max-w-2xl mx-auto mb-8">
                    Choose the plan that fits your needs.
                </p>
                
                <!-- Toggle Buttons -->
                <div class="inline-flex border-2 border-blue-500 rounded-md overflow-hidden">
                    <button 
                        class="py-2 px-6 focus:outline-none text-base font-medium" 
                        :class="{ 'bg-blue-600 text-white': billingCycle === 'monthly', 'text-blue-700': billingCycle !== 'monthly' }" 
                        @click="billingCycle = 'monthly'">
                        Monthly
                    </button>
                    <button 
                        class="py-2 px-6 focus:outline-none text-base font-medium" 
                        :class="{ 'bg-blue-600 text-white': billingCycle === 'yearly', 'text-blue-700': billingCycle !== 'yearly' }" 
                        @click="billingCycle = 'yearly'">
                        Yearly
                    </button>
                </div>
            </div>

            @if(isset($plans) && $plans->count() > 0)
            <!-- Pricing Cards Carousel -->
            <div class="relative max-w-6xl mx-auto">
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($plans as $index => $planItem)
                    <!-- {{ $planItem->plan_name }} Plan -->
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                        <div class="p-8 border-b border-gray-100 {{ $index === 0 ? 'relative' : '' }}">
                            @if($index === 0)
                            <span class="bg-blue-600 text-white px-3 py-1 text-xs absolute right-0 top-0 rounded-bl font-semibold">Popular</span>
                            @endif
                            <h3 class="text-sm font-medium text-blue-500 uppercase tracking-wider mb-1">{{ $planItem->plan_name }}</h3>
                            <div class="flex items-end">
                                <span class="text-4xl font-bold text-blue-900">
                                    ₱ <span x-text="billingCycle === 'yearly' ? '{{ number_format($planItem->price * 10, 0) }}' : '{{ number_format($planItem->price, 0) }}'"></span>
                                </span>
                                <span class="text-lg ml-1 text-blue-600 mb-1" x-text="billingCycle === 'monthly' ? '/month' : '/year'"></span>
                            </div>
                            <p class="text-blue-700 mt-2">{{ $planItem->description ?? 'Best for your needs' }}</p>
                        </div>
                        <div class="p-8">
                            <ul class="space-y-4">
                                @forelse($planItem->features as $feature)
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-blue-700">{{ $feature->name }}</span>
                                </li>
                                @empty
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-blue-700">All essential features included</span>
                                </li>
                                @endforelse
                            </ul>
                            <a href="{{ route('plans.register', $planItem->id) }}" class="mt-8 block w-full bg-blue-600 text-white text-center py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                                Start Now
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-blue-700 text-lg">No plans available at the moment. Please check back later.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-blue-800 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to streamline your document management?</h2>
            <p class="text-blue-100 mb-8 max-w-2xl mx-auto">Join thousands of organizations that trust DocTrack for their document tracking and archiving needs.</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-800 px-8 py-3 rounded-md font-medium hover:bg-blue-50 transition-colors shadow-lg">
                Get Started For Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="h-10 w-10 bg-white rounded-md flex items-center justify-center">
                            <span class="text-blue-900 font-bold text-xl">D</span>
                        </div>
                        <span class="text-xl font-semibold">DocTrack</span>
                    </div>
                    <p class="text-blue-200 leading-relaxed">
                        A document tracking & archiving system is software that securely manages document lifecycle from
                        uploading to archival. Tracks document locations, updates, approvals, rejections, and recording
                        who accessed the document.
                    </p>
                </div>
                <div>
                    <h5 class="text-xl font-medium mb-4">Products</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Solutions</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Enterprise</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xl font-medium mb-4">Support</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Terms & Service</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xl font-medium mb-4">Follow Us</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Twitter</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">LinkedIn</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Instagram</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition-colors">Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 mt-8 border-t border-blue-800 text-center text-blue-300">
                <p>&copy; {{ date('Y') }} DocTrack. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>

