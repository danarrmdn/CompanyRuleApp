<x-app-layout>
    @push('styles')
    <style>
        .notification-link {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            text-align: left;
        }
        .notification-link p.message {
            font-size: 1rem;
            line-height: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            word-wrap: break-word; 
            white-space: normal;
        }
        .notification-link p.time {
            font-size: 0.875rem; 
            color: indigo-50; 
            margin-top: 0.25rem;
        }
    </style>
    @endpush
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('All Notifications') }}
            </h2>
            <div class="flex space-x-2">
                @if($unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Mark All as Read
                    </button>
                </form>
                @endif
                <button onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Back
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        @forelse ($notifications as $notification)
                            <a href="{{ route('notifications.read', $notification->id) }}" class="notification-link rounded-lg border {{ $notification->read_at ? 'border-gray-200 bg-gray-50' : 'border-indigo-200 bg-indigo-50' }}">
                                <div class="flex justify-between items-start">
                                    <p class="message">
                                        {{ $notification->data['message'] ?? 'Notification' }}
                                    </p>
                                    @if(!$notification->read_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <p class="time">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </p>
                            </a>
                        @empty
                            <div class="p-3 text-sm text-gray-500 text-center">
                                No notifications.
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($notifications, 'links'))
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>