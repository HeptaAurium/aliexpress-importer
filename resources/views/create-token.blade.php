@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container mx-auto">
        <div class="flex justify-center items-center h-screen">

            <div class="w-full max-w-md bg-white shadow-lg mb-5 rounded-lg p-5">
                <h1 class="text-3xl font-bold mb-6">Create Token for the Shawika Chrome Extension</h1>
                <form action="/aliexpress-importer/create-token" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="text" name="email" id="email"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                        <input type="password" name="password" id="password"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="btn btn-primary">Generate
                            Token</button>
                    </div>
                </form>
            </div>
            <div class="w-full max-w-md bg-white shadow rounded-lg my-4">
                <table class="w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">TOKEN</th>
                            <th class="py-2 px-4 border-b">USER</th>
                            <th class="py-2 px-4 border-b">STATUS</th>
                            <th class="py-2 px-4 border-b">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tokens as $token)
                            <tr>
                                <td class="py-2 px-4 border-b text-sm">
                                    {{ $token_url . $token->token }}
                                    <button class="btn btn-sm btn-secondary ml-auto"
                                        onclick="copyToken('{{ $token_url . $token->token }}')">Copy</button>
                                </td>
                                <td class="py-2 px-4 border-b">{{ $token->user->email }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if (now() < $token->expires_at)
                                        Expires in {{ now()->diff($token->expires_at)->format('%d days') }}
                                    @else
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Expired
                                            {{ now()->diff($token->expires_at)->format('%d days') }} ago</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <form action="/aliexpress-importer/delete-token/{{ $token->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty <tr>
                                <td class="py-2 px-4 border-b" colspan="3">No tokens found.</td>
                            </tr>
                        @endforelse
                </table>
            </div>
        </div>
    </div>
    <script>
        const copyToken = (token) => {
            navigator.clipboard.writeText(token)
                .then(() => {
                    alert("Token copied to clipboard!");
                })
                .catch((error) => {
                    console.error("Failed to copy token to clipboard:", error);
                });
        };
    </script>
@endsection
