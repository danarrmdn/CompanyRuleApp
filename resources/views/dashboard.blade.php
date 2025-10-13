<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h3>
                    <p class="mt-2 text-gray-600">Here is a brief overview of the company rule document management system.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <a href="{{ route('company-rules.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Total Documents</h4>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'pending']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Pending Approval</h4>
                        <p class="text-3xl font-bold text-yellow-500">{{ $pendingDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'send_back']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Send Back</h4>
                        <p class="text-3xl font-bold text-orange-500">{{ $sendBackDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'draft']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Draft</h4>
                        <p class="text-3xl font-bold text-gray-500">{{ $draftDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'approved']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Approved Documents</h4>
                        <p class="text-3xl font-bold text-green-600">{{ $approvedDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'obsolete']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Obsolete Documents</h4>
                        <p class="text-3xl font-bold text-gray-500">{{ $obsoleteDocuments }}</p>
                    </div>
                </a>
                <a href="{{ route('company-rules.index', ['status' => 'rejected']) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800">Rejected Documents</h4>
                        <p class="text-3xl font-bold text-red-600">{{ $rejectedDocuments }}</p>
                    </div>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('company-rules.index') }}"
                           class="group block p-6 bg-blue-600 rounded-lg border border-transparent text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-white transform group-hover:scale-110 transition-transform duration-300"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">{{ __('View Document') }}</h3>
                            <p class="text-sm text-blue-200">{{ __('Browse and manage existing documents.') }}</p>
                        </a>

                        <a href="{{ route('company-rules.create') }}"
                           class="group block p-6 bg-white rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-blue-600 transform group-hover:scale-110 transition-transform duration-300"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">{{ __('Create Document') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Start a new document from scratch.') }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
