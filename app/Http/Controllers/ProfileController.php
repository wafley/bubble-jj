<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\DataJJ;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'user') {
            return spaRender($request, 'admin.profile');
        } else {
            $data['videos'] = DataJJ::where('user_id', Auth::id())->where('sts_active', true)->get()->groupBy('display_type');
            return spaRender($request, 'user.profile', $data);
        }
    }

    public function update(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::with('profile')->findOrFail(Auth::id());
            $profile = $user->profile;

            DB::table('profiles')->lockForUpdate()->get();

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => ['nullable', 'string', 'max:15'],
                'username_1' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('profiles', 'username_1')->ignore($profile->id),
                    Rule::unique('profiles', 'username_2')->ignore($profile->id),
                    'different:username_2',
                ],
                'username_2' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('profiles', 'username_1')->ignore($profile->id),
                    Rule::unique('profiles', 'username_2')->ignore($profile->id),
                    'different:username_1',
                ],
                'password' => 'nullable|string|min:8',
            ]);

            $updateData = [
                'name'  => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ];

            // kalau password ada di request, update juga
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            $profile->update([
                'username_1' => $validated['username_1'] ?? null,
                'username_2' => $validated['username_2'] ?? null,
            ]);

            ActivityLogger::logFor(
                $user,
                "Profile user {$user->id} diperbarui.",
                Auth::user(),
                [
                    'updated_fields' => array_keys($updateData),
                    'profile' => [
                        'username_1' => $validated['username_1'] ?? null,
                        'username_2' => $validated['username_2'] ?? null,
                    ],
                ],
                logName: 'profile_updated',
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Profil berhasil diperbarui!',
            ]);
        });
    }

    public function changePicture(Request $request, $slot)
    {
        $user = User::with('profile')->findOrFail(Auth::id());
        $profile = $user->profile;

        if (!in_array($slot, [1, 2, 3, 4])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slot tidak valid.'
            ], 422);
        }

        $request->validate([
            'picture' => 'required|image'
        ]);

        $column = 'picture_' . $slot;
        $oldPicture = $profile->{$column};

        if ($oldPicture && Storage::disk('public')->exists('profiles/' . $oldPicture)) {
            Storage::disk('public')->delete('profiles/' . $oldPicture);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('picture'))->encode(new JpegEncoder(quality: 75));

        $filename = uniqid() . '.jpg';
        Storage::disk('public')->put('profiles/' . $filename, (string) $image);

        $profile->{$column} = $filename;
        $profile->save();

        ActivityLogger::logFor(
            $profile,
            "Foto Profil user {$user->id} diperbarui (slot {$slot})",
            Auth::user(),
            [
                'old_picture' => $oldPicture,
                'new_picture' => $filename,
            ],
            'profile_picture'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Foto berhasil diperbarui',
        ]);
    }
}
