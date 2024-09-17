@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container mx-auto bg-white m-0 p-0 w-100">
        <div class="flex justify-center items-center h-screen">
            @if ($status !== 200)
                <div class="w-full max-w-md">
                    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                        <div class="mx-auto max-w-screen-sm text-center">
                            <i class="ti ti-face-sad text-7xl"></i>
                            <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl">
                                {{ $status }}</h1>
                            <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl ">
                                {{ $message }}</p>
                            <a href="create-token" class="mb-4 font-medium text-blue-600 hover:underline">Try
                                again</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="w-full max-w-md">
                    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                        <div class="mx-auto max-w-screen-sm text-center">
                            <p class="mb-4 text-4xl tracking-tight font-bold text-gray-900 md:text-4xl">
                                Use this token to validate your requests</p>
                            <h1 class="mb-4 text-lg tracking-tight font-extrabold lg:text-9xl text-red-500">
                                {{ $authorisation['token_url'] }}</h1>
                            <div class="flex justify-center mt-8"></div>
                            <button id="copyButton" class="btn btn-primary">
                                Copy to Clipboard
                            </button>
                        </div>
                    </div>
            @endif
        </div>
    </div>
    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            const token = "{{ $authorisation['token_url'] ?? '' }}";
            navigator.clipboard.writeText(token)
                .then(() => {
                    alert("Token copied to clipboard!");
                })
                .catch((error) => {
                    console.error("Failed to copy token to clipboard:", error);
                });
        });
    </script>
@endsection
