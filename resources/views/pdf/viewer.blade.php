<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <link rel="stylesheet" href="{{ asset('viewer/css/pdfjs-viewer.css') }}">
    <link rel="stylesheet" href="{{ asset('viewer/css/pdftoolbar.css') }}">
    <script src="{{ asset('viewer/js/pdfjs-viewer.js') }}"></script>
    
    <script>
        // PDF Viewer implementation
        class PDFViewer {
            constructor(container, options = {}) {
                this.container = container;
                this.options = options;
                this.currentPage = 1;
                this.zoom = 1;
                this.pdf = null;
                this.totalPages = 0;
                this.pageRendering = false;
                this.pageNumPending = null;
                this.canvas = document.createElement('canvas');
                this.ctx = this.canvas.getContext('2d');
                this.container.appendChild(this.canvas);
            }
            
            async loadDocument(url) {
                try {
                    this.pdf = await pdfjsLib.getDocument(url).promise;
                    this.totalPages = this.pdf.numPages;
                    document.getElementById('pagecount').textContent = this.totalPages;
                    this.renderPage(this.currentPage);
                    return this.pdf;
                } catch (error) {
                    console.error('Error loading PDF:', error);
                }
            }
            
            async renderPage(num) {
                this.pageRendering = true;
                try {
                    const page = await this.pdf.getPage(num);
                    
                    const viewport = page.getViewport({ scale: this.zoom });
                    this.canvas.height = viewport.height;
                    this.canvas.width = viewport.width;
                    
                    const renderContext = {
                        canvasContext: this.ctx,
                        viewport: viewport
                    };
                    
                    await page.render(renderContext).promise;
                    this.pageRendering = false;
                    
                    document.getElementById('pageno').value = num;
                    
                    if (this.pageNumPending !== null) {
                        this.renderPage(this.pageNumPending);
                        this.pageNumPending = null;
                    }
                    
                    this.currentPage = num;
                } catch (error) {
                    console.error('Error rendering page:', error);
                    this.pageRendering = false;
                }
            }
            
            queueRenderPage(num) {
                if (this.pageRendering) {
                    this.pageNumPending = num;
                } else {
                    this.renderPage(num);
                }
            }
            
            prev() {
                if (this.currentPage <= 1) return;
                this.queueRenderPage(this.currentPage - 1);
            }
            
            next() {
                if (this.currentPage >= this.totalPages) return;
                this.queueRenderPage(this.currentPage + 1);
            }
            
            first() {
                this.queueRenderPage(1);
            }
            
            last() {
                this.queueRenderPage(this.totalPages);
            }
            
            setZoom(value) {
                if (value === 'in') {
                    this.zoom *= 1.2;
                } else if (value === 'out') {
                    this.zoom /= 1.2;
                } else if (value === 'fit') {
                    this.zoom = 1; 
                } else {
                    this.zoom = parseFloat(value);
                }
                
                document.getElementById('zoomval').value = Math.round(this.zoom * 100) + '%';
                
                this.renderPage(this.currentPage);
            }
            
            scrollToPage(pageNumber) {
                if (pageNumber < 1 || pageNumber > this.totalPages) return;
                this.queueRenderPage(pageNumber);
            }
        }
        
        // Thumbnail viewer implementation
        class PDFThumbnails {
            constructor(container, options = {}) {
                this.container = container;
                this.options = options;
                this.pdf = null;
                this.totalPages = 0;
                this.renderedThumbs = new Set();
            }
            
            async loadDocument(url) {
                try {
                    this.pdf = await pdfjsLib.getDocument(url).promise;
                    this.totalPages = this.pdf.numPages;
                    this.renderThumbnails();
                    return this.pdf;
                } catch (error) {
                    console.error('Error loading PDF for thumbnails:', error);
                }
            }
            
            async renderThumbnails() {
                this.container.innerHTML = '';
                
                for (let i = 1; i <= this.totalPages; i++) {
                    const thumbDiv = document.createElement('div');
                    thumbDiv.className = 'pdfpage';
                    thumbDiv.dataset.page = i;
                    thumbDiv.textContent = i;
                    thumbDiv.onclick = () => {
                        document.querySelectorAll('.thumbnails .pdfpage').forEach(el => {
                            el.classList.remove('selected');
                        });
                        thumbDiv.classList.add('selected');
                        pdfViewer.scrollToPage(i);
                    };
                    
                    this.container.appendChild(thumbDiv);
                    
                    this.renderThumbnail(i, thumbDiv);
                }
            }
            
            async renderThumbnail(pageNum, container) {
                if (this.renderedThumbs.has(pageNum)) return;
                
                try {
                    const page = await this.pdf.getPage(pageNum);
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    const viewport = page.getViewport({ scale: 0.2 });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    await page.render({
                        canvasContext: ctx,
                        viewport: viewport
                    }).promise;
                    
                    container.textContent = '';
                    container.appendChild(canvas);
                    
                    this.renderedThumbs.add(pageNum);
                } catch (error) {
                    console.error('Error rendering thumbnail for page ' + pageNum + ':', error);
                }
            }
            
            setZoom(value) {
                // Implement if needed
            }
        }
    </script>
    
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; margin: 0; padding: 0; overflow: hidden; }
        .pdfviewer { height: 100vh; margin: 0; padding: 0; display: flex; flex-direction: column; }
        .pdfviewer-container { margin: 0; padding: 0; display: flex; overflow: hidden; flex: 1; }

        .thumbnails { width: 150px; background-color: #f0f0f0; transition: all 0.3s ease; }
        .thumbnails.hide { width: 0; overflow: hidden; }
        .thumbnails .pdfpage.selected { border: 2px solid #3498db; border-radius: 2px; }
        
        .maindoc { flex: 1; }
        
        .pdfjs-toolbar { 
            background-color: #f8f9fa; 
            border-bottom: 1px solid #e0e0e0; 
            display: flex;
            align-items: center;
            padding: 4px 8px;
            height: 40px;
        }
        
        .pdfjs-toolbar button, 
        .pdfjs-toolbar a.button { 
            background-color: transparent; 
            border: none; 
            cursor: pointer; 
            padding: 6px; 
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
        }
        
        .pdfjs-toolbar button:hover, 
        .pdfjs-toolbar a.button:hover { 
            background-color: #e9ecef; 
        }
        
        .pdfjs-toolbar button.pushed { 
            background-color: #e9ecef; 
            box-shadow: inset 0 0 3px rgba(0,0,0,0.2);
        }
        
        .pdfjs-toolbar input.pageno {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 4px;
            margin: 0 4px;
        }
        
        .pdfjs-toolbar .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .pdfjs-toolbar .dropdown-value {
            display: flex;
            align-items: center;
            padding: 6px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .pdfjs-toolbar .dropdown-value:hover {
            background-color: #e9ecef;
        }
        
        .pdfjs-toolbar .dropdown-content {
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .pdfjs-toolbar .dropdown-content a {
            color: black;
            padding: 8px 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .pdfjs-toolbar .dropdown-content a i {
            margin-right: 8px;
        }
        
        .pdfjs-toolbar .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .pdfjs-toolbar .v-sep {
            height: 24px;
            width: 1px;
            background-color: #ddd;
            margin: 0 6px;
        }
        
        .pdfjs-toolbar .divider {
            flex-grow: 1;
        }
        
        .pdfpage { 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="pdfviewer">
        <div id="watermark" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); color: rgba(0, 0, 0, 0.4); font-size: 10vw; font-weight: bold; pointer-events: none; z-index: 1000; opacity: 0.8; white-space: nowrap;">OBSOLETE</div>
        <div class="pdfjs-toolbar">
            <!-- Sidebar Icon -->
            <button onclick="togglethumbs(this);" title="Toggle Thumbnails/Sidebar"><i class="material-icons-outlined">view_sidebar</i></button>
            <div class="v-sep"></div>
            
            <!-- Navigation Controls -->
            <button onclick="pdfViewer.prev();" title="Previous Page"><i class="material-icons-outlined">arrow_upward</i></button>
            <div class="v-sep"></div>
            <button onclick="pdfViewer.next();" title="Next Page"><i class="material-icons-outlined">arrow_downward</i></button>
            
            <!-- Page Number Input Box -->
            <input id="pageno" class="pageno" type="number" value="1" min="1" onchange="pdfViewer.scrollToPage(parseInt(this.value))" title="Page Number">
            <span id="pagecount" class="pageno"></span>
            
            <!-- Zoom Controls -->
            <button onclick="setZoom('out')" title="Zoom Out"><i class="material-icons-outlined">remove</i></button>
            <div class="v-sep"></div>
            <button onclick="setZoom('in')" title="Zoom In"><i class="material-icons-outlined">add</i></button>
            
            <!-- Percentage Dropdown -->
            <div class="dropdown">
                <div class="dropdown-value" onclick="this.parentNode.classList.toggle('show');">
                    <span id="zoomval">100%</span>
                    <i class="material-icons-outlined">keyboard_arrow_down</i>
                </div>
                <div class="dropdown-content" onclick="this.parentNode.classList.toggle('show');">
                    <a href="#" onclick='setZoom("width"); return false;'>Fit to width</a>
                    <a href="#" onclick='setZoom("fit"); return false;'>Fit to page</a>
                    <a href="#" onclick='setZoom(1); return false;'>100%</a>
                </div>
            </div>
            
            <!-- Reset View Button -->
            <button onclick="resetView();" title="Reset View"><i class="material-icons-outlined">restart_alt</i></button>
            
            <div class="divider"></div>
            
            <!-- Fullscreen Button -->
            <button onclick="toggleFullscreen();" title="Toggle Fullscreen">
                <i class="material-icons-outlined">fullscreen</i>
            </button>
            
            <!-- Print Icon -->
            <button onclick="printPDF();" title="Print Document">
                <i class="material-icons-outlined">print</i>
            </button>
            
            <!-- Download Icon -->
            <a id="filedownload" class="button" title="Download PDF">
                <i class="material-icons-outlined">file_download</i>
            </a>
            
            <!-- Dropdown Menu -->
            <div class="dropdown">
                <button class="button" onclick="this.parentNode.classList.toggle('show');" title="More Options">
                    <i class="material-icons-outlined">more_horiz</i>
                </button>
                <div class="dropdown-content" onclick="this.parentNode.classList.toggle('show');">
                    <a href="#" onclick='pdfViewer.first(); return false;'>
                        <i class="material-icons-outlined">vertical_align_top</i> 
                        <div>
                            <div>First page</div>
                            <small style="color: #666; font-size: 11px;">Go to the first page</small>
                        </div>
                    </a>
                    <a href="#" onclick='pdfViewer.last(); return false;'>
                        <i class="material-icons-outlined">vertical_align_bottom</i> 
                        <div>
                            <div>Last page</div>
                            <small style="color: #666; font-size: 11px;">Go to the last page</small>
                        </div>
                    </a>
                    <hr style="margin: 4px 0; border: none; border-top: 1px solid #e0e0e0;">
                    <a href="#" onclick='rotateCounterClockwise(); return false;'>
                        <i class="material-icons-outlined">rotate_left</i> 
                        <div>
                            <div>Rotate countrary clockwise</div>
                            <small style="color: #666; font-size: 11px;">Rotate document 90° left</small>
                        </div>
                    </a>
                    <a href="#" onclick='rotateClockwise(); return false;'>
                        <i class="material-icons-outlined">rotate_right</i> 
                        <div>
                            <div>Rotate clockwise</div>
                            <small style="color: #666; font-size: 11px;">Rotate document 90° right</small>
                        </div>
                    </a>
                    <hr style="margin: 4px 0; border: none; border-top: 1px solid #e0e0e0;">
                    <a href="#" onclick='setScrollVertical(); return false;'>
                        <i class="material-icons-outlined">more_vert</i> 
                        <div>
                            <div>Vertical scroll</div>
                            <small style="color: #666; font-size: 11px;">Scroll pages vertically</small>
                        </div>
                    </a>
                    <a href="#" onclick='setScrollHorizontal(); return false;'>
                        <i class="material-icons-outlined">more_horiz</i> 
                        <div>
                            <div>Horizontal scroll</div>
                            <small style="color: #666; font-size: 11px;">Scroll pages horizontally</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="pdfviewer-container">
            <div class="thumbnails pdfjs-viewer hide">
            </div>
            <div class="maindoc pdfjs-viewer">
                <div class="pdfpage placeholder"><p>Loading document...</p></div>
            </div>
        </div>
    </div>
</body>
<script>
    function preparePage() {
        const urlParams = new URLSearchParams(window.location.search);
        const PDFFILE = urlParams.get('doc');
        const isObsolete = urlParams.get('obsolete') === 'true';
        const isRestricted = urlParams.get('restricted') === 'true';

        if (isObsolete) {
            const watermark = document.getElementById('watermark');
            watermark.style.display = 'block';

            if (window.self !== window.top) {
                watermark.style.fontSize = '6rem';
            }

            const downloadButton = document.getElementById('filedownload');
            if (downloadButton) downloadButton.style.display = 'none';

            const printButton = document.querySelector('button[title="Print Document"]');
            if (printButton) printButton.style.display = 'none';

            const copyButton = document.querySelector('button[title="Copy/Export"]');
            if (copyButton) copyButton.style.display = 'none';
        } else if (isRestricted) {
            const downloadButton = document.getElementById('filedownload');
            if (downloadButton) downloadButton.style.display = 'none';

            const printButton = document.querySelector('button[title="Print Document"]');
            if (printButton) printButton.style.display = 'none';
        }

        if (!PDFFILE) {
            document.querySelector('.placeholder p').innerText = 'File PDF not found.';
            return;
        }

        // Main PDF viewer
        let pdfViewer = new PDFjsViewer(document.querySelector('.maindoc'), {
            onZoomChange: function(zoom) {
                document.querySelector('#zoomval').innerText = Math.round(zoom * 100) + '%';
            },
            onActivePageChanged: function(page) {
                let pageno = page.dataset["page"];
                if (document.activeElement !== document.querySelector('#pageno')) {
                    document.querySelector('#pageno').value = pageno;
                }
                pdfThumbnails.setActivePage(pageno);
            },
            renderingScale: 1.5 
        });

        // Load the main document
        pdfViewer.loadDocument(PDFFILE).then(function() {
            document.querySelector('#pageno').max = pdfViewer.getPageCount();
            document.querySelector('#pagecount').innerText = 'of ' + pdfViewer.getPageCount();
            
            // Set up download button
            pdfViewer.pdf.getData().then(function(data) {
                document.querySelector('#filedownload').href = URL.createObjectURL(new Blob([data], {type: 'application/pdf'}));
                document.querySelector('#filedownload').target = '_blank';
                document.querySelector('#filedownload').download = PDFFILE.split('/').pop();
            });
            
            pdfViewer.setZoom(1);
        });

        // Thumbnail viewer
        let pdfThumbnails = new PDFjsViewer(document.querySelector('.thumbnails'), {
            onNewPage: function(page, i) {
                page.addEventListener('click', function() {
                    pdfViewer.scrollToPage(i);
                });
            },
            extraPagesToLoad: 5 
        });
        
        pdfThumbnails.setActivePage = function(pageno) {
            this.$container.find('.pdfpage').removeClass('selected');
            this.$container.find('.pdfpage[data-page="' + pageno + '"]').addClass('selected');
        }.bind(pdfThumbnails);

        pdfThumbnails.loadDocument(PDFFILE).then(() => pdfThumbnails.setZoom('fit'));

        function setZoom(zoom) {
            pdfViewer.setZoom(zoom);
        }
        
        function togglethumbs(el) {
            const thumbnailsContainer = document.querySelector('.thumbnails');
            thumbnailsContainer.classList.toggle('hide');
            el.classList.toggle('pushed');
            
            if (!thumbnailsContainer.classList.contains('hide')) {
                pdfThumbnails.loadDocument(PDFFILE).then(() => {
                    pdfThumbnails.setZoom('fit');
                    if (pdfViewer.currentPage) {
                        pdfThumbnails.setActivePage(pdfViewer.currentPage);
                    }
                });
            }
        }
        
        function resetView() {
            pdfViewer.setZoom(1);
            pdfViewer.scrollToPage(1);
        }
        
        function printPDF() {
            if (pdfViewer.pdf) {
                window.print();
            }
        }
        
        let currentRotation = 0;
        function rotateClockwise() {
            currentRotation = (currentRotation + 90) % 360;
            applyRotation();
        }
        
        function rotateCounterClockwise() {
            currentRotation = (currentRotation - 90) % 360;
            if (currentRotation < 0) currentRotation += 360;
            applyRotation();
        }
        
        function applyRotation() {
            document.querySelectorAll('.maindoc .pdfpage').forEach(page => {
                page.style.transform = `rotate(${currentRotation}deg)`;
                
                if (currentRotation === 90 || currentRotation === 270) {
                    page.style.transformOrigin = 'center center';
                }
            });
        }
        
        function setScrollVertical() {
            document.querySelector('.maindoc').classList.remove('horizontal-scroll');
        }
        
        function setScrollHorizontal() {
            document.querySelector('.maindoc').classList.add('horizontal-scroll');
        }
        
        function toggleFullscreen() {
            const pdfViewer = document.querySelector('.pdfviewer');
            const toolbar = document.querySelector('.pdfjs-toolbar');
            const thumbnails = document.querySelector('.thumbnails');
            
            if (!document.fullscreenElement) {
                if (pdfViewer.requestFullscreen) {
                    pdfViewer.requestFullscreen();
                } else if (pdfViewer.mozRequestFullScreen) { /* Firefox */
                    pdfViewer.mozRequestFullScreen();
                } else if (pdfViewer.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
                    pdfViewer.webkitRequestFullscreen();
                } else if (pdfViewer.msRequestFullscreen) { /* IE/Edge */
                    pdfViewer.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) { /* Firefox */
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) { /* IE/Edge */
                    document.msExitFullscreen();
                }
            }
        }
        
        document.addEventListener('fullscreenchange', updateFullscreenUI);
        document.addEventListener('webkitfullscreenchange', updateFullscreenUI);
        document.addEventListener('mozfullscreenchange', updateFullscreenUI);
        document.addEventListener('MSFullscreenChange', updateFullscreenUI);
        
        function updateFullscreenUI() {
            const toolbar = document.querySelector('.pdfjs-toolbar');
            const thumbnails = document.querySelector('.thumbnails');
            const fullscreenButton = document.querySelector('button[title="Toggle Fullscreen"] i');
            
            if (document.fullscreenElement) {
                if (fullscreenButton) fullscreenButton.textContent = 'fullscreen_exit';
                if (toolbar) toolbar.style.display = 'none';
                if (thumbnails) thumbnails.classList.add('hide');
            } else {
                if (fullscreenButton) fullscreenButton.textContent = 'fullscreen';
                if (toolbar) toolbar.style.display = '';
                if (thumbnails && document.querySelector('button[onclick="togglethumbs(this);"]').classList.contains('pushed')) {
                    thumbnails.classList.remove('hide');
                }
            }
        }

        // Make functions available globally
        window.pdfViewer = pdfViewer;
        window.setZoom = setZoom;
        window.togglethumbs = togglethumbs;
        window.resetView = resetView;
        window.printPDF = printPDF;
        window.rotateClockwise = rotateClockwise;
        window.rotateCounterClockwise = rotateCounterClockwise;
        window.setScrollVertical = setScrollVertical;
        window.setScrollHorizontal = setScrollHorizontal;
        window.toggleFullscreen = toggleFullscreen;
    }
    
    document.addEventListener("DOMContentLoaded", preparePage);
</script>
</html>