<x-app-layout>
    <x-slot name="header">
        @php
            $hasControllers = count(array_filter([$rule->controller_1_id, $rule->controller_2_id, $rule->controller_3_id, $rule->controller_4_id, $rule->controller_5_id])) > 0;
            $hasApprovers = count(array_filter([$rule->approver_1_id, $rule->approver_2_id, $rule->approver_3_id])) > 0;
            $controllersDisabled = ($rule->status !== 'Draft' || $hasControllers);
            $approversDisabled = !in_array($rule->status, ['Draft', 'Send Back']);
        @endphp
        <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Edit Company Document') }}
                </h2>
                <div>
                                    <a href="{{ route('company-rules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Back</a>
                </div>
        </div>
    </x-slot>
    

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-medium">Editing: <span class="text-indigo-600">{{ $rule->document_name }}</span></h3>
                    <p class="text-sm text-gray-500 border-b border-gray-200 pb-4 mb-4">Update the document details below. The form is reset for clarity.</p>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company-rules.update', $rule) }}" enctype="multipart/form-data" id="edit-form">
                        @csrf
                        @method('PUT')
                        
                        {{-- Controller --}}
                        <div class="border rounded-md mb-6 p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                <label class="font-semibold text-sm">No of Controller</label>
                                <div class="md:col-span-3 flex items-center space-x-4">
                                    @foreach(range(1, 5) as $i)
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="no_of_controller" value="{{ $i }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('no_of_controller', count(array_filter([$rule->controller_1_id, $rule->controller_2_id, $rule->controller_3_id, $rule->controller_4_id, $rule->controller_5_id]))) == $i ? 'checked' : '' }} {{ $controllersDisabled ? 'disabled' : '' }}>
                                        <span>{{ $i }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            @foreach(range(1, 5) as $i)
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="controller-row-{{$i}}" style="display: {{ count(array_filter([$rule->controller_1_id, $rule->controller_2_id, $rule->controller_3_id, $rule->controller_4_id, $rule->controller_5_id])) >= $i ? 'grid' : 'none' }}">
                                <label class="font-semibold text-sm">Controller - {{$i}}</label>
                                <div class="md:col-span-3">
                                    <select name="controller_{{$i}}_id" class="select2-class block w-full border-gray-300 rounded-md shadow-sm" {{ $controllersDisabled ? 'disabled' : '' }}>
                                        <option value=""></option>
                                        @foreach ($controllers as $position)
                                            <option value="{{ $position->holder_id }}" {{ old('controller_'.$i.'_id', $rule->{'controller_'.$i.'_id'}) == $position->holder_id ? 'selected' : '' }}>
                                                {{ $position->position_title }} - {{ $position->holder->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        {{-- Main Form Section --}}
                        <div class="border rounded-md">
                            <div class="divide-y divide-gray-200">
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <div class="font-semibold text-sm text-gray-600">Department</div>
                                    <div class="col-span-3 text-sm text-gray-800">{{ $rule->creator->department ?? 'Information and Technology' }}</div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <x-input-label for="category" class="font-semibold text-sm text-gray-600" :value="__('Category *')" />
                                    <div class="col-span-3 flex items-center space-x-2">
                                        <x-text-input id="category" class="block w-full" type="text" name="category" :value="old('category', $rule->category)" required readonly />
                                        <x-primary-button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'keywords-modal')">...</x-primary-button>
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <x-input-label for="number" class="font-semibold text-sm text-gray-600" :value="__('Number *')" />
                                    <div class="col-span-3">
                                        <x-text-input id="number" class="block w-full" type="text" name="number" :value="old('number', $rule->number)" required />
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <x-input-label for="document_name" class="font-semibold text-sm text-gray-600" :value="__('Document Name *')" />
                                    <div class="col-span-3">
                                        <x-text-input id="document_name" class="block w-full" type="text" name="document_name" :value="old('document_name', $rule->document_name)" required />
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-start">
                                    <x-input-label for="reason_of_revision" class="font-semibold text-sm text-gray-600" :value="__('Reason of Revision *')" />
                                    <div class="col-span-3">
                                        <textarea id="reason_of_revision" name="reason_of_revision" rows="3" class="block w-full border-gray-300 focus:border-indigo-600 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('reason_of_revision', $rule->reason_of_revision) }}</textarea>
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <x-input-label for="effective_date" class="font-semibold text-sm text-gray-600" :value="__('Effective Date *')" />
                                    <div class="col-span-3">
                                        <x-text-input id="effective_date" class="block w-full" type="date" name="effective_date" :value="old('effective_date', $rule->effective_date)" required />
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <div class="font-semibold text-sm text-gray-600">Creator</div>
                                    <div class="col-span-3">
                                        <x-text-input class="block w-full bg-gray-100" type="text" value="{{ $rule->creator->name ?? 'N/A' }}" readonly />
                                    </div>
                                </div>

                                {{-- Approver --}}
                                <div class="border rounded-md mb-6 p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                        <label class="font-semibold text-sm">Names of Approver</label>
                                        <div class="md:col-span-3 flex items-center space-x-4">
                                            @foreach(range(1, 3) as $i)
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="no_of_approver" value="{{ $i }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('no_of_approver', count(array_filter([$rule->approver_1_id, $rule->approver_2_id, $rule->approver_3_id]))) == $i ? 'checked' : '' }} {{ $approversDisabled ? 'disabled' : '' }}>
                                                <span>{{ $i }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach(range(1, 3) as $i)
                                    <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="approver-row-{{$i}}" style="display: {{ count(array_filter([$rule->approver_1_id, $rule->approver_2_id, $rule->approver_3_id])) >= $i ? 'grid' : 'none' }}">
                                        <label class="font-semibold text-sm">Approver - {{$i}}</label>
                                        <div class="md:col-span-3">
                                            <select name="approver_{{$i}}_id" class="select2-class block w-full border-gray-300 rounded-md shadow-sm" {{ $approversDisabled ? 'disabled' : '' }}>
                                                <option value=""></option>
                                                @foreach ($approvers_list as $user)
                                                    <option value="{{ $user->id }}" {{ old('approver_'.$i.'_id', $rule->{'approver_'.$i.'_id'}) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->emp_id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <x-input-label for="file" class="font-semibold text-sm text-gray-600" :value="__('Change File (Optional)')" />
                                    <div class="col-span-3 flex flex-col items-center">
                                        @if($rule->file_path)
                                            <div class="mb-2">
                                                <button type="button" id="view-pdf-btn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-800">
                                                    View Current File
                                                </button>
                                            </div>
                                        @endif
                                        <div class="w-full">
                                            <x-filepond name="file" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-6 border-t">
                            <input type="hidden" name="is_draft" id="is-draft-input" value="0">
                            <x-danger-button type="button" class="me-4" onclick="document.getElementById('is-draft-input').value = '1'; document.getElementById('edit-form').submit();">
                                {{ __('Save as Draft') }}
                            </x-danger-button>
                            <x-primary-button>
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <x-modal name="keywords-modal" :show="false" focusable>
        <div class="p-6 light-modal-content" x-data="{ selectedKeywords: document.getElementById('category').value.split(', ') }">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Select Keywords') }}</h2>
            <div class="mt-4 space-y-2">
                <label class="flex items-center p-2 rounded-md hover:bg-gray-100">
                    <input class="keyword-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="checkbox" value="00 Policy & Principle: PP" x-model="selectedKeywords">
                    <span class="ms-2 text-sm text-gray-600">00 Policy & Principle: PP</span>
                </label>
                <label class="flex items-center p-2 rounded-md hover:bg-gray-100">
                    <input class="keyword-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="checkbox" value="10 Management System: MS" x-model="selectedKeywords">
                    <span class="ms-2 text-sm text-gray-600">10 Management System: MS</span>
                </label>
                <label class="flex items-center p-2 rounded-md hover:bg-gray-100">
                    <input class="keyword-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="checkbox" value="20 Business & Operation: BO" x-model="selectedKeywords">
                    <span class="ms-2 text-sm text-gray-600">20 Business & Operation: BO</span>
                </label>
                <label class="flex items-center p-2 rounded-md hover:bg-gray-100">
                    <input class="keyword-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="checkbox" value="90 Others: OT" x-model="selectedKeywords">
                    <span class="ms-2 text-sm text-gray-600">90 Others: OT</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end">
                <x-danger-button type="button" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-danger-button>
                <x-primary-button class="ms-3" x-on:click="document.getElementById('category').value = selectedKeywords.join(', '); $dispatch('close')">OK</x-primary-button>
            </div>
        </div>
    </x-modal>

    {{-- PDF Viewer Modal --}}
    <div id="pdf-modal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-11/12 h-5/6 max-w-6xl">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium">{{ $rule->document_name }}</h3>
                <button id="close-pdf-modal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 h-5/6">
                <iframe id="pdf-iframe" class="w-full h-full border-0" src="" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const viewPdfBtn = document.getElementById('view-pdf-btn');
        const pdfModal = document.getElementById('pdf-modal');
        const closePdfModal = document.getElementById('close-pdf-modal');
        const pdfIframe = document.getElementById('pdf-iframe');
        
        if (viewPdfBtn) {
            viewPdfBtn.addEventListener('click', function() {
                const pdfUrl = "{{ route('rules.file.show', $rule) }}";
                const viewerUrl = "{{ route('pdf.viewer') }}?doc=" + encodeURIComponent(pdfUrl);
                pdfIframe.src = viewerUrl;
                
                pdfModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        }
        
        if (closePdfModal) {
            closePdfModal.addEventListener('click', function() {
                pdfModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                
                pdfIframe.src = '';
            });
        }
        
        if (pdfModal) {
            pdfModal.addEventListener('click', function(e) {
                if (e.target === pdfModal) {
                    pdfModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    pdfIframe.src = '';
                }
            });
        }
        
        $(document).ready(function() {
            
            function initializeSelect2(selector) {
                if (!selector.hasClass('select2-hidden-accessible')) {
                    selector.select2({
                        placeholder: 'Choose...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }

            $('.select2-class:visible').each(function() {
                initializeSelect2($(this));
            });

            $('input[name="no_of_controller"]').change(function() {
                if (!$(this).is(':disabled')) {
                    var selectedValue = parseInt($(this).val());
                    $('[id^="controller-row-"]').hide();
                    for (var i = 1; i <= selectedValue; i++) {
                        var row = $('#controller-row-' + i);
                        row.css('display', 'grid');
                        initializeSelect2(row.find('.select2-class'));
                    }
                }
            });

            $('input[name="no_of_approver"]').change(function() {
                if (!$(this).is(':disabled')) {
                    var selectedValue = parseInt($(this).val());
                    $('[id^="approver-row-"]').hide();
                    for (var i = 1; i <= selectedValue; i++) {
                        var row = $('#approver-row-' + i);
                        row.css('display', 'grid');
                        initializeSelect2(row.find('.select2-class'));
                    }
                }
            });

        });
    </script>
    @endpush
</x-app-layout>