<!-- Modal -->
<div id="cameraModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    {{-- <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-4.553a1 1 0 00-1.414-1.414L13.586 8.586a1 1 0 01-1.414 0L9.414 5.414a1 1 0 00-1.414 1.414L12 10m0 0l-4.553 4.553a1 1 0 001.414 1.414L10.414 13.414a1 1 0 011.414 0l2.172 2.172a1 1 0 001.414-1.414L12 10z" />
                        </svg>
                    </div> --}}
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Capture Image</h3>
                        <div class="mt-2">
                            <video id="camfeed" autoplay class="w-full rounded-md"></video>
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
    document.querySelector('#snap').addEventListener('click', function() {
        var canvas = document.createElement('canvas');
        canvas.width = 300;
        canvas.height = 300;
        var context = canvas.getContext('2d');
        context.drawImage(document.querySelector('#camfeed'), 0, 0, 300, 300);
        canvas.toBlob(function(blob) {
            var file = new File([blob], "snapshot.png", { type: "image/png" });
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.querySelector('#file-input').files = dataTransfer.files;
            alert('Image captured and added to the form.');
            document.getElementById('cameraModal').classList.add('hidden');
        }, 'image/png');
    });

    document.querySelector('#closeModal').addEventListener('click', function() {
        document.getElementById('cameraModal').classList.add('hidden');
    });

    document.querySelector('#btn-opencam').addEventListener('click', function() {
        document.getElementById('cameraModal').classList.remove('hidden');
    });
</script>

<script>
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            var video = document.querySelector('#camfeed');
            video.srcObject = stream;
            video.play();
        });
    } else {
        alert('Camera not found');
    }

    document.querySelector('#snap').addEventListener('click', function() {
        var canvas = document.createElement('canvas');
        canvas.width = 300;
        canvas.height = 300;
        var context = canvas.getContext('2d');
        context.drawImage(document.querySelector('#camfeed'), 0, 0, 300, 300);
        canvas.toBlob(function(blob) {
            var file = new File([blob], "snapshot.png", { type: "image/png" });
            var dataTransfer = new DataTransfer();ss
            dataTransfer.items.add(file);
            document.querySelector('#file-input').files = dataTransfer.files;
            alert('Image captured and added to the form.');
        }, 'image/png');
    });
</script>