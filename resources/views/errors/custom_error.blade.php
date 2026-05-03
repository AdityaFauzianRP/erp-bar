<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="text-center p-8 bg-white shadow-xl rounded-lg max-w-lg">
        <div class="mb-4 text-red-500">
            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 17c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">System Error, Waduh, ada masalah teknis!</h1>
        <p class="text-gray-600 mb-6">Sistem sedang mengalami kendala. Tim kami akan segera memperbaikinya.</p>
        
        <div class="flex flex-col gap-3">
            <a href="/" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">Kembali ke Beranda</a>
            @if(config('app.debug'))
                <button onclick="showSecretError()" class="text-xs text-gray-400 hover:underline">Show Detail Error</button>
            @endif
        </div>
    </div>
    <div id="ignition-container" class="hidden fixed inset-0 z-50 bg-white">
        <iframe id="error-frame" class="w-full h-full border-none"></iframe>
        <button onclick="hideSecretError()" class="fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-[60]">Close Detail</button>
    </div>

    <script>
        function showSecretError() {
            document.getElementById('ignition-container').classList.remove('hidden');
            document.getElementById('error-frame').src = window.location.href + '?show_technical_details=1';
        }
        function hideSecretError() {
            document.getElementById('ignition-container').classList.add('hidden');
        }
    </script>
</body>
</html>