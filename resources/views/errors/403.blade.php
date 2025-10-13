<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Access denied') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Access denied! (Error 403)</h3>
                    <p class="mt-2 text-gray-600 max-w-2xl mx-auto">
                        You do not have permission to access this page. The document you are trying to view may be under review and is private only to its creator and assigned approvers.
                    </p>
                    <p class="mt-2 text-gray-600 max-w-2xl mx-auto">
                        If you feel you should have access, please contact the document creator or your system administrator.
                    </p>
                    <div class="mt-6">
                        <button onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            Back to Previous Page
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>