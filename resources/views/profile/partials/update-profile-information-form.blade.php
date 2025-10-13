<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div x-data="{photoPreview: null, showDropdown: false}" class="col-span-6 sm:col-span-4">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Profile Photo') }}</h2>
            <!-- Profile Photo File Input -->
            <input name="avatar" id="avatar" type="file" class="hidden"
                   x-ref="photo"
                   @change="
                       const reader = new FileReader();
                       reader.onload = (e) => {
                           photoPreview = e.target.result;
                       };
                       reader.readAsDataURL($refs.photo.files[0]);
                   ">

            <div class="mt-2" @click.away="showDropdown = false">
                <div class="relative">
                    <!-- Current/New Photo -->
                    <div x-show="!photoPreview">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Profile Photo" class="h-24 w-24 rounded-full object-cover cursor-pointer" @click="showDropdown = !showDropdown">
                    </div>
                    <div x-show="photoPreview" style="display: none;">
                        <span class="block h-24 w-24 rounded-full bg-cover bg-center cursor-pointer" :style="'background-image: url(\'' + photoPreview + '\');'" @click="showDropdown = !showDropdown"></span>
                    </div>

                    <!-- Dropdown Menu -->
                    <div x-show="showDropdown" style="display: none;" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-60">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            @if ($user->avatar)
                                <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" @click="showDropdown = false; $dispatch('open-modal', 'view-photo')">{{ __('View Photo') }}</button>
                            @endif
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" @click.prevent="$refs.photo.click(); showDropdown = false;">{{ __('Change Photo') }}</a>
                            @if ($user->avatar)
                                <button type="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" role="menuitem"
                                        @click="
                                            showDropdown = false;
                                            fetch('{{ route('profile.avatar.destroy') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                                    'Accept': 'application/json',
                                                }
                                            })
                                            .then(response => {
                                                if (response.ok) {
                                                    window.location.reload();
                                                } else {
                                                    console.error('Delete request failed. Server responded with status:', response.status);
                                                    response.json().then(data => {
                                                        console.error('Error details:', data);
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                console.error('A network error occurred:', error);
                                            });
                                        ">
                                    {{ __('Delete Photo') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

            <!-- View Photo Modal -->
            <x-modal name="view-photo" maxWidth="2xl">
                <div class="p-4">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}" alt="Profile Photo" class="w-full h-auto rounded-lg">
                </div>
            </x-modal>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>