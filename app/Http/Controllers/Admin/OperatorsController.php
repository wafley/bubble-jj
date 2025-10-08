<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class OperatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'admin.operators.index');
    }

    public function data(Request $request)
    {
        $role = Role::where('name', 'operator')->firstOrFail();
        $operators = User::with('role')->where('role_id', $role->id)->orderBy('created_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $operators->where('is_active', $request->status);
        }

        return DataTables::of($operators)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->editColumn('created_at', fn($row) => formatDate($row->created_at))
            ->addColumn('action', function ($row) {
                $detailUrl = route('operators.show', $row->id);
                return "<a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>";
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return spaRender($request, 'admin.operators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('profiles', 'username_1'),
                Rule::unique('profiles', 'username_2'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $username = sanitizeUsername($request->username);
        $phone    = sanitizePhone($request->phone);

        $role = Role::where('name', 'operator')->firstOrFail();
        $operator = User::create([
            'name' => $request->name,
            'phone' => $phone,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $operator->profile()->create([
            'username_1' => $username
        ]);

        ActivityLogger::logFor(
            $operator,
            'Operator ditambahkan',
            Auth::user(),
            [
                'name' => $operator->name,
                'username' => $username,
            ],
            'operator_created'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Operator berhasil ditambahkan.',
            'redirect' => 'back',
            'redirect_type' => 'history',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $operator = User::with('role')->findOrFail($id);

        if ($operator->role->name !== 'operator') {
            abort(404);
        }

        $activities = Activity::causedBy($operator)
            ->where('log_name', '!=', 'login')
            ->latest()
            ->take(10)
            ->get();

        return spaRender($request, 'admin.operators.show', compact('operator', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $operator = User::with('role')->findOrFail($id);

        if ($operator->role->name !== 'operator') {
            abort(404);
        }

        return spaRender($request, 'admin.operators.edit', compact('operator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $operator = User::with('profile', 'role')->findOrFail($id);

        if ($operator->role->name !== 'operator') {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid, hanya operator yang dapat diperbarui.'
            ], 400);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('profiles', 'username_1')->ignore($operator->profile->id),
                Rule::unique('profiles', 'username_2')->ignore($operator->profile->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $username = sanitizeUsername($request->username);
        $phone    = sanitizePhone($request->phone);

        $operator->update([
            'name' => $request->name,
            'phone' => $phone,
            'password' => $request->filled('password')
                ? Hash::make($request->password)
                : $operator->password,
            'is_active' => $request->boolean('is_active'),
            'verified_at' => now(),
        ]);

        $operator->profile()->update([
            'username_1' => $username,
        ]);

        ActivityLogger::logFor(
            $operator,
            'Operator diperbarui',
            Auth::user(),
            $request->except(['password', 'password_confirmation']),
            'operator_update'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Operator berhasil diperbarui.',
            'redirect' => route('operators.show', $operator->id),
            'redirect_type' => 'spa',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $operator = User::findOrFail($id);

        if ($operator->role->name !== 'operator') {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid, hanya operator yang dapat dihapus.'
            ], 400);
        }

        $operator->delete();

        ActivityLogger::logFor(
            $operator,
            'Operator dihapus',
            Auth::user(),
            null,
            'operator'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Operator berhasil dihapus.',
            'redirect' => route('operators.index'),
            'redirect_type' => 'http',
        ]);
    }
}
