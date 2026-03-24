<!-- Camera Capture Modal -->
<div id="cameraModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Capture Image</h3>
                            <!-- Camera Selector (shown only if multiple cameras available) -->
                            <select id="cameraSelect" class="hidden text-sm px-3 py-1 rounded-md border border-slate-300 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <!-- Options populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mt-2">
                            <video id="camfeed" autoplay playsinline class="w-full rounded-md bg-black"></video>
                            <p id="cam-status" class="text-xs text-slate-500 mt-1 text-center hidden">Starting camera…</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="snap" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Snap</button>
                <button id="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif

<script>
(function() {
    var _camStream = null;
    var _availableCameras = [];
    var _selectedCameraId = localStorage.getItem('selectedCameraId') || null;

    function stopCamStream() {
        if (_camStream) {
            _camStream.getTracks().forEach(function(t) { t.stop(); });
            _camStream = null;
        }
        var video = document.querySelector('#camfeed');
        if (video) video.srcObject = null;
    }

    // Enumerate available cameras
    function enumerateCameras() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            return Promise.resolve([]);
        }
        return navigator.mediaDevices.enumerateDevices()
            .then(function(devices) {
                return devices.filter(function(device) {
                    return device.kind === 'videoinput';
                });
            })
            .catch(function(err) {
                console.error('Error enumerating devices:', err);
                return [];
            });
    }

    // Populate camera selector
    function populateCameraSelector(cameras) {
        var select = document.querySelector('#cameraSelect');
        if (!select) return;

        select.innerHTML = '';
        _availableCameras = cameras;

        cameras.forEach(function(camera, index) {
            var option = document.createElement('option');
            option.value = camera.deviceId;
            option.textContent = camera.label || 'Camera ' + (index + 1);
            select.appendChild(option);
        });

        // Show selector only if multiple cameras
        if (cameras.length > 1) {
            select.classList.remove('hidden');
            // Select previously used camera if available
            if (_selectedCameraId) {
                select.value = _selectedCameraId;
            }
        } else {
            select.classList.add('hidden');
        }
    }

    // Start camera stream with specified device ID
    function startCamera(deviceId) {
        stopCamStream();
        var video = document.querySelector('#camfeed');
        var status = document.getElementById('cam-status');
        
        if (status) { status.textContent = 'Starting camera…'; status.classList.remove('hidden'); }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (status) { status.textContent = 'Camera not supported in this browser.'; }
            return;
        }

        var constraints = { video: { facingMode: 'environment' } };
        if (deviceId) {
            constraints.video = { deviceId: { exact: deviceId } };
        }

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                _camStream = stream;
                video.srcObject = stream;
                video.play();
                if (status) status.classList.add('hidden');
                // Save camera preference
                if (deviceId) {
                    _selectedCameraId = deviceId;
                    localStorage.setItem('selectedCameraId', deviceId);
                }
            })
            .catch(function(err) {
                if (status) { status.textContent = 'Unable to access camera: ' + err.message; }
                console.error('Camera error:', err);
            });
    }

    // Capture frame and inject into file input
    document.querySelector('#snap').addEventListener('click', function() {
        var video = document.querySelector('#camfeed');
        var canvas = document.createElement('canvas');
        canvas.width = video.videoWidth  || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(function(blob) {
            var file = new File([blob], 'snapshot.png', { type: 'image/png' });
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            var fileInput = document.querySelector('#main-document');
            if (fileInput) fileInput.files = dataTransfer.files;
            document.getElementById('cameraModal').classList.add('hidden');
            stopCamStream();
        }, 'image/png');
    });

    // Close/cancel — stop the camera stream
    document.querySelector('#closeModal').addEventListener('click', function() {
        document.getElementById('cameraModal').classList.add('hidden');
        stopCamStream();
    });

    // Handle camera selection change
    var cameraSelect = document.querySelector('#cameraSelect');
    if (cameraSelect) {
        cameraSelect.addEventListener('change', function() {
            startCamera(this.value);
        });
    }

    // Open camera modal and start the stream
    document.querySelector('#btn-opencam').addEventListener('click', function() {
        var modal = document.getElementById('cameraModal');
        modal.classList.remove('hidden');

        // Enumerate cameras and start
        enumerateCameras()
            .then(function(cameras) {
                populateCameraSelector(cameras);
                // Start with previously selected camera or first available
                var deviceIdToUse = _selectedCameraId;
                if (cameras.length > 0 && (!deviceIdToUse || !cameras.find(function(c) { return c.deviceId === deviceIdToUse; }))) {
                    deviceIdToUse = cameras[0].deviceId;
                }
                startCamera(deviceIdToUse || null);
            });
    });
})();
</script>
