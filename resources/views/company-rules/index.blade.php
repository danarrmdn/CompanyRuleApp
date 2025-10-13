<x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Document List') }}
                </h2>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Back
                </a>
            </div>
        </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end space-x-2 mb-4">
                <a href="{{ route('company-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + CREATE
                </a>
                <a href="{{ route('company-rules.create-revision') }}" class="inline-flex items-center px-4 py-2 bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-yellow-500 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + REVISE
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 flex flex-col md:flex-row gap-8" x-data="{
                    showLogModal: false,
                    logData: [],
                    documentTitle: '',
                    isLoadingLogs: false,
                    getLogs(ruleId, ruleName) {
                        this.isLoadingLogs = true;
                        this.documentTitle = ruleName;
                        this.showLogModal = true;
                        this.logData = [];

                        fetch(`/api/company-rules/${ruleId}/logs`)
                            .then(response => response.json())
                            .then(data => {
                                this.logData = data.data; // Is paginated
                                this.isLoadingLogs = false;
                            })
                            .catch(error => {
                                console.error('Error fetching logs:', error);
                                this.isLoadingLogs = false;
                            });
                    }
                }">

                    <!-- Left Column: Navigation -->
                    <div class="w-full md:w-1/4 border-b md:border-b-0 md:border-r pb-4 md:pb-0 md:pr-8">
                        <h3 class="text-lg font-semibold mb-4">Document Views</h3>
                        <nav class="flex flex-col space-y-2">
                            <a href="{{ route('company-rules.index', ['view' => 'latest']) }}" 
                               class="px-4 py-2 rounded-md text-sm font-medium {{ $view == 'latest' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                Latest Document
                            </a>
                            <a href="{{ route('company-rules.index', ['view' => 'obsolete']) }}" 
                               class="px-4 py-2 rounded-md text-sm font-medium {{ $view == 'obsolete' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                Obsolete Document
                            </a>
                            <a href="{{ route('company-rules.index', ['view' => 'all']) }}" 
                               class="px-4 py-2 rounded-md text-sm font-medium {{ $view == 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                                All Document
                            </a>
                        </nav>
                    </div>

                    <!-- Right Column: Content -->
                    <div class="w-full md:w-3/4">

                        {{-- 1. Latest Document View --}}
                        @if ($view == 'latest')
                            <h3 class="text-xl font-bold mb-4">Latest Approved Documents</h3>
                            <div class="space-y-2" x-data="{
                                openCategories: @js(request('open_category') ? [request('open_category')] : []),
                                toggleCategory(category) {
                                    const index = this.openCategories.indexOf(category);
                                    if (index === -1) {
                                        this.openCategories.push(category);
                                    } else {
                                        this.openCategories.splice(index, 1);
                                    }
                                }
                            }">
                                @foreach ($data['categories'] as $category)
                                    <div class="border rounded-lg overflow-hidden">
                                        <button @click="toggleCategory('{{ $category }}')" class="w-full flex justify-between items-center p-3 text-left font-bold" style="background-color: #E0E0E0;">
                                            <span>
                                                <span x-text="openCategories.includes('{{ $category }}') ? '▼' : '►'"></span>
                                                {{ $category }}
                                            </span>
                                        </button>
                                        <div x-show="openCategories.includes('{{ $category }}')" class="p-0" x-cloak>
                                            @if (isset($data['latestDocuments'][$category]) && $data['latestDocuments'][$category]->isNotEmpty())
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-sm">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="p-3 text-left">Document No.</th>
                                                                <th class="p-3 text-left">Document Name</th>
                                                                <th class="p-3 text-left">Department</th>
                                                                <th class="p-3 text-left">Rev No</th>
                                                                <th class="p-3 text-left">Effective Date</th>
                                                                <th class="p-3 text-left">Names of Approver</th>
                                                                <th class="p-3 text-left">Status</th>
                                                                <th class="p-3 text-left">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @foreach ($data['latestDocuments'][$category] as $rule)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="p-3">{{ $rule->number }}</td>
                                                                    <td class="p-3"><a href="{{ route('company-rules.show', ['rule' => $rule, 'view' => $view, 'open_category' => $category]) }}" class="text-indigo-600 hover:underline">{{ $rule->document_name }}</a></td>
                                                                    <td class="p-3">{{ $rule->creator->department ?? 'N/A' }}</td>
                                                                    <td class="p-3">{{ $rule->version }}</td>
                                                                    <td class="p-3">{{ $rule->effective_date ? date('d M Y', strtotime($rule->effective_date)) : 'N/A' }}</td>
                                                                    <td class="p-3">
                                                                        @php $approverNames = collect([$rule->approver1, $rule->approver2, $rule->approver3])->whereNotNull()->pluck('name')->join(', '); @endphp
                                                                        {{ $approverNames ?: 'N/A' }}
                                                                    </td>
                                                                    <td class="p-3"><span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $rule->status }}</span></td>
                                                                    <td class="p-3">
                                                                        <div class="flex items-center space-x-4">
                                                                            <button @click="getLogs({{ $rule->id }}, '{{ e($rule->document_name) }}')" title="Log Activity" class="p-1 text-gray-600 hover:text-gray-900">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd" />
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="p-4 text-gray-500">There are no documents for this category.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- 2. Obsolete Document View --}}
                        @if ($view == 'obsolete')
                            <h3 class="text-xl font-bold mb-4">Obsolete Documents</h3>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="p-3 text-left">Document No.</th>
                                                <th class="p-3 text-left">Document Name</th>
                                                <th class="p-3 text-left">Department</th>
                                                <th class="p-3 text-left">Rev No</th>
                                                <th class="p-3 text-left">Effective Date</th>
                                                <th class="p-3 text-left">Names of Approver</th>
                                                <th class="p-3 text-left">Status</th>
                                                <th class="p-3 text-left">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse ($data['obsoleteDocuments'] as $rule)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="p-3">{{ $rule->number }}</td>
                                                    <td class="p-3"><a href="{{ route('company-rules.show', ['rule' => $rule, 'view' => $view]) }}" class="text-indigo-600 hover:underline">{{ $rule->document_name }}</a></td>
                                                    <td class="p-3">{{ $rule->creator->department ?? 'N/A' }}</td>
                                                    <td class="p-3">{{ $rule->version }}</td>
                                                    <td class="p-3">{{ $rule->effective_date ? date('d M Y', strtotime($rule->effective_date)) : 'N/A' }}</td>
                                                    <td class="p-3">
                                                        @php $approverNames = collect([$rule->approver1, $rule->approver2, $rule->approver3])->whereNotNull()->pluck('name')->join(', '); @endphp
                                                        {{ $approverNames ?: 'N/A' }}
                                                    </td>
                                                    <td class="p-3"><span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Obsolete</span></td>
                                                    <td class="p-3">
                                                        <div class="flex items-center space-x-4">
                                                            <button @click="getLogs({{ $rule->id }}, '{{ e($rule->document_name) }}')" title="Log Activity" class="p-1 text-gray-600 hover:text-gray-900">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="p-4 text-center text-gray-500">No obsolete documents found.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-6">{{ $data['obsoleteDocuments']->links() }}</div>
                        @endif

                        {{-- 3. All Document View --}}
                        @if ($view == 'all')
                            <h3 class="text-xl font-bold mb-4">All Documents</h3>
                            <div class="flex flex-wrap gap-2 mb-6 border-b pb-4">
                                <a href="{{ route('company-rules.index', ['view' => 'all']) }}" class="px-3 py-1 text-sm rounded-full {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">All</a>
                                <a href="{{ route('company-rules.index', ['view' => 'all', 'status' => 'approved']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'approved' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800' }}">Approved</a>
                                <a href="{{ route('company-rules.index', ['view' => 'all', 'status' => 'pending']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800' }}">Pending</a>
                                <a href="{{ route('company-rules.index', ['view' => 'all', 'status' => 'Draft']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'Draft' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-800' }}">Draft</a>
                                <a href="{{ route('company-rules.index', ['view' => 'all', 'status' => 'Send Back']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'Send Back' ? 'bg-orange-500 text-white' : 'bg-orange-100 text-orange-800' }}">Send Back</a>
                                <a href="{{ route('company-rules.index', ['view' => 'all', 'status' => 'Rejected']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'Rejected' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800' }}">Rejected</a>
                            </div>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="p-3 text-left">Document No.</th>
                                                <th class="p-3 text-left">Document Name</th>
                                                <th class="p-3 text-left">Department</th>
                                                <th class="p-3 text-left">Rev No</th>
                                                <th class="p-3 text-left">Effective Date</th>
                                                <th class="p-3 text-left">Names of Approver</th>
                                                <th class="p-3 text-left">Status</th>
                                                <th class="p-3 text-left">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse ($data['allDocuments'] as $rule)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="p-3">{{ $rule->number }}</td>
                                                    <td class="p-3"><a href="{{ route('company-rules.show', ['rule' => $rule, 'view' => $view]) }}" class="text-indigo-600 hover:underline">{{ $rule->document_name }}</a></td>
                                                    <td class="p-3">{{ $rule->creator->department ?? 'N/A' }}</td>
                                                    <td class="p-3">{{ $rule->version }}</td>
                                                    <td class="p-3">{{ $rule->effective_date ? date('d M Y', strtotime($rule->effective_date)) : 'N/A' }}</td>
                                                    <td class="p-3">
                                                        @php $approverNames = collect([$rule->approver1, $rule->approver2, $rule->approver3])->whereNotNull()->pluck('name')->join(', '); @endphp
                                                        {{ $approverNames ?: 'N/A' }}
                                                    </td>
                                                     <td class="p-3">
                                                        @php
                                                            $statusName = $rule->is_obsolete ? 'Obsolete' : $rule->status;
                                                            $statusClass = match(true) {
                                                                $rule->is_obsolete => 'bg-gray-200 text-gray-700',
                                                                $rule->status == 'Approved' => 'bg-green-100 text-green-800',
                                                                Str::startsWith($rule->status, 'Pending') => 'bg-yellow-100 text-yellow-800',
                                                                $rule->status == 'Rejected' || $rule->status == 'Send Back' => 'bg-red-100 text-red-800',
                                                                default => 'bg-gray-100 text-gray-800',
                                                            };
                                                        @endphp
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusName }}</span>
                                                    </td>
                                                    <td class="p-3">
                                                        <div class="flex items-center space-x-4">
                                                            <button @click="getLogs({{ $rule->id }}, '{{ e($rule->document_name) }}')" title="Log Activity" class="p-1 text-gray-600 hover:text-gray-900">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 10.586V6z" clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                            @can('update', $rule)
                                                                @if (in_array($rule->status, ['Draft', 'Send Back']))
                                                                    <a href="{{ route('company-rules.edit', $rule->id) }}" title="Edit" class="p-1 text-blue-600 hover:text-blue-900">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                            @endcan
                                                            @can('delete', $rule)
                                                                @if (in_array($rule->status, ['Draft', 'Send Back']))
                                                                    <form action="{{ route('company-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" title="Delete" class="p-1 text-red-600 hover:text-red-900">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="p-4 text-center text-gray-500">No documents found for this status.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-6">{{ $data['allDocuments']->appends(request()->query())->links() }}</div>
                        @endif

                    </div>

                    <!-- Log Modal -->
                    <div x-show="showLogModal" @keydown.escape.window="showLogModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                        <div @click.away="showLogModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-lg font-semibold">Activity Log: <span x-text="documentTitle" class="font-normal"></span></h3>
                                <button @click="showLogModal = false" class="text-gray-500 hover:text-gray-800">&times;</button>
                            </div>
                            <div class="p-6 overflow-y-auto">
                                <div x-show="isLoadingLogs" class="text-center">Loading...</div>
                                <div x-show="!isLoadingLogs" class="space-y-4">
                                    <template x-for="log in logData" :key="log.id">
                                        <div class="border-b pb-2">
                                            <p class="font-semibold" x-text="log.activity"></p>
                                            <p class="text-sm text-gray-600">by <span x-text="log.user ? log.user.name : 'System'"></span></p>
                                            <p class="text-xs text-gray-400" x-text="new Date(log.created_at).toLocaleString()"></p>
                                        </div>
                                    </template>
                                    <div x-show="logData.length === 0">
                                        <p>No activity logs found for this document.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
