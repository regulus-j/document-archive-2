@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Notifications</h2>
    <div class="bg-white shadow rounded-lg divide-y">
        @forelse($notifications as $notification)
            <div class="p-4 flex items-center {{ $notification->read_at ? 'bg-slate-50' : 'bg-indigo-50' }}">
                <div class="flex-1">
                    <div class="font-semibold text-slate-800">
                        {{ json_decode($notification->data)->message ?? 'Notification' }}
                    </div>
                    <div class="text-xs text-slate-500 mt-1">
                        {{ json_decode($notification->data)->title ?? '' }}
                        <span class="ml-2">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if(!$notification->read_at)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button class="ml-4 px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs">Mark as Read</button>
                </form>
                @endif
            </div>
        @empty
            <div class="p-6 text-center text-slate-500">No notifications found.</div>
        @endforelse
    </div>
</div>
@endsection
