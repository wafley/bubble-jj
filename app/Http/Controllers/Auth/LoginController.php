<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\LoginService;

class LoginController extends Controller
{
    protected $loginService;
    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function loginView(Request $request)
    {
        $isAdmin = session('prelogin_admin');
        if ($isAdmin && isset($isAdmin['expires_at']) && now()->lt($isAdmin['expires_at'])) {
            return spaRender($request, 'auth.prelogin', [
                'prefill' => $isAdmin,
            ]);
        }

        session()->forget('prelogin_admin');
        return spaRender($request, 'auth.login');
    }

    public function login(Request $request)
    {
        $isAdmin = session('prelogin_admin');

        if ($isAdmin) {
            $key = 'prelogin:' . $isAdmin['user_id'] . ':' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.'
                ], 429);
            }

            $request->validate([
                'password' => 'required|string',
            ]);

            $data = array_merge($isAdmin, $request->only('password'));

            $response = $this->loginService->prelogin($data);

            RateLimiter::hit($key, $this->decayMinutes * 60);
        } else {
            $request->validate([
                'username' => 'required|string',
                'phone' => 'required|string',
            ]);

            $response = $this->loginService->handle($request->all());
        }

        return response()->json($response);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
