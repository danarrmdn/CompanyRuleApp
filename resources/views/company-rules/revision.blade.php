<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Revise Document') }}
            </h2>
            <div>
                <a href="{{ route('company-rules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="" id="revision-form" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Document Selector -->
                        <div class="border rounded-md mb-6 p-4 space-y-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                <label for="rule_to_revise" class="font-semibold text-sm">Select Document *</label>
                                <div class="md:col-span-3">
                                    <select id="rule_to_revise" class="select2-class block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Choose a document --</option>
                                        @foreach($revisableRules as $rule)
                                            <option value="{{ $rule->id }}">{{ $rule->document_name }} (Version: {{ $rule->version }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                <label class="font-semibold text-sm">Version</label>
                                <div class="md:col-span-3">
                                    <x-text-input id="new_version" class="block w-full bg-gray-200" type="text" name="new_version" readonly />
                                </div>
                            </div>
                        </div>

                        <fieldset id="revision-fieldset" disabled>
                            <!-- Controller -->
                            <div class="border rounded-md mb-6 p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                    <label class="font-semibold text-sm">No of Controller</label>
                                    <div class="md:col-span-3 flex items-center space-x-4">
                                        @foreach(range(1, 5) as $i)
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="no_of_controller" value="{{ $i }}" class="rounded-full border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                            <span>{{ $i }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                
                                @foreach(range(1, 5) as $i)
                                <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="controller-row-{{$i}}" style="display: none;">
                                    <label class="font-semibold text-sm">Controller - {{$i}}</label>
                                    <div class="md:col-span-3">
                                        <select name="controller_{{$i}}_id" id="controller_{{$i}}_id" class="select2-dynamic block w-full border-gray-300 rounded-md shadow-sm">
                                            <option value=""></option>
                                            @foreach ($controllers as $position)
                                                <option value="{{ $position->holder_id }}">{{ $position->position_title }} - {{ $position->holder->name ?? 'N/A' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Main Form Section -->
                            <div class="border rounded-md">
                                <div class="divide-y divide-gray-200">
                                    <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                        <x-input-label for="category" class="font-semibold text-sm text-gray-600" :value="__('Category *')" />
                                        <div class="col-span-3">
                                            <x-text-input id="category" class="block w-full" type="text" name="category" required />
                                        </div>
                                    </div>
                                    <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                        <x-input-label for="number" class="font-semibold text-sm text-gray-600" :value="__('Number *')" />
                                        <div class="col-span-3">
                                            <x-text-input id="number" class="block w-full" type="text" name="number" required />
                                        </div>
                                    </div>
                                    <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                        <x-input-label for="document_name" class="font-semibold text-sm text-gray-600" :value="__('Document Name *')" />
                                        <div class="col-span-3">
                                            <x-text-input id="document_name" class="block w-full" type="text" name="document_name" required />
                                        </div>
                                    </div>
                                    <div class="p-4 grid grid-cols-4 gap-4 items-start">
                                        <x-input-label for="reason_of_revision" class="font-semibold text-sm text-gray-600" :value="__('Reason of Revision *')" />
                                        <div class="col-span-3">
                                            <textarea id="reason_of_revision" name="reason_of_revision" rows="3" class="block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm" required></textarea>
                                        </div>
                                    </div>
                                    <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                        <x-input-label for="effective_date" class="font-semibold text-sm text-gray-600" :value="__('Effective Date *')" />
                                        <div class="col-span-3">
                                            <x-text-input id="effective_date" class="block w-full" type="date" name="effective_date" required />
                                        </div>
                                    </div>
                                    
                                    <!-- Approver -->
                                    <div class="p-4">
                                        <div class="border rounded-md p-4 space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-4 items-center">
                                                <label class="font-semibold text-sm">Names of Approver</label>
                                                <div class="md:col-span-3 flex items-center space-x-4">
                                                    @foreach(range(1, 3) as $i)
                                                    <label class="flex items-center space-x-2">
                                                        <input type="radio" name="no_of_approver" value="{{ $i }}" class="rounded-full border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                                        <span>{{ $i }}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            @foreach(range(1, 3) as $i)
                                            <div class="grid grid-cols-1 md:grid-cols-4 items-center" id="approver-row-{{$i}}" style="display: none;">
                                                <label class="font-semibold text-sm">Approver - {{$i}}</label>
                                                <div class="md:col-span-3">
                                                    <select name="approver_{{$i}}_id" id="approver_{{$i}}_id" class="select2-dynamic block w-full border-gray-300 rounded-md shadow-sm">
                                                        <option value=""></option>
                                                        @foreach ($approvers_list as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->emp_id }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="p-4 grid grid-cols-4 gap-4 items-center">
                                        <x-input-label for="file" class="font-semibold text-sm text-gray-600" :value="__('Upload New PDF File *')" />
                                        <div class="col-span-3 flex flex-col items-center">
                                            <div id="view-file-container" style="display: none;" class="mb-2">
                                                <button type="button" id="view-pdf-btn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-800">
                                                    View Current File
                                                </button>
                                            </div>
                                            <div class="w-full">
                                                <x-filepond name="file" required />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 pt-6 border-t">
                                <input type="hidden" name="is_draft" id="is-draft-input" value="0">
                                <x-danger-button type="button" class="me-4" onclick="document.getElementById('is-draft-input').value = '1'; document.getElementById('revision-form').submit();">
                                    {{ __('Save Revision as Draft') }}
                                </x-danger-button>
                                <x-primary-button>
                                    {{ __('Submit Revision') }}
                                </x-primary-button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF Viewer Modal --}}
    <div id="pdf-modal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-11/12 h-5/6 max-w-6xl">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="pdf-modal-title" class="text-lg font-medium"></h3>
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
    $(document).ready(function() {
        const ruleSelector = document.getElementById('rule_to_revise');
        const form = document.getElementById('revision-form');
        const fieldset = document.getElementById('revision-fieldset');
        
        // PDF Modal Elements
        const viewPdfBtn = document.getElementById('view-pdf-btn');
        const pdfModal = document.getElementById('pdf-modal');
        const closePdfModal = document.getElementById('close-pdf-modal');
        const pdfIframe = document.getElementById('pdf-iframe');
        const pdfModalTitle = document.getElementById('pdf-modal-title');
        const viewFileContainer = document.getElementById('view-file-container');

        function initializeSelect2(selector) {
            $(selector).each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        placeholder: 'Choose...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        }

        // Initializations
        initializeSelect2('#rule_to_revise');

        // PDF Modal Handlers
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

        // Event handlers for radio buttons
        $('input[name="no_of_controller"]').change(function() {
            var selectedValue = parseInt($(this).val());
            $('[id^="controller-row-"]').hide();
            for (var i = 1; i <= selectedValue; i++) {
                var row = $('#controller-row-' + i);
                row.css('display', 'grid');
                initializeSelect2(row.find('.select2-dynamic'));
            }
        });

        $('input[name="no_of_approver"]').change(function() {
            var selectedValue = parseInt($(this).val());
            $('[id^="approver-row-"]').hide();
            for (var i = 1; i <= selectedValue; i++) {
                var row = $('#approver-row-' + i);
                row.css('display', 'grid');
                initializeSelect2(row.find('.select2-dynamic'));
            }
        });

        // Main handler for document selection
        $(ruleSelector).change(async function () {
            const ruleId = this.value;

            if (!ruleId) {
                form.action = '';
                fieldset.disabled = true;
                viewFileContainer.style.display = 'none';
                document.getElementById('new_version').value = '';
                $('#revision-fieldset input[type=text], #revision-fieldset textarea').val('');
                $('.select2-dynamic').val(null).trigger('change');
                $('input[type=radio]').prop('checked', false);
                $('[id^=controller-row-]').hide();
                $('[id^=approver-row-]').hide();
                return;
            }

            form.action = `{{ url('company-rules') }}/${ruleId}/revise`;

            try {
                const response = await fetch(`{{ url('internal-api/company-rules') }}/${ruleId}`);
                if (!response.ok) throw new Error('Failed to fetch document data.');
                const data = await response.json();

                // Handle View Current File button
                if (data.file_path) {
                    viewFileContainer.style.display = 'block';
                    viewPdfBtn.onclick = function() {
                        const pdfUrl = `{{ url('rules-file') }}/${data.id}`;
                        const viewerUrl = `{{ route('pdf.viewer') }}?doc=${encodeURIComponent(pdfUrl)}`;
                        pdfIframe.src = viewerUrl;
                        pdfModalTitle.textContent = data.document_name;
                        pdfModal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    };
                } else {
                    viewFileContainer.style.display = 'none';
                }

                // Reset sections before populating
                $('input[name=no_of_controller], input[name=no_of_approver]').prop('checked', false);
                $('[id^=controller-row-], [id^=approver-row-]').hide();
                $('.select2-dynamic').val(null).trigger('change');

                // Populate simple fields
                document.getElementById('new_version').value = (data.version || 1) + 1;
                document.getElementById('category').value = data.category || '';
                document.getElementById('number').value = data.number || '';
                document.getElementById('document_name').value = data.document_name || '';
                document.getElementById('reason_of_revision').value = ''; // Reason is always new
                document.getElementById('effective_date').value = data.effective_date || '';

                // Populate Controllers
                const controllerIds = [data.controller_1_id, data.controller_2_id, data.controller_3_id, data.controller_4_id, data.controller_5_id].filter(id => id !== null);
                const numControllers = controllerIds.length;
                if (numControllers > 0) {
                    $(`input[name=no_of_controller][value=${numControllers}]`).prop('checked', true).trigger('change');
                    controllerIds.forEach((id, index) => {
                        $(`#controller_${index + 1}_id`).val(id).trigger('change');
                    });
                }

                // Populate Approvers
                const approverIds = [data.approver_1_id, data.approver_2_id, data.approver_3_id].filter(id => id !== null);
                const numApprovers = approverIds.length;
                if (numApprovers > 0) {
                    $(`input[name=no_of_approver][value=${numApprovers}]`).prop('checked', true).trigger('change');
                    approverIds.forEach((id, index) => {
                        $(`#approver_${index + 1}_id`).val(id).trigger('change');
                    });
                }

                fieldset.disabled = false;

            } catch (error) {
                console.error('Error fetching rule data:', error);
                fieldset.disabled = true;
                alert('Failed to load document data. Please try again.');
            }
        });
    });
</script>
@endpush
</x-app-layout>