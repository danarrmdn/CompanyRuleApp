<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Profile') }}
            </h2>

            <a href="{{ route('company-rules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>
    

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50"
                >
                    <p>{{ __('Profile has been updated.') }}</p>
                </div>
            @elseif (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50"
                >
                    <p>{{ __('Password has been updated.') }}</p>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">Profile Details</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            @if($user->avatar)
                                <button @click="$dispatch('open-modal', 'full-profile-photo')">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile Photo" class="h-32 w-32 rounded-full object-cover cursor-pointer hover:opacity-80 transition">
                                </button>
                            @else
                                <img src="{{ 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Profile Photo" class="h-32 w-32 rounded-full object-cover">
                            @endif
                        </div>
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email Address</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Employee ID</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $user->emp_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Department</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $user->department }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Grade</p>
                                <p class="mt-1 text-lg text-gray-900">{{ $user->grade }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    {{-- Modal for Full Size Profile Photo --}}
    <x-modal name="full-profile-photo" maxWidth="4xl">
        <div class="p-1 bg-gray-800">
            @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile Photo" class="w-full h-full object-contain max-h-[90vh]">
            @endif
        </div>
    </x-modal>

</x-app-layout>