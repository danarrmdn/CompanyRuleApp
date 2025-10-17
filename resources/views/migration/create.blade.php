<x-app-layout>
    @push('styles')
        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Migrasi Data') }}
            </h2>
            <div>
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-lg font-bold text-gray-800">Migrate Company Rules Document</p>
                            <p class="text-sm text-gray-500 mt-1">▶ This document will be automatically approved upon submission.</p>
                        </div>
                        <span class="bg-green-200 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Auto-Approved</span>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('migration.store') }}" enctype="multipart/form-data" id="create-form">
                        @csrf

                        <input type="hidden" name="file_path_temp" id="file_path_temp">

                        
                        {{-- Controller --}}
                        <div class="border rounded-md mb-6 p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                <label class="font-semibold text-sm">No of Controller</label>
                                <div class="md:col-span-3 flex items-center space-x-4">
                                    @foreach(range(1, 5) as $i)
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="no_of_controller" value="{{ $i }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span>{{ $i }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            @foreach(range(1, 5) as $i)
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="controller-row-{{$i}}">
                                <label class="font-semibold text-sm">Controller - {{$i}}</label>
                                <div class="md:col-span-3">
                                    <select name="controller_{{$i}}_id" class="select2-class block w-full border-gray-300 rounded-md shadow-sm">
                                        <option></option>
                                        @foreach ($controllers as $position)
                                            @if ($position->holder)
                                                <option value="{{ $position->holder_id }}">{{ $position->position_title }} - {{ $position->holder->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        {{-- Main Form Section --}}
                        <div class="border rounded-md">
                            <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                <x-input-label for="department" class="font-semibold text-sm text-gray-600" :value="__('Department')" />
                                <div class="col-span-3">
                                    <x-text-input id="department" class="block w-full bg-gray-100" type="text" name="department" :value="$userDepartment" readonly />
                                </div>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div class="p-4 grid grid-cols-4 gap-4 items-center"><x-input-label for="category" class="font-semibold text-sm text-gray-600" :value="__('Category *')" /><div class="col-span-3 flex items-center space-x-2"><x-text-input id="category" class="block w-full" type="text" name="category" :value="old('category')" required readonly /><x-primary-button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'keywords-modal')">...</x-primary-button></div></div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center"><x-input-label for="number" class="font-semibold text-sm text-gray-600" :value="__('Number *')" /><div class="col-span-3"><x-text-input id="number" class="block w-full" type="text" name="number" :value="old('number')" required /></div></div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center"><x-input-label for="document_name" class="font-semibold text-sm text-gray-600" :value="__('Document Name *')" /><div class="col-span-3"><x-text-input id="document_name" class="block w-full" type="text" name="document_name" :value="old('document_name')" required /></div></div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-start"><x-input-label for="reason_of_revision" class="font-semibold text-sm text-gray-600" :value="__('Reason of Revision *')" /><div class="col-span-3"><textarea id="reason_of_revision" name="reason_of_revision" rows="3" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('reason_of_revision') }}</textarea></div></div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center"><x-input-label for="effective_date" class="font-semibold text-sm text-gray-600" :value="__('Effective Date *')" /><div class="col-span-3"><x-text-input id="effective_date" class="block w-full" type="date" name="effective_date" :value="old('effective_date', date('Y-m-d'))" required /></div></div>
                                <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                    <div class="font-semibold text-sm text-gray-600">Creator</div>
                                    <div class="col-span-3">
                                        <x-text-input class="block w-full bg-gray-100" type="text" value="{{ auth()->user()->name }}" readonly />
                                    </div>
                                </div>

                                {{-- Approver --}}
                                <div class="border rounded-md mb-6 p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                        <label class="font-semibold text-sm">Names of Approver</label>
                                        <div class="md:col-span-3 flex items-center space-x-4">
                                            @foreach(range(1, 3) as $i)
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="no_of_approver" value="{{ $i }}" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span>{{ $i }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach(range(1, 3) as $i)
                                    <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="approver-row-{{$i}}">
                                        <label class="font-semibold text-sm">Approver - {{$i}}</label>
                                        <div class="md:col-span-3">
                                            <select name="approver_{{$i}}_id" class="select2-class block w-full border-gray-300 rounded-md shadow-sm">
                                                <option></option>
                                                @foreach ($approvers_list as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->emp_id }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="p-4 grid grid-cols-4 gap-4 items-start">
                                    <x-input-label for="file" class="font-semibold text-sm text-gray-600 pt-2" :value="__('File *')" />
                                    <div class="col-span-3">
                                        <input type="file" name="file" id="file" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Submit as Approved') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="keywords-modal" :show="false" focusable>
        <div class="p-6 light-modal-content" x-data="{ selectedKeyword: '' }">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Select Category') }}</h2>
            <div class="mt-4 space-y-2">
                 <label class="flex items-center p-2 rounded-md hover:bg-gray-100"><input class="keyword-radio rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="radio" name="category_option" value="00 Policy & Principle: PP" x-model="selectedKeyword"><span class="ms-2 text-sm text-gray-600">00 Policy & Principle: PP</span></label>
                 <label class="flex items-center p-2 rounded-md hover:bg-gray-100"><input class="keyword-radio rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="radio" name="category_option" value="10 Management System: MS" x-model="selectedKeyword"><span class="ms-2 text-sm text-gray-600">10 Management System: MS</span></label>
                 <label class="flex items-center p-2 rounded-md hover:bg-gray-100"><input class="keyword-radio rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="radio" name="category_option" value="20 Business & Operation: BO" x-model="selectedKeyword"><span class="ms-2 text-sm text-gray-600">20 Business & Operation: BO</span></label>
                 <label class="flex items-center p-2 rounded-md hover:bg-gray-100"><input class="keyword-radio rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" type="radio" name="category_option" value="90 Others: OT" x-model="selectedKeyword"><span class="ms-2 text-sm text-gray-600">90 Others: OT</span></label>
            </div>
            <div class="mt-6 flex justify-end">
                <x-danger-button type="button" x-on:click="$dispatch('close')">Cancel</x-danger-button>
                <x-primary-button class="ms-3" x-on:click="document.getElementById('category').value = selectedKeyword; $dispatch('close'); window.generateNumber();">OK</x-primary-button>
            </div>
        </div>
    </x-modal>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
        <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
        <script>
            FilePond.registerPlugin(FilePondPluginFileValidateType);
            const inputElement = document.querySelector('input[id="file"]');
            
            const pond = FilePond.create(inputElement, {
                acceptedFileTypes: ['application/pdf'],
                server: {
                    process: {
                        url: '{{ route("company-rules.upload") }}',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        onload: (response) => {
                            const data = JSON.parse(response);
                            document.getElementById('file_path_temp').value = data.path;
                            return response;
                        },
                    },
                    revert: null 
                }
            });
        </script>

        
        <script>
            $(document).ready(function() {
                $('.select2-class').select2({
                    placeholder: 'Choose...',
                    allowClear: true
                });

                $('[id^="controller-row-"]').hide();
                $('[id^="approver-row-"]').hide();

                $('input[name="no_of_controller"]').on('change', function() {
                    var selectedValue = $(this).val();
                    $('[id^="controller-row-"]').hide(); 
                    for (var i = 1; i <= selectedValue; i++) {
                        $('#controller-row-' + i).show(); 
                    }
                    for (var i = parseInt(selectedValue) + 1; i <= 5; i++) {
                        $('select[name="controller_' + i + '_id"]').val(null).trigger('change');
                    }
                });

                $('input[name="no_of_approver"]').on('change', function() {
                    var selectedValue = $(this).val();
                    $('[id^="approver-row-"]').hide(); 
                    for (var i = 1; i <= selectedValue; i++) {
                        $('#approver-row-' + i).show(); 
                    }
                    for (var i = parseInt(selectedValue) + 1; i <= 3; i++) {
                        $('select[name="approver_' + i + '_id"]').val(null).trigger('change');
                    }
                });

                function syncSelectors(selectorPrefix, count) {
                    var selectors = [];
                    for (let i = 1; i <= count; i++) { selectors.push($('select[name="' + selectorPrefix + '_' + i + '_id"]')); }
                    
                    function sync() {
                        var selectedValues = [];
                        selectors.forEach(function(selector) { var val = selector.val(); if (val) { selectedValues.push(val); } });
                        
                        selectors.forEach(function(targetSelector) {
                            var currentValue = targetSelector.val();
                            targetSelector.find('option').prop('disabled', false);
                            selectedValues.forEach(function(valueToDisable) {
                                if (valueToDisable !== currentValue) { 
                                    targetSelector.find('option[value="' + valueToDisable + '"]').prop('disabled', true); 
                                }
                            });
                            targetSelector.trigger('change.select2');
                        });
                    }
                    
                    selectors.forEach(function(selector) { selector.on('change', sync); });
                }

                syncSelectors('controller', 5);
                syncSelectors('approver', 3);

                window.generateNumber = function() {
                    const category = document.getElementById('category').value;
                    if (!category) return;

                    fetch('{{ route("company-rules.getNextNumber") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ category: category })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.next_number) {
                            document.getElementById('number').value = data.next_number;
                        } else {
                            console.error('Error:', data.error || 'Could not retrieve next number.');
                        }
                    })
                    .catch(error => console.error('Fetch Error:', error));
                }

                // Also run on page load if category is already set
                if ($('#category').val()) {
                    window.generateNumber();
                }
            });
        </script>
    @endpush
</x-app-layout>