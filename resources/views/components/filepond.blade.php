<div>
    <input
        type="file"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="filepond"
    />
    
    <!-- Hidden input to store the temporary file path -->
    <input type="hidden" name="file_path_temp" id="file_path_temp" value="">
    
    @once
    @push('scripts')
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FilePond.registerPlugin(FilePondPluginFileValidateType);
            
            // Initialize FilePond on all elements with class 'filepond'
            document.querySelectorAll('.filepond').forEach(function(element) {
                const pond = FilePond.create(element, {
                    acceptedFileTypes: ['application/pdf'],
                    labelIdle: 'Drag & Drop your PDF or <span class="filepond--label-action">Browse</span>',
                    server: {
                        process: {
                            url: '{{ route("company-rules.upload") }}',
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            onload: (response) => {
                                try {
                                    // Store the file path in a hidden input
                                    const data = JSON.parse(response);
                                    document.getElementById('file_path_temp').value = data.path;
                                    console.log('File uploaded successfully, path:', data.path);
                                    return response;
                                } catch (error) {
                                    console.error('Error processing upload response:', error);
                                    return response;
                                }
                            },
                        },
                        revert: null
                    }
                });
            });
        });
    </script>
    @endpush
    @endonce

    @once
    @push('styles')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    @endpush
    @endonce
</div>