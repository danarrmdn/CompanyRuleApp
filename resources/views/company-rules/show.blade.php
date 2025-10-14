<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Document Details') }}
            </h2>
            <div>
                                <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($rule->status == 'Send Back')
            <div class="bg-orange-50 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Document Requires Revision!</strong>
                <p class="mt-2">{{ $rule->reason }}</p>
            </div>
            @elseif($rule->status == 'Rejected')
            <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Document Rejected!</strong>
                <p class="mt-2">{{ $rule->reason }}</p>
            </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-medium">{{ $rule->document_name }}</h3>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Version: {{ $rule->version }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 border-b pb-4 mb-4">Viewing document details</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="space-y-4">
                            <div>
                                <dt class="font-bold">Document Number</dt>
                                <dd class="text-gray-600">{{ $rule->number ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold">Category</dt>
                                <dd class="text-gray-600">{{ $rule->category ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold">Effective Date</dt>
                                <dd class="text-gray-600">{{ $rule->effective_date ? \Carbon\Carbon::parse($rule->effective_date)->format('d F Y') : 'N/A' }}</dd>
                            </div>
                        </div>
                        <div class="space-y-4">
                             <div>
                                <dt class="font-bold">Creator</dt>
                                <dd class="text-gray-600">{{ $rule->creator->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold">Controller</dt>
                                @php
                                    $controllers = collect([$rule->controller1, $rule->controller2, $rule->controller3, $rule->controller4, $rule->controller5])->filter();
                                @endphp
                                @if($controllers->isNotEmpty())
                                    <ul class="mt-1 text-gray-600 list-disc list-inside">
                                        @foreach($controllers as $controller)
                                            <li>{{ optional($controller->userPosition)->position_title ?? $controller->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <dd class="text-gray-500">-</dd>
                                @endif
                            </div>
                            <div>
                                <dt class="font-bold">Approver</dt>
                                @php
                                    $approvers = collect([$rule->approver1, $rule->approver2, $rule->approver3])->filter();
                                @endphp
                                @if($approvers->isNotEmpty())
                                    <ul class="mt-1 text-gray-600 list-disc list-inside">
                                        @foreach($approvers as $approver)
                                            <li>{{ $approver->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <dd class="text-gray-500">-</dd>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <dt class="font-bold mb-2">Status</dt>
                        <dd>
                            @if($rule->is_obsolete)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-600">
                                    Obsolete
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($rule->status == 'Approved') bg-green-100 text-green-800 
                                    @elseif(Str::startsWith($rule->status, 'Pending')) bg-yellow-100 text-yellow-800 
                                    @elseif($rule->status == 'Rejected' || $rule->status == 'Send Back') bg-red-100 text-red-800 
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $rule->status }}
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div class="mt-6 pt-4 border-t">
                        <dt class="font-bold">Reason of Revision</dt>
                        <dd class="mt-1 text-gray-600 bg-gray-50 p-3 rounded-md">{{ $rule->reason_of_revision ?? 'N/A' }}</dd>
                    </div>

                    <div class="mt-6 pt-4 border-t">
                        <dt class="font-bold mb-2">File PDF</dt>
                        @if ($rule->file_path)
                            <div class="border rounded-lg relative" style="height: 80vh; overflow: hidden;">
                                @php
                                    $viewerUrl = route('pdf.viewer') . '?doc=' . urlencode(route('rules.file.show', ['id' => $rule->id, 'v' => $rule->updated_at->timestamp]));
                                    
                                    // Logic to add watermark for obsolete documents
                                    if ($rule->is_obsolete || request()->get('v') === 'older') {
                                        $viewerUrl .= '&obsolete=true';
                                    } 
                                    // Restrict toolbar if the conditions are not met
                                    else if (!$canDownloadAndPrint) {
                                        $viewerUrl .= '&restricted=true';
                                    }
                                @endphp
                                <iframe 
                                    src="{{ $viewerUrl }}" 
                                    width="100%" 
                                    height="100%" 
                                    title="PDF Viewer"
                                    style="border: none;">
                                </iframe>

                            </div>
                        @else
                            <p class="text-gray-500 mt-2">No file available.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
