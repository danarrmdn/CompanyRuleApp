<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Edit Position') }}
            </h2>
            <a href="{{ route('positions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">
                                {{ __('Whoops! Something went wrong.') }}
                            </div>

                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('positions.update', $position) }}">
                        @csrf
                        @method('PUT')

                        <!-- Position Title -->
                        <div class="mb-4">
                            <label for="position_title" class="block text-sm font-medium text-gray-700">Position Title</label>
                            <input type="text" name="position_title" id="position_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100 text-gray-500" value="{{ $position->position_title }}" readonly>
                        </div>

                        <!-- Position Holder -->
                        <div class="mb-4">
                            <label for="holder_id" class="block text-sm font-medium text-gray-700">Position Holder</label>
                            <select name="holder_id" id="holder_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ $position->holder_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Update Position
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#holder_id').select2();
        });
    </script>
    @endpush
</x-app-layout>