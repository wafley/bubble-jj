<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'admin.users.index');
    }

    public function data(Request $request)
    {
        $role = Role::where('name', 'user')->firstOrFail();
        $users = User::with('profile', 'role')->where('role_id', $role->id)->orderBy('created_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $users->where('is_active', $request->status);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->editColumn('name', fn($row) => $row->name ?? '-')
            ->addColumn('username', function ($row) {
                $username1 = $row->profile->username_1 ?? '-';
                $username2 = $row->profile->username_2 ?? '-';

                return "<span>{$username1} <br> {$username2}</span>";
            })
            ->addColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->editColumn('created_at', fn($row) => formatDate($row->created_at))
            ->addColumn('action', function ($row) {
                $detailUrl = route('users.show', $row->id);
                return "<a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>";
            })
            ->rawColumns(['username', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::with('profile', 'role')->findOrFail($id);

        if ($user->role->name !== 'user') {
            abort(404);
        }

        $activities = Activity::causedBy($user)
            ->where('log_name', '!=', 'login')
            ->latest()
            ->take(10)
            ->get();

        return spaRender($request, 'admin.users.show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $user = User::with('role')->findOrFail($id);

        if ($user->role->name !== 'user') {
            abort(404);
        }

        return spaRender($request, 'admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::with('profile', 'role')->findOrFail($id);

        if ($user->role->name !== 'user') {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid, hanya user yang dapat diperbarui.'
            ], 400);
        }

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'username_1' => [
                'required',
                'string',
                'max:50',
                Rule::unique('profiles', 'username_1')->ignore($user->profile->id),
                Rule::unique('profiles', 'username_2')->ignore($user->profile->id),
                'different:username_2',
            ],
            'username_2' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('profiles', 'username_1')->ignore($user->profile->id),
                Rule::unique('profiles', 'username_2')->ignore($user->profile->id),
                'different:username_1',
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $username_1 = sanitizeUsername($request->username_1);
        $username_2 = sanitizeUsername($request->username_2);
        $phone    = sanitizePhone($request->phone);

        $user->update([
            'name' => $request->name,
            'phone' => $phone,
            'is_active' => $request->boolean('is_active'),
            'verified_at' => now(),
        ]);

        $user->profile()->update([
            'username_1' => $username_1,
            'username_2' => $username_2,
        ]);

        ActivityLogger::logFor(
            $user,
            "User {$user->id} diperbarui.",
            Auth::user(),
            [
                'updated_fields' => $request->all(),
            ],
            'user_update',
        );

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui.',
            'redirect' => route('users.show', $user->id),
            'redirect_type' => 'spa',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->role->name !== 'user') {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid, hanya user yang dapat dihapus.'
            ], 400);
        }

        $user->delete();

        ActivityLogger::logFor(
            $user,
            "User {$user->id} dihapus.",
            Auth::user(),
            null,
            'user_destroy',
        );

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dihapus.',
            'redirect' => route('users.index'),
            'redirect_type' => 'http',
        ]);
    }
}
