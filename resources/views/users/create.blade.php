<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Add New User') }}
            </h2>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Employee ID -->
                        <div class="mt-4">
                            <x-input-label for="emp_id" :value="__('Employee ID')" />
                            <x-text-input id="emp_id" class="block mt-1 w-full" type="text" name="emp_id" :value="old('emp_id')" required />
                            <x-input-error :messages="$errors->get('emp_id')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Roles -->
                        <div class="mt-4">
                            <x-input-label for="roles" :value="__('Role')" />
                            <x-text-input id="roles" class="block mt-1 w-full" type="number" name="roles" :value="old('roles')" required />
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <!-- Grade -->
                        <div class="mt-4">
                            <x-input-label for="grade" :value="__('Grade')" />
                            <x-text-input id="grade" class="block mt-1 w-full" type="number" name="grade" :value="old('grade')" />
                            <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                        </div>

                        <!-- Department -->
                        <div class="mt-4">
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" />
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>

                        <!-- Department ID -->
                        <div class="mt-4">
                            <x-input-label for="department_id" :value="__('Department ID')" />
                            <x-text-input id="department_id" class="block mt-1 w-full" type="number" name="department_id" :value="old('department_id')" />
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                        </div>

                        <!-- Section -->
                        <div class="mt-4">
                            <x-input-label for="section" :value="__('Section')" />
                            <x-text-input id="section" class="block mt-1 w-full" type="text" name="section" :value="old('section')" />
                            <x-input-error :messages="$errors->get('section')" class="mt-2" />
                        </div>

                        <!-- Section ID -->
                        <div class="mt-4">
                            <x-input-label for="section_id" :value="__('Section ID')" />
                            <x-text-input id="section_id" class="block mt-1 w-full" type="number" name="section_id" :value="old('section_id')" />
                            <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
