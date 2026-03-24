<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Tracking and Archiving System - User Manual</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div x-data="{ activeTab: 'introduction' }" class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-indigo-700 to-indigo-800 text-white py-8 px-4 shadow-lg">
            <div class="container mx-auto max-w-6xl">
                <h1 class="text-4xl font-bold tracking-tight mb-2 text-center">Document Tracking and Archiving System</h1>
                <p class="text-indigo-100 text-center text-lg">User Manual - Version 1.0</p>
            </div>
        </header>
        <!-- Navigation -->
        <div class="sticky top-0 bg-white shadow-md z-10 border-b border-slate-200">
            <div class="container mx-auto max-w-6xl px-4 py-2 overflow-x-auto">
                <nav class="flex space-x-1 md:space-x-2">
                    <button 
                        @click="activeTab = 'introduction'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'introduction', 'hover:bg-slate-100': activeTab !== 'introduction' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Introduction
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'getting-started'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'getting-started', 'hover:bg-slate-100': activeTab !== 'getting-started' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Getting Started
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'document-management'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'document-management', 'hover:bg-slate-100': activeTab !== 'document-management' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Documents
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'tracking'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'tracking', 'hover:bg-slate-100': activeTab !== 'tracking' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tracking
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'archiving'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'archiving', 'hover:bg-slate-100': activeTab !== 'archiving' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Archiving
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'users'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'users', 'hover:bg-slate-100': activeTab !== 'users' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users & Roles
                        </span>
                    </button>
                    <button 
                        @click="activeTab = 'troubleshooting'" 
                        :class="{ 'bg-indigo-100 text-indigo-700 border-indigo-700': activeTab === 'troubleshooting', 'hover:bg-slate-100': activeTab !== 'troubleshooting' }"
                        class="px-3 py-2 text-sm md:text-base font-medium rounded-md transition-colors duration-200 border-b-2 border-transparent"
                    >
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Help
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <main class="container mx-auto max-w-6xl px-4 py-8">
            <!-- Introduction -->
            <div x-show="activeTab === 'introduction'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Introduction
                    </h2>
                    <p class="text-indigo-100">Overview of the Document Tracking and Archiving System</p>
                </div>
                <div class="p-6 space-y-6">
                    <p class="text-slate-700 leading-relaxed">
                        Welcome to the Document Tracking and Archiving System. This comprehensive solution 
                        allows your organization to efficiently manage, track, and archive important documents 
                        throughout their lifecycle.
                    </p>
                    
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
                        <p class="text-indigo-700">
                            This system is built with Laravel, providing a robust and secure foundation for document management.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Key Features</h3>
                        <ul class="list-disc pl-6 space-y-2 text-slate-700">
                            <li>Centralized document repository with advanced search capabilities</li>
                            <li>Version control and document history tracking</li>
                            <li>Automated workflows for document approval and processing</li>
                            <li>Secure archiving with retention policy enforcement</li>
                            <li>Role-based access control and permissions</li>
                            <li>Audit trails for compliance and accountability</li>
                            <li>Integration with existing business systems</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">System Requirements</h3>
                        <p class="text-slate-700 mb-3">
                            The system is web-based and accessible through all modern browsers. For optimal 
                            performance, we recommend:
                        </p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                <h4 class="font-medium text-slate-800 mb-2">Browser Requirements</h4>
                                <ul class="list-disc pl-6 space-y-1 text-slate-600">
                                    <li>Chrome 90+</li>
                                    <li>Firefox 88+</li>
                                    <li>Safari 14+</li>
                                    <li>Edge 90+</li>
                                </ul>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                <h4 class="font-medium text-slate-800 mb-2">Other Requirements</h4>
                                <ul class="list-disc pl-6 space-y-1 text-slate-600">
                                    <li>Minimum screen resolution of 1280 x 720</li>
                                    <li>Stable internet connection</li>
                                    <li>PDF viewer for document previews</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started -->
            <div x-show="activeTab === 'getting-started'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Getting Started
                    </h2>
                    <p class="text-indigo-100">Learn how to access and navigate the system</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Accessing the System</h3>
                            <p class="text-slate-700 mb-4">
                                Access the Document Tracking and Archiving System through your organization's 
                                dedicated URL. Contact your system administrator if you don't have this information.
                            </p>
                            
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
                                <p class="text-slate-700 font-medium">Default URL Format:</p>
                                <code class="block bg-slate-800 text-green-400 p-2 rounded mt-2 text-sm">
                                    https://docs.yourcompany.com
                                </code>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Logging In</h3>
                            <ol class="list-decimal pl-6 space-y-2 text-slate-700">
                                <li>Navigate to your organization's system URL</li>
                                <li>Enter your username and password</li>
                                <li>Click the "Login" button</li>
                                <li>For first-time users, you'll be prompted to change your password</li>
                            </ol>
                        </div>
                        
                        <div>
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg overflow-hidden mb-6">
                                <div class="bg-indigo-100 px-4 py-2 border-b border-indigo-200">
                                    <h4 class="font-medium text-indigo-800">Login Screen</h4>
                                </div>
                                <div class="p-4 bg-white">
                                    <div class="border border-slate-300 rounded-lg p-6 max-w-md mx-auto">
                                        <div class="text-center mb-6">
                                            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-slate-800">Document System</h3>
                                            <p class="text-slate-500 text-sm">Sign in to your account</p>
                                        </div>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                                <div class="mt-1">
                                                    <input type="email" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="you@example.com">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                                                <div class="mt-1">
                                                    <input type="password" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="••••••••">
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Sign in
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Password Requirements</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>
                                                Passwords must be at least 8 characters long and include uppercase letters, 
                                                lowercase letters, numbers, and special characters.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Dashboard Overview</h3>
                        <p class="text-slate-700 mb-4">
                            After logging in, you'll be directed to the main dashboard, which provides:
                        </p>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-slate-800 mb-1">Recent Documents</h4>
                                <p class="text-slate-600 text-sm">Quick access to recently viewed or edited documents</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-slate-800 mb-1">Notifications</h4>
                                <p class="text-slate-600 text-sm">Pending approvals, tasks, and system alerts</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-slate-800 mb-1">Quick Search</h4>
                                <p class="text-slate-600 text-sm">Search functionality for rapid document retrieval</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Navigation</h3>
                        <p class="text-slate-700 mb-4">
                            The main navigation menu is located on the left side of the screen and provides 
                            access to all system modules. The top navigation bar contains user settings, 
                            notifications, and help resources.
                        </p>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-lg font-semibold text-slate-800">Main Navigation</div>
                                <div class="text-sm text-slate-500">Press <kbd class="px-2 py-1 bg-slate-100 border border-slate-300 rounded text-xs">Alt+M</kbd> to toggle menu</div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center p-2 bg-indigo-100 text-indigo-700 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </div>
                                <div class="flex items-center p-2 hover:bg-slate-100 rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Documents
                                </div>
                                <div class="flex items-center p-2 hover:bg-slate-100 rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Recent Activity
                                </div>
                                <div class="flex items-center p-2 hover:bg-slate-100 rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0  stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Archives
                                </div>
                                <div class="flex items-center p-2 hover:bg-slate-100 rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Users & Permissions
                                </div>
                                <div class="flex items-center p-2 hover:bg-slate-100 rounded transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Settings
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Management -->
            <div x-show="activeTab === 'document-management'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Document Management
                    </h2>
                    <p class="text-indigo-100">Learn how to upload, organize, and search for documents</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Uploading Documents</h3>
                            <ol class="list-decimal pl-6 space-y-2 text-slate-700">
                                <li>Navigate to the "Documents" section from the main menu</li>
                                <li>Click the "Upload" button in the top-right corner</li>
                                <li>Select files from your computer or drag and drop them into the upload area</li>
                                <li>Fill in the required metadata fields (title, document type, department, etc.)</li>
                                <li>Assign appropriate tags for easier searching</li>
                                <li>Click "Submit" to complete the upload</li>
                            </ol>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                <p class="text-indigo-700 text-sm">
                                    <strong>Laravel Tip:</strong> The system uses Laravel's file storage system with secure validation and sanitization.
                                    Maximum upload size is configured in your php.ini file.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">Upload Interface</h4>
                                </div>
                                <div class="p-4">
                                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-slate-600">
                                            Drag and drop files here, or
                                            <span class="text-indigo-600 font-medium">browse</span>
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Supported formats: PDF, DOCX, XLSX, JPG, PNG (Max 100MB)
                                        </p>
                                    </div>
                                    
                                    <div class="mt-4 space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Document Title*</label>
                                            <input type="text" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Document Type*</label>
                                            <select class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option>Select type...</option>
                                                <option>Contract</option>
                                                <option>Report</option>
                                                <option>Invoice</option>
                                                <option>Policy</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Tags</label>
                                            <input type="text" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Separate with commas">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Supported File Types</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 mr-3 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-slate-900">PDF</span>
                                    <span class="block text-xs text-slate-500">.pdf</span>
                                </div>
                            </div>
                            <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 mr-3 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-slate-900">Word</span>
                                    <span class="block text-xs text-slate-500">.doc, .docx</span>
                                </div>
                            </div>
                            <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 mr-3 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-slate-900">Excel</span>
                                    <span class="block text-xs text-slate-500">.xls, .xlsx</span>
                                </div>
                            </div>
                            <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 mr-3 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-slate-900">Images</span>
                                    <span class="block text-xs text-slate-500">.jpg, .png, .tiff</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Searching for Documents</h3>
                        <p class="text-slate-700 mb-4">
                            The system offers multiple search options:
                        </p>
                        
                        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm mb-6">
                            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                <h4 class="font-medium text-slate-800">Search Interface</h4>
                            </div>
                            <div class="p-4">
                                <div class="flex gap-2">
                                    <div class="flex-grow">
                                        <input type="text" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search documents...">
                                    </div>
                                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Search
                                    </button>
                                </div>
                                
                                <div class="mt-3 flex items-center text-sm">
                                    <button class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                        Advanced Search
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-slate-800 mb-2">Search Types</h4>
                                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                    <li><strong>Quick Search:</strong> Use the search bar at the top of any page</li>
                                    <li><strong>Advanced Search:</strong> Filter by metadata, date ranges, file types, and more</li>
                                    <li><strong>Full-Text Search:</strong> Search within document contents (for supported file types)</li>
                                    <li><strong>Saved Searches:</strong> Save frequently used search criteria for quick access</li>
                                </ul>
                            </div>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
                                <h4 class="font-medium text-indigo-800 mb-2">Laravel Search Implementation</h4>
                                <p class="text-indigo-700 text-sm">
                                    The system uses Laravel Scout with a database driver for efficient document searching. 
                                    Full-text search is implemented using Laravel's built-in database full-text indexing capabilities.
                                </p>
                                <p class="text-indigo-700 text-sm mt-2">
                                    <code class="bg-indigo-100 px-1 py-0.5 rounded">php artisan scout:import "App\Models\Document"</code> is used to index documents for searching.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking -->
            <div x-show="activeTab === 'tracking'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Document Tracking
                    </h2>
                    <p class="text-indigo-100">Learn about version control and audit trails</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Version Control</h3>
                            <p class="text-slate-700 mb-4">
                                The system automatically maintains version history for all documents:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                <li>Each time a document is modified, a new version is created</li>
                                <li>Previous versions remain accessible and can be restored if needed</li>
                                <li>Version comparison tools allow you to see changes between versions</li>
                                <li>Version notes can be added to describe changes made</li>
                            </ul>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                <p class="text-indigo-700 text-sm">
                                    <strong>Laravel Implementation:</strong> Document versioning is implemented using Laravel's 
                                    model events and a related versions table to track all changes.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">Version History Example</h4>
                                </div>
                                <div class="p-4">
                                    <div class="text-sm font-medium text-slate-900 mb-2">Document: Annual Report 2023.docx</div>
                                    <div class="space-y-3">
                                        <div class="flex items-start p-3 border border-slate-200 rounded-lg bg-indigo-50">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-medium">v3</div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="flex items-center justify-between">
                                                    <div class="font-medium text-slate-900">Current Version</div>
                                                    <div class="text-xs text-slate-500">May 15, 2023 - 14:32</div>
                                                </div>
                                                <div class="text-sm text-slate-600 mt-1">Updated financial projections and executive summary</div>
                                                <div class="text-xs text-slate-500 mt-1">Modified by: John Smith</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start p-3 border border-slate-200 rounded-lg">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-medium">v2</div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="flex items-center justify-between">
                                                    <div class="font-medium text-slate-900">Previous Version</div>
                                                    <div class="text-xs text-slate-500">May 10, 2023 - 09:15</div>
                                                </div>
                                                <div class="text-sm text-slate-600 mt-1">Added Q4 results and updated charts</div>
                                                <div class="text-xs text-slate-500 mt-1">Modified by: Sarah Johnson</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start p-3 border border-slate-200 rounded-lg">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-medium">v1</div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="flex items-center justify-between">
                                                    <div class="font-medium text-slate-900">Initial Version</div>
                                                    <div class="text-xs text-slate-500">May 5, 2023 - 11:42</div>
                                                </div>
                                                <div class="text-sm text-slate-600 mt-1">Initial draft with Q1-Q3 data</div>
                                                <div class="text-xs text-slate-500 mt-1">Created by: David Wilson</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Checking Out Documents</h3>
                        <p class="text-slate-700 mb-4">
                            To prevent conflicting edits, documents can be checked out:
                        </p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <ol class="list-decimal pl-6 space-y-2 text-slate-700">
                                <li>Navigate to the document you wish to edit</li>
                                <li>Click the "Check Out" button</li>
                                <li>Make your changes and upload the new version</li>
                                <li>Click "Check In" to release the lock and make the document available to others</li>
                            </ol>
                            
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Important Note</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>
                                                Documents that are checked out by other users cannot be modified until they are checked back in.
                                                Administrators can override check-outs if necessary.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Audit Trails</h3>
                        <p class="text-slate-700 mb-4">
                            The system maintains comprehensive audit trails for all document activities:
                        </p>
                        
                        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm mb-6">
                            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                <h4 class="font-medium text-slate-800">Audit Log Example</h4>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Timestamp</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">User</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200 text-sm">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">2023-05-15 14:32:45</td>
                                            <td class="px-6 py-4 whitespace-nowrap">John Smith</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Document Updated
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">Updated "Annual Report 2023.docx" (Version 3)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">2023-05-15 14:30:12</td>
                                            <td class="px-6 py-4 whitespace-nowrap">John Smith</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    Document Checkout
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">Checked out "Annual Report 2023.docx"</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">2023-05-14 09:45:33</td>
                                            <td class="px-6 py-4 whitespace-nowrap">Maria Garcia</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Permission Change
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">Changed access permissions for "Q1 Financial Report.pdf"</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">2023-05-12 16:22:07</td>
                                            <td class="px-6 py-4 whitespace-nowrap">David Wilson</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Document Deleted
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">Deleted "Draft Marketing Plan.docx"</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-slate-800 mb-2">Tracked Activities</h4>
                                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                    <li>Document creation, modification, and deletion</li>
                                    <li>Document access and viewing</li>
                                    <li>Permission changes</li>
                                    <li>Status changes and workflow transitions</li>
                                    <li>User actions (login, logout, failed login attempts)</li>
                                </ul>
                            </div>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
                                <h4 class="font-medium text-indigo-800 mb-2">Laravel Audit Implementation</h4>
                                <p class="text-indigo-700 text-sm">
                                    The system uses Laravel's model events and observers to track all document activities.
                                    Each action is recorded in a dedicated audit_logs table with timestamps, user information,
                                    and detailed action data.
                                </p>
                                <p class="text-indigo-700 text-sm mt-2">
                                    Administrators can export audit logs to CSV or PDF for compliance reporting.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archiving -->
            <div x-show="activeTab === 'archiving'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        Document Archiving
                    </h2>
                    <p class="text-indigo-100">Learn about archiving documents and retention policies</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Archiving Process</h3>
                            <p class="text-slate-700 mb-4">
                                Documents can be archived manually or automatically based on predefined rules:
                            </p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                <li><strong>Manual Archiving:</strong> Select documents and click the "Archive" button</li>
                                <li><strong>Automatic Archiving:</strong> Documents are archived based on age, status, or other criteria</li>
                                <li>Archived documents are moved to a separate storage area but remain searchable</li>
                                <li>Archived documents are typically read-only unless explicitly restored</li>
                            </ul>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                <p class="text-indigo-700 text-sm">
                                    <strong>Laravel Implementation:</strong> Archiving is implemented using Laravel's soft delete feature
                                    combined with a document status field. Laravel scheduled tasks handle automatic archiving based on
                                    configured retention policies.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">Archive Interface</h4>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="text-sm font-medium text-slate-900">Documents Ready for Archiving</h5>
                                        <button class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Archive Selected
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded mr-3">
                                            <div class="flex-grow">
                                                <div class="text-sm font-medium text-slate-900">Q1 2022 Financial Report.pdf</div>
                                                <div class="text-xs text-slate-500">Last modified: Jan 15, 2022 (485 days ago)</div>
                                            </div>
                                            <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                Retention: 1 year
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded mr-3">
                                            <div class="flex-grow">
                                                <div class="text-sm font-medium text-slate-900">Employee Handbook v2.docx</div>
                                                <div class="text-xs text-slate-500">Last modified: Mar 10, 2022 (430 days ago)</div>
                                            </div>
                                            <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                Retention: 1 year
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded mr-3">
                                            <div class="flex-grow">
                                                <div class="text-sm font-medium text-slate-900">Marketing Campaign Results 2022.xlsx</div>
                                                <div class="text-xs text-slate-500">Last modified: Dec 20, 2022 (145 days ago)</div>
                                            </div>
                                            <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                Retention: 6 months
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Retention Policies</h3>
                        <p class="text-slate-700 mb-4">
                            Retention policies determine how long documents are kept before being archived or deleted:
                        </p>
                        
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-slate-200 border border-slate-200 rounded-lg">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Document Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Retention Period</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Archive Action</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200 text-sm">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">Financial Reports</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">7 years</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">Archive, then delete</td>
                                        <td class="px-6 py-4 text-slate-700">Required for tax purposes</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">HR Documents</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">5 years after termination</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">Archive indefinitely</td>
                                        <td class="px-6 py-4 text-slate-700">Legal requirement</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">Marketing Materials</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">1 year</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">Archive for 3 years, then delete</td>
                                        <td class="px-6 py-4 text-slate-700">Keep for reference</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">Project Documentation</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">2 years after completion</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700">Archive for 5 years, then delete</td>
                                        <td class="px-6 py-4 text-slate-700">Knowledge retention</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Compliance Note</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            Always consult your organization's compliance officer before modifying retention policies
                                            or deleting archived documents, as this may have legal implications.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Retrieving Archived Documents</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-slate-700 mb-4">
                                    To access or restore archived documents:
                                </p>
                                <ol class="list-decimal pl-6 space-y-2 text-slate-700">
                                    <li>Use the "Archive" tab in the search interface</li>
                                    <li>Locate the document using search filters</li>
                                    <li>Click "View" to access the document in read-only mode</li>
                                    <li>Click "Restore" to move the document back to active status (requires appropriate permissions)</li>
                                </ol>
                                
                                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                    <p class="text-indigo-700 text-sm">
                                        <strong>Laravel Tip:</strong> The system uses Laravel's query scopes to easily filter between active and 
                                        archived documents. For example: <code class="bg-indigo-100 px-1 py-0.5 rounded">Document::archived()->get()</code>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">Archive Search</h4>
                                </div>
                                <div class="p-4">
                                    <div class="flex gap-2 mb-4">
                                        <div class="flex-grow">
                                            <input type="text" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search archived documents...">
                                        </div>
                                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Search
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">2021 Annual Budget.xlsx</div>
                                                <div class="text-xs text-slate-500">Archived: Jan 15, 2022 | Type: Financial</div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded hover:bg-slate-200">
                                                    View
                                                </button>
                                                <button class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded hover:bg-indigo-200">
                                                    Restore
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">Old Product Catalog 2020.pdf</div>
                                                <div class="text-xs text-slate-500">Archived: Mar 22, 2021 | Type: Marketing</div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button class="px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded hover:bg-slate-200">
                                                    View
                                                </button>
                                                <button class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded hover:bg-indigo-200">
                                                    Restore
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users and Permissions -->
            <div x-show="activeTab === 'users'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users and Permissions
                    </h2>
                    <p class="text-indigo-100">Learn about user roles and access control</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">User Roles</h3>
                            <p class="text-slate-700 mb-4">
                                The system uses role-based access control with the following default roles:
                            </p>
                            <div class="space-y-3">
                                <div class="p-3 border border-slate-200 rounded-lg bg-slate-50">
                                    <h4 class="font-medium text-slate-900">System Administrator</h4>
                                    <p class="text-sm text-slate-700 mt-1">Full access to all system functions and settings</p>
                                </div>
                                <div class="p-3 border border-slate-200 rounded-lg">
                                    <h4 class="font-medium text-slate-900">Document Manager</h4>
                                    <p class="text-sm text-slate-700 mt-1">Can create, modify, and delete documents across all departments</p>
                                </div>
                                <div class="p-3 border border-slate-200 rounded-lg">
                                    <h4 class="font-medium text-slate-900">Department Manager</h4>
                                    <p class="text-sm text-slate-700 mt-1">Can manage documents within their assigned department</p>
                                </div>
                                <div class="p-3 border border-slate-200 rounded-lg">
                                    <h4 class="font-medium text-slate-900">Content Creator</h4>
                                    <p class="text-sm text-slate-700 mt-1">Can upload and edit their own documents</p>
                                </div>
                                <div class="p-3 border border-slate-200 rounded-lg">
                                    <h4 class="font-medium text-slate-900">Viewer</h4>
                                    <p class="text-sm text-slate-700 mt-1">Can view documents but cannot make changes</p>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                <p class="text-indigo-700 text-sm">
                                    <strong>Laravel Implementation:</strong> User roles and permissions are implemented using Laravel's 
                                    built-in Gates and Policies, with a roles and permissions database structure.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Permission Levels</h3>
                            <p class="text-slate-700 mb-4">
                                Permissions can be assigned at multiple levels:
                            </p>
                            
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm mb-6">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">Permission Management</h4>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">User</label>
                                            <select class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option>John Smith</option>
                                                <option>Maria Garcia</option>
                                                <option>David Wilson</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                                            <select class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option>Content Creator</option>
                                                <option>Department Manager</option>
                                                <option>Document Manager</option>
                                                <option>System Administrator</option>
                                                <option>Viewer</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                                            <select class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option>All Departments</option>
                                                <option>Finance</option>
                                                <option>Human Resources</option>
                                                <option>Marketing</option>
                                                <option>Operations</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-2">Specific Permissions</label>
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" checked>
                                                    <label class="ml-2 block text-sm text-slate-700">View Documents</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" checked>
                                                    <label class="ml-2 block text-sm text-slate-700">Create Documents</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" checked>
                                                    <label class="ml-2 block text-sm text-slate-700">Edit Documents</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                                    <label class="ml-2 block text-sm text-slate-700">Delete Documents</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                                    <label class="ml-2 block text-sm text-slate-700">Manage Users</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Save Permissions
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-slate-800 mb-2">Permission Hierarchy</h4>
                                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                    <li><strong>System Level:</strong> Access to system modules and functions</li>
                                    <li><strong>Folder Level:</strong> Access to specific document folders</li>
                                    <li><strong>Document Level:</strong> Access to individual documents</li>
                                    <li><strong>Field Level:</strong> Access to specific metadata fields</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">User Groups</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-slate-700 mb-4">
                                    Users can be organized into groups for easier permission management:
                                </p>
                                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                    <li>Groups can be based on departments, teams, or projects</li>
                                    <li>Permissions can be assigned to groups rather than individual users</li>
                                    <li>Users can belong to multiple groups</li>
                                    <li>Group membership can be synchronized with Active Directory or LDAP</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">User Groups</h4>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">Finance Department</div>
                                                    <div class="text-xs text-slate-500">12 members</div>
                                                </div>
                                            </div>
                                            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                Manage
                                            </button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">Marketing Team</div>
                                                    <div class="text-xs text-slate-500">8 members</div>
                                                </div>
                                            </div>
                                            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                Manage
                                            </button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-2 border border-slate-200 rounded-lg hover:bg-slate-50">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">Project Alpha</div>
                                                    <div class="text-xs text-slate-500">5 members</div>
                                                </div>
                                            </div>
                                            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                Manage
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">User Profile Management</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-slate-700 mb-4">
                                    Users can manage their own profiles by:
                                </p>
                                <ol class="list-decimal pl-6 space-y-2 text-slate-700">
                                    <li>Clicking on their username in the top-right corner</li>
                                    <li>Selecting "Profile" from the dropdown menu</li>
                                    <li>Updating personal information, preferences, and notification settings</li>
                                    <li>Changing their password</li>
                                </ol>
                                
                                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                    <p class="text-indigo-700 text-sm">
                                        <strong>Laravel Security:</strong> Password changes are handled securely using Laravel's 
                                        built-in authentication system with proper hashing and validation.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                                    <h4 class="font-medium text-slate-800">User Profile</h4>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center mb-6">
                                        <div class="mr-4">
                                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xl font-bold">
                                                JS
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-medium text-slate-900">John Smith</h5>
                                            <p class="text-sm text-slate-500">john.smith@example.com</p>
                                            <p class="text-xs text-slate-500">Content Creator</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                                            <input type="text" value="John Smith" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                            <input type="email" value="john.smith@example.com" class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                                            <select class="block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option>Marketing</option>
                                                <option>Finance</option>
                                                <option>Human Resources</option>
                                                <option>Operations</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Notification Preferences</label>
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" checked>
                                                    <label class="ml-2 block text-sm text-slate-700">Email notifications for document updates</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded" checked>
                                                    <label class="ml-2 block text-sm text-slate-700">Email notifications for workflow tasks</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                                    <label class="ml-2 block text-sm text-slate-700">System announcements</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div x-show="activeTab === 'troubleshooting'" class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Troubleshooting and Support
                    </h2>
                    <p class="text-indigo-100">Find solutions to common issues and get help</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Common Issues</h3>
                            
                            <div class="space-y-4">
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm">
                                    <h4 class="font-medium text-slate-900 mb-2">Login Problems</h4>
                                    <ul class="list-disc pl-6 space-y-1 text-slate-700 text-sm">
                                        <li>Verify your username and password</li>
                                        <li>Check if Caps Lock is enabled</li>
                                        <li>Clear browser cache and cookies</li>
                                        <li>Contact your administrator if your account is locked</li>
                                    </ul>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm">
                                    <h4 class="font-medium text-slate-900 mb-2">Document Upload Failures</h4>
                                    <ul class="list-disc pl-6 space-y-1 text-slate-700 text-sm">
                                        <li>Ensure the file is not larger than the maximum allowed size (100MB)</li>
                                        <li>Verify the file format is supported</li>
                                        <li>Check your network connection</li>
                                        <li>Try uploading smaller batches if uploading multiple files</li>
                                    </ul>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm">
                                    <h4 class="font-medium text-slate-900 mb-2">Search Not Returning Expected Results</h4>
                                    <ul class="list-disc pl-6 space-y-1 text-slate-700 text-sm">
                                        <li>Check spelling and try alternative keywords</li>
                                        <li>Use wildcards (*) for partial matches</li>
                                        <li>Verify you have access permissions to the documents you're searching for</li>
                                        <li>Try the advanced search with more specific criteria</li>
                                    </ul>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm">
                                    <h4 class="font-medium text-slate-900 mb-2">Performance Issues</h4>
                                    <ul class="list-disc pl-6 space-y-1 text-slate-700 text-sm">
                                        <li>Close unused browser tabs and applications</li>
                                        <li>Clear browser cache</li>
                                        <li>Try a different browser</li>
                                        <li>Check your internet connection speed</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mt-4">
                                <p class="text-indigo-700 text-sm">
                                    <strong>Laravel Debugging:</strong> For developers, Laravel's debug mode can be enabled in the .env file
                                    by setting <code class="bg-indigo-100 px-1 py-0.5 rounded">APP_DEBUG=true</code>. This provides detailed
                                    error information for troubleshooting.
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800 mb-3 border-b border-slate-200 pb-2">Getting Help</h3>
                            <p class="text-slate-700 mb-4">
                                Multiple support options are available:
                            </p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex flex-col items-center text-center">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-slate-900 mb-1">In-App Help</h4>
                                    <p class="text-sm text-slate-600">Click the "Help" icon in the top navigation bar</p>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex flex-col items-center text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-slate-900 mb-1">Knowledge Base</h4>
                                    <p class="text-sm text-slate-600">Access articles and tutorials in the Help Center</p>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex flex-col items-center text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-slate-900 mb-1">IT Support</h4>
                                    <p class="text-sm text-slate-600">Contact your internal IT department</p>
                                </div>
                                
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm flex flex-col items-center text-center">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-medium text-slate-900 mb-1">Vendor Support</h4>
                                    <p class="text-sm text-slate-600">Submit a support ticket through the "Support" link</p>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-slate-800 mb-2">Reporting Issues</h4>
                                <p class="text-slate-700 mb-3">
                                    When reporting issues, please include:
                                </p>
                                <div class="bg-white p-4 border border-slate-200 rounded-lg shadow-sm">
                                    <ul class="list-disc pl-6 space-y-2 text-slate-700">
                                        <li>Detailed description of the problem</li>
                                        <li>Steps to reproduce the issue</li>
                                        <li>Screenshots or error messages</li>
                                        <li>Browser and operating system information</li>
                                        <li>Time when the issue occurred</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mt-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">System Status</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>
                                                Check the system status page at <span class="font-medium">status.documentsystem.com</span> for 
                                                information about ongoing maintenance or service disruptions.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-800 text-slate-300 py-6 px-4 mt-8">
            <div class="container mx-auto max-w-6xl">
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Document System</h3>
                        <p class="text-sm">
                            A comprehensive solution for document tracking and archiving built with Laravel and Tailwind CSS.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white">Help Center</a></li>
                            <li><a href="#" class="hover:text-white">System Status</a></li>
                            <li><a href="#" class="hover:text-white">Contact Support</a></li>
                            <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Contact</h3>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                support@documentsystem.com
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                +1 (555) 123-4567
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-700 mt-6 pt-6 text-sm text-center">
                    &copy; 2023 Document Tracking and Archiving System. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
</x-app-layout>
