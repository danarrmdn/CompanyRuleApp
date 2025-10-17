@php
    use App\Http\Controllers\ApprovalController;
    use Illuminate\Support\Facades\Auth;
@endphp
<nav x-data="{ open: false }" class="navbar-dashboard sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-xl">
                        <img src="{{ asset('images/side-name-logo-nobg.png') }}" alt="NSI Logo" class="block h-10 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('company-rules.index')" :active="request()->routeIs('company-rules.index', 'company-rules.show', 'company-rules.edit')">
                        {{ __('Document List') }}
                    </x-nav-link>

                    @if (Auth::check() && Auth::user()->grade >= 8)
                        @php
                            $pendingCount = ApprovalController::getPendingCount();
                        @endphp
                        <x-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('approvals.index')" :active="request()->routeIs('approvals.index')">
                            <span class="relative">
                                {{ __('Need Actions') }}
                                @if ($pendingCount > 0)
                                    <span class="absolute top-[-0.75rem] right-[-1.5rem] inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </span>
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->roles >= 2)
                        <div class="hidden sm:flex sm:items-center sm:ms-4">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-800 bg-transparent hover:text-blue-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>Manage</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('users.index')">
                                        {{ __('User') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('positions.index')">
                                        {{ __('Position') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Dropdown Notifikasi --}}
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="96">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-800 hover:text-gray-900 focus:outline-none">
                                <svg class="w-7 h-7" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 4 14h12a1 1 0 0 0 .707-1.707L16 11.586V8a6 6 0 0 0-6-6Zm0 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                                </svg>
                                <span class="sr-only">Notifications</span>
                                @if($unreadNotificationsCount > 0)
                                    <div class="absolute -top-2 -end-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-lg">{{ $unreadNotificationsCount }}</div>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div>
                                <div class="flex justify-between items-center px-4 py-2 bg-gray-50">
                                    <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                                    <div class="flex space-x-4">
                                        @if($unreadNotificationsCount > 0)
                                            <div>
                                                <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                        Mark all as read
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    @forelse ($notifications->take(5) as $notification)
                                        <a href="{{ route('notifications.read', $notification->id) }}" 
                                        class="block w-full px-4 py-3 text-start text-sm leading-5 text-gray-700 hover:bg-indigo-50 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out {{ $notification->read_at ? 'bg-gray-50' : 'bg-white' }}">
                                            
                                            <p class="font-semibold text-base text-gray-800 break-words">
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </p>
                                            
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                @if(!$notification->read_at)
                                                    <span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">New</span>
                                                @endif
                                            </p>
                                        </a>
                                    @empty
                                        <div class="p-3 text-sm text-gray-500 text-center">
                                            No notifications.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="border-t border-gray-200">
                                <x-dropdown-link :href="route('notifications.index')" class="text-center text-sm font-bold">
                                    View All Notifications
                                </x-dropdown-link>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
                {{-- Dropdown Profil --}}
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-800 focus:outline-none transition ease-in-out duration-150">
                                @if (Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="h-8 w-8 rounded-full me-2 object-cover">
                                @else
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 me-2">
                                        <span class="text-sm font-medium text-gray-600 leading-none">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    </span>
                                @endif

                                <div class="ms-1">
                                    
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('company-rules.index', ['my_documents' => 1])">
                                {{ __('My Document') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.settings')">
                                {{ __('Account Settings') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="hover:bg-red-50">
                                    <span class="text-red-600 font-medium">{{ __('Log Out') }}</span>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('company-rules.index')" :active="request()->routeIs('company-rules.index', 'company-rules.show', 'company-rules.edit')">
                {{ __('Document List') }}
            </x-responsive-nav-link>

            @if (Auth::check() && Auth::user()->grade >= 8)
                @php
                    $pendingCount = ApprovalController::getPendingCount();
                @endphp
                <x-responsive-nav-link :href="is_null(Auth::user()->password_change_at) ? route('profile.settings') : route('approvals.index')" :active="request()->routeIs('approvals.index')">
                    <span class="relative">
                        {{ __('Approve Document') }}
                        @if ($pendingCount > 0)
                            <span class="absolute top-[-0.25rem] right-[-1.5rem] inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </span>
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('company-rules.index', ['my_documents' => 1])">
                    {{ __('My Document') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.settings')">
                    {{ __('Account Settings') }}
                </x-responsive-nav-link>

                <div class="border-t border-gray-200"></div>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <span class="text-red-600 font-medium">{{ __('Log Out') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>