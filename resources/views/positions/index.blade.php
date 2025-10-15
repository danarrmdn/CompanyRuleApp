<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Position Holders') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('positions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Add New Position
                        </a>
                        <form action="{{ route('positions.index') }}" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" class="form-input rounded-md shadow-sm" placeholder="Search positions..." value="{{ $search ?? '' }}">
                                <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <table class="min-w-full divide-y divide-gray-200 mt-4">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holder</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($positions as $position)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $position->position_title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $position->holder->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('positions.edit', $position) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    No positions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $positions->appends(['search' => $search])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>