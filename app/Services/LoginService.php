<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function handle(array $data): array
    {
        $username = sanitizeUsername($data['username']);
        $phone    = sanitizePhone($data['phone']);
        $remember = $data['remember'] ?? false;

        $user = User::with('role', 'profile')
            ->where('phone', $phone)
            ->where(function ($q) use ($username) {
                $q->whereHas('profile', fn($q2) => $q2->where('username_1', $username))
                    ->orWhereHas('profile', fn($q2) => $q2->where('username_2', $username));
            })
            ->first();

        if (!$user) {
            return $this->handleUnknownUser($username, $phone, $remember);
        }

        if (!$user->role) {
            return [
                'status' => 'error',
                'message' => 'User tidak memiliki role yang valid.',
            ];
        }

        if ($user->role->name !== 'user') {
            session([
                'prelogin_admin' => [
                    'user_id'   => $user->id,
                    'username'  => $username,
                    'phone'     => $phone,
                    'remember'  => $remember,
                    'expires_at' => now()->addHour(),
                ]
            ]);

            return [
                'status'  => 'warning',
                'message' => 'Akun ini adalah akun admin. Silakan masuk menggunakan kata sandi.',
                'redirect' => route('login'),
                'redirect_type' => 'spa',
            ];
        }

        Auth::login($user, $remember);

        ActivityLogger::log(
            "{$user->role->label} {$username} melakukan login",
            $user,
            [
                'ip'         => request()->getClientIp(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
            ],
            'login',
        );

        return [
            'status' => 'success',
            'message' => 'Berhasil masuk!',
            'redirect' => session()->pull('url.intended', route($user->role->redirect ?? 'home')),
            'redirect_type' => 'http',
        ];
    }

    public function prelogin(array $data): array
    {
        if (!isset($data['expires_at']) || now()->greaterThan($data['expires_at'])) {
            session()->forget('prelogin_admin');
            return [
                'status' => 'error',
                'message' => 'Session login admin telah kedaluwarsa.',
            ];
        }

        $user = User::with('role', 'profile')->find($data['user_id']);

        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'User tidak ditemukan.',
            ];
        }

        if (!Hash::check($data['password'], $user->password)) {
            return [
                'status' => 'error',
                'message' => 'Password salah.',
            ];
        }

        Auth::login($user, $data['remember'] ?? false);
        session()->forget('prelogin_admin');

        ActivityLogger::log(
            "{$user->role->label} {$user->profile->username_1} melakukan login sebagai admin",
            $user,
            [
                'ip' => request()->getClientIp(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
            ],
            'login',
        );

        return [
            'status' => 'success',
            'message' => 'Berhasil login sebagai admin!',
            'redirect' => session()->pull('url.intended', route($user->role->redirect ?? 'dashboard')),
            'redirect_type' => 'http',
        ];
    }

    protected function handleUnknownUser(string $username, string $phone, bool $remember)
    {
        $usernameExists = User::whereHas('profile', function ($q) use ($username) {
            $q->where('username_1', $username)
                ->orWhere('username_2', $username);
        })->exists();
        $phoneExists = User::where('phone', $phone)->exists();

        if ($usernameExists && !$phoneExists) {
            return [
                'status'  => 'error',
                'message' => 'Nomor Whatsapp tidak terdaftar!',
            ];
        }

        if (!$usernameExists && $phoneExists) {
            return [
                'status'  => 'error',
                'message' => 'Username tidak terdaftar!',
            ];
        }

        if (!$usernameExists && !$phoneExists) {
            $userRole = Role::where('name', 'user')->first();

            DB::beginTransaction();
            try {
                $newUser = User::create([
                    'phone'   => $phone,
                    'role_id' => $userRole->id,
                ]);

                $newUser->profile()->create([
                    'username_1' => $username
                ]);

                Auth::login($newUser, $remember);

                ActivityLogger::log(
                    "{$newUser->role->label} {$username} melakukan login (akun baru)",
                    $newUser,
                    [
                        'ip'         => request()->getClientIp(),
                        'user_agent' => request()->userAgent(),
                        'session_id' => session()->getId(),
                    ],
                    'login',
                );

                DB::commit();

                return [
                    'status' => 'success',
                    'message' => 'Akun baru berhasil dibuat, berhasil login!',
                    'redirect' => session()->pull('url.intended', route($user->role->redirect ?? 'home')),
                    'redirect_type' => 'http',
                ];
            } catch (\Throwable $e) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => 'Gagal membuat akun baru. Silakan coba lagi.',
                ];
            }
        }

        return [
            'status' => 'error',
            'message' => 'Informasi login tidak cocok dengan akun mana pun.',
        ];
    }
}
