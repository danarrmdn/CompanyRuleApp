@props(['rule'])

@if ($rule->file_path)
    <div class="mt-6 pt-4 border-t">
        <dt class="font-bold mb-2">File PDF</dt>

        <div id="pdf-viewer-container" class="border rounded-lg relative" style="height: 80vh; overflow: hidden;">
            <!-- PDF.js viewer iframe will be injected here by the script -->
        </div>

        <div id="pdf-viewer-data"
             data-file-url="{{ route('rules.file.show', ['id' => $rule->id, 'v' => $rule->updated_at->timestamp]) }}"
             data-viewer-url="{{ route('pdf.viewer') }}"
             data-is-obsolete="{{ $rule->is_obsolete ? 'true' : 'false' }}">
        </div>
    </div>

<style>
.pdf-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    color: rgba(0, 0, 0, 0.2); 
    font-size: 8rem;
    font-weight: bold;
    font-style: italic; 
    pointer-events: none; 
    z-index: 10;
    letter-spacing: 0.1em; 
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); 
}
</style>

<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        const viewerDataEl = document.getElementById('pdf-viewer-data');
        if (!viewerDataEl) return; 

        const viewerContainer = document.getElementById('pdf-viewer-container');
        const fileUrl = viewerDataEl.dataset.fileUrl;
        const isObsolete = viewerDataEl.dataset.isObsolete === 'true';
        const viewerUrl = viewerDataEl.dataset.viewerUrl + '?doc=' + encodeURIComponent(fileUrl) + '&obsolete=' + isObsolete;

        // Create and append the iframe that contains the PDF viewer
        const iframe = document.createElement('iframe');
        iframe.src = viewerUrl;
        iframe.width = '100%';
        iframe.height = '100%';
        iframe.title = 'PDF Viewer';
        iframe.style.border = 'none';
        viewerContainer.appendChild(iframe);

        if (isObsolete) {
            const watermark = document.createElement('div');
            watermark.className = 'pdf-watermark';
            watermark.textContent = 'OBSOLETE';
            viewerContainer.appendChild(watermark);
        }

    });
</script>

@else
    <div class="mt-6 pt-4 border-t">
        <dt class="font-bold mb-2">File PDF</dt>
        <p class="text-gray-500 mt-2">No file available.</p>
    </div>
@endif
