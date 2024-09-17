<?php

namespace Heptaaurium\AliexpressImporter\Http\Controllers;

use Heptaaurium\AliexpressImporter\Models\AliExpressImporterToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public function createToken()
    {
        $data = [];
        $data['tokens'] = AliExpressImporterToken::orderBy('id', 'desc')
            ->paginate(20);
        $data['token_url'] = request()->getSchemeAndHttpHost() . '/api/?api_token=';
        return view('aliexpress-importer::create-token', $data);
    }

    public function deleteToken($id)
    {
        $token = AliExpressImporterToken::find($id);
        $token->delete();
        return redirect()->back()->with('success', 'Token deleted successfully.');
    }

    public function storeToken(Request $request)
    {
        $data = [];

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token = null;
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            if (AliExpressImporterToken::where('user_id', $user->id)->where('expires_at', '>', now())->exists()) {
                // Token already exists for this user
                $data['message'] = 'Token already exists for this user';
                $data['status'] = 409; // Conflict status code
                return view('aliexpress-importer::token-result', $data);
            }

            $token = $user->createToken('API Token')->plainTextToken;
        }

        if ($token) {
            $user = Auth::user();
            $data['message'] = 'Authentication successful';
            $data['status'] = 200;
            $data['user'] = $user;
            $data['authorisation'] = [
                'token' => $token,
                'type' => 'Bearer',
                'token_url' => request()->getSchemeAndHttpHost() . '/api/?api_token=' . $token
            ];

            AliExpressImporterToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => \Carbon\Carbon::now()->addDays(30)
            ]);

            return view('aliexpress-importer::token-result', $data);
        }

        $data['message'] = 'Authentication failed';
        $data['status'] = 401;

        return view('aliexpress-importer::token-result', $data);
    }

    public function verifyToken(Request $request)
    {
        return [
            'status' => true
        ];
    }
}
