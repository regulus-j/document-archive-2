@extends('layouts.app')

@section('content')
    <div class="bg-gradient-to-b from-indigo-50 to-white min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-indigo-100">
                <div class="p-6 border-b border-indigo-200">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-slate-800">Subscription Plans</h1>
                        <a href="{{ route('plans.create') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Plan
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    No</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Plan Name</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Description</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Price</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Billing Cycle</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Features</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Status</th>
                                <th
                                    class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach ($plans as $plan)
                                <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                {{ substr($plan->plan_name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900">{{ $plan->plan_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $plan->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                        P{{ number_format($plan->price / 100, 2) }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $plan->billing_cycle }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            @if($plan->feature_1)
                                                <span
                                                    class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">Feature
                                                    1</span>
                                            @endif
                                            @if($plan->feature_2)
                                                <span
                                                    class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">Feature
                                                    2</span>
                                            @endif
                                            @if($plan->feature_3)
                                                <span
                                                    class="px-2 py-1 text-xs font-medium bg-cyan-100 text-cyan-800 rounded-full">Feature
                                                    3</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($plan->is_active)
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-rose-100 text-rose-800 rounded-full">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('plans.show', $plan->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-1.5 rounded-md hover:bg-indigo-100 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('plans.edit', $plan->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-1.5 rounded-md hover:bg-indigo-100 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('plans.destroy', $plan->id) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-rose-600 hover:text-rose-900 bg-rose-50 p-1.5 rounded-md hover:bg-rose-100 transition-colors"
                                                    onclick="return confirm('Are you sure you want to delete this plan?')">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($plans->hasPages())
                <div class="mt-4 px-6 pb-4">
                    {{ $plans->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection