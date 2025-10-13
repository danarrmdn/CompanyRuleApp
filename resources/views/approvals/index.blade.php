<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Approval Document') }}
            </h2>
            <div>
                <button onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Back
                </button>
            </div>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rules as $rule)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $rule->document_name }}</td>
                                        <td>{{ $rule->number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($rule->effective_date)->format('d F Y') }}</td>
                                        <td>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($rule->status == 'Approved') bg-green-100 text-green-800
                                                @elseif(Str::startsWith($rule->status, 'Pending')) bg-yellow-100 text-yellow-800
                                                @elseif($rule->status == 'Rejected' || $rule->status == 'Send Back') bg-red-100 text-red-800
                                                @endif">
                                                {{ $rule->status }}
                                            </span>
                                        </td>
                                        <td>{{ $rule->version }}</td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('company-rules.show', $rule) }}?from=approvals" class="text-indigo-600 hover:text-indigo-800" title="View">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('approvals.approve', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to approve this document?');">
                                                    @csrf
                                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-approval-{{ $rule->id }}')" class="text-green-600 hover:text-green-800" title="Approve">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'send-back-modal-{{ $rule->id }}')" class="text-yellow-600 hover:text-yellow-800" title="Send Back">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                    </svg>
                                                </button>
                                                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $rule->id }}')" class="text-red-600 hover:text-red-800" title="Reject">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-500 py-4">
                                            No documents awaiting approval.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @forelse ($rules as $rule)
        <x-modal name="reject-modal-{{ $rule->id }}" :show="false" focusable>
        <div class="p-6 light-modal-content">
            <form method="post" action="{{ route('approvals.reject', $rule) }}" class="p-6" x-ref="rejectForm">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Reject Document') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Please provide a reason for rejecting this document.') }}
                </p>
                <div class="mt-6">
                    <x-input-label for="reason" value="{{ __('Reason') }}" />
                    <textarea name="reason" id="reason" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" @keydown.enter.prevent="$refs.rejectForm.submit()"></textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-danger-button class="ms-3">
                        {{ __('Reject') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
        </x-modal>

        <x-modal name="send-back-modal-{{ $rule->id }}" :show="false" focusable>
        <div class="p-6 light-modal-content">
            <form method="post" action="{{ route('approvals.send-back', $rule) }}" class="p-6" x-ref="sendBackForm">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Send Back Document') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Please provide a reason for sending this document back.') }}
                </p>
                <div class="mt-6">
                    <x-input-label for="reason" value="{{ __('Reason') }}" />
                    <textarea name="reason" id="reason" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" @keydown.enter.prevent="$refs.sendBackForm.submit()"></textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-3">
                        {{ __('Send Back') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        </x-modal>

        <x-modal :name="'confirm-approval-' . $rule->id">
        <div class="p-6 light-modal-content">
            <form method="post" action="{{ route('approvals.approve', $rule) }}" class="p-6">
                @csrf
                @method('POST')

                <h2 class="text-lg font-medium text-gray-900">
                    Approve Document?
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to approve the document: <br>
                    <strong class="font-semibold">{{ $rule->document_name }}</strong>?
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-3">
                        {{ __('Approve Document') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        </x-modal>
    @empty
    @endforelse
</x-app-layout>