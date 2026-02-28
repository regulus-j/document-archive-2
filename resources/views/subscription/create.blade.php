<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-slate-200">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-slate-900">Create New Subscription</h2>
                        <p class="mt-1 text-sm text-slate-500">Set up a new subscription for a company</p>
                    </div>

                    <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Company Selection -->
                        <div>
                            <label for="company_id" class="block text-sm font-medium text-slate-700">Company</label>
                            <select id="company_id" name="company_id" required
                                class="mt-1 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <option value="">Select a company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Plan Selection -->
                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-slate-700">Plan</label>
                            <select id="plan_id" name="plan_id" required
                                class="mt-1 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <option value="">Select a plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->plan_name }} -
                                        ${{ number_format($plan->price, 2) }}/{{ $plan->billing_cycle }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-slate-700">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required
                                class="mt-1 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-slate-700">End Date</label>
                            <input type="date" id="end_date" name="end_date"
                                class="mt-1 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            <p class="mt-1 text-sm text-slate-500">Leave empty for auto-renewal subscriptions</p>
                        </div>

                        <!-- Auto Renew -->
                        <div class="flex items-center">
                            <input type="checkbox" id="auto_renew" name="auto_renew" value="1" checked
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="auto_renew" class="ml-2 block text-sm text-slate-900">Enable auto-renewal</label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Create Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>