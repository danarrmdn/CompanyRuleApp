<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if (is_null(Auth::user()->password_change_at))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg" role="alert">
                <p class="font-bold">Warning!</p>
                <p>You must change your default password before you can access other parts of the application.</p>
            </div>
        </div>
        @endif
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 p-6">
                        <h3 class="text-lg font-medium text-gray-900">Settings</h3>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <a href="#profile-information" class="block text-gray-600 hover:text-gray-900 font-semibold">Profile Information</a>
                            </li>
                            <li>
                                <a href="#update-password" class="block text-gray-600 hover:text-gray-900">Update Password</a>
                            </li>
                            <li>
                                <a href="#delete-account" class="block text-red-600 hover:text-red-900">Delete Account</a>
                            </li>
                        </ul>
                    </div>
                    <div class="md:col-span-2 p-6 border-l border-gray-200">
                        <div id="profile-information" class="mb-12">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        <hr class="my-8">
                        <div id="update-password" class="mb-12">
                            @include('profile.partials.update-password-form')
                        </div>
                        <hr class="my-8">
                        <div id="delete-account" class="mb-12">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
