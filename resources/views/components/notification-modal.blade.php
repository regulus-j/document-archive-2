<div class="p-0 min-h-[250px] max-h-[500px] overflow-y-auto overflow-x-hidden min-w-[600px] bg-white shadow-lg rounded-lg border border-slate-200">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
        <h5 class="font-semibold text-xl text-slate-800 m-0">Notifications</h5>
        <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
            View All
        </a>
    </div>
    <ul class="divide-y divide-slate-100 overflow-y-auto">
        @forelse($notifications as $notification)
            <li class="flex justify-between items-start {{ $notification->read_at ? 'bg-slate-50' : 'bg-blue-50' }} p-4 hover:bg-slate-100 transition-colors duration-150">
                <div class="flex-1 pr-4">
                    <div class="font-semibold text-lg text-slate-900 mb-2">
                        {{ json_decode($notification->data)->message ?? 'Notification' }}
                    </div>
                    <small class="text-slate-600 text-base">
                        {{ json_decode($notification->data)->title ?? '' }} &middot; {{ $notification->created_at->diffForHumans() }}
                    </small>
                </div>
                @if(!$notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="flex-shrink-0">
                        @csrf
                        <button class="text-sm font-medium text-blue-600 hover:bg-blue-100 rounded-md px-4 py-2 transition-colors">
                            Mark as Read
                        </button>
                    </form>
                @endif            </li>
        @empty
            <li class="text-center text-slate-400 py-16 px-8 text-lg flex items-center justify-center min-h-[150px]">
                <div>
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p>No notifications found.</p>
                </div>
            </li>
        @endforelse
    </ul>
</div>
