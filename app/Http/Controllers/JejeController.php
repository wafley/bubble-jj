<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\DataJJ;
use App\Models\UploadService;
use App\Services\JejeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class JejeController extends Controller
{
    protected $jejeService;

    public function __construct(JejeService $jejeService)
    {
        $this->jejeService = $jejeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $request->input('username');
        $results = null;

        if ($query) {
            $results = DataJJ::with('user.profile')
                ->where(function ($q) use ($query) {
                    $q->where('username_1', 'like', "%{$query}%")
                        ->orWhere('username_2', 'like', "%{$query}%");
                })
                ->where('sts_active', true)
                ->paginate(10)
                ->withQueryString();
        }

        if ($user && $user->role->name !== 'user') {
            return spaRender($request, 'jeje.index', compact('results'));
        } else {
            return spaRender($request, 'jeje.search', compact('results'));
        }
    }

    public function data(Request $request)
    {
        $videos = DataJJ::with('user.profile')->latest('created_at');

        if ($request->status !== null && $request->status !== '') {
            $videos->where('sts_active', $request->status);
        }

        return DataTables::of($videos)
            ->addIndexColumn()
            ->editColumn('owner', function ($video) {
                $username = $video->user->profile->username_1 ?? '-';
                $phone = $video->user->phone ?? '-';
                return "<strong>{$username}</strong><br><small class='text-primary'>{$phone}</small>";
            })
            ->editColumn('info', function ($row) {
                $duration = gmdate("i:s", $row->duration);
                $size = formatSize($row->size);
                return "{$duration}<br>{$size}";
            })
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($row) {
                $showUrl = asset('storage/jj/' . $row->filename);
                $showBtn = "<a href='{$showUrl}' class='btn btn-sm btn-primary my-1' target='_blank'>Preview</a>";

                $toggleUrl = route('jeje.update', $row->id);
                $toggleText = $row->sts_active ? 'Nonaktifkan' : 'Aktifkan';
                $toggleColor = $row->sts_active ? 'danger' : 'success';
                $toggleBtn = "<button type='button' class='btn btn-sm btn-outline-{$toggleColor} my-1 btn-toggle' data-url='{$toggleUrl}'>{$toggleText}</button>";

                return $showBtn . "<br>" . $toggleBtn;
            })
            ->filterColumn('owner', function ($query, $keyword) {
                $query->whereHas('user.profile', function ($q) use ($keyword) {
                    $q->where('username_1', 'like', "%{$keyword}%")
                        ->orWhere('username_2', 'like', "%{$keyword}%");
                })
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('phone', 'like', "%{$keyword}%");
                    });
            })
            ->rawColumns(['owner', 'info', 'status', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $service = UploadService::where('slug', 'free')->firstOrFail();

        $data = [
            'service' => $service,
        ];

        return spaRender($request, 'jeje.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimetypes:video/*|max:5120',
            'display_type' => 'nullable|in:10,20,30,99',
        ]);

        return $this->jejeService->upload($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $video = DataJJ::findOrFail($id);
        return spaRender($request, 'jeje.show', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $video = DataJJ::findOrFail($id);
        return spaRender($request, 'jeje.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $video = DataJJ::findOrFail($id);
        $user = Auth::user();

        if ($user->role->name !== 'user') {
            $video->sts_active = !$video->sts_active;
            $video->save();

            ActivityLogger::logFor(
                $video,
                $video->sts_active ? "Video diaktifkan" : "Video dinonaktifkan",
                $user,
                null,
                'jeje_update'
            );

            return response()->json([
                'status'  => 'success',
                'message' => $video->sts_active ? 'Video diaktifkan.' : 'Video dinonaktifkan.',
                'redirect_type' => 'reload'
            ]);
        } else {
            if ($video->user_id !== $user->id) {
                return response()->json(['status' => 'error', 'message' => 'Kamu tidak bisa mengubah JJ milik orang lain.'], 403);
            }

            $validated = $request->validate([
                'file' => 'required|file|mimetypes:video/*|max:5120',
                'display_type' => 'nullable|in:10,20,30,99',
            ]);

            ActivityLogger::logFor(
                $video,
                "User melakukan update video JJ",
                $user,
                ['filename' => $validated['file']->getClientOriginalName()],
                'jeje_update'
            );

            return $this->jejeService->upload($validated);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $video = DataJJ::findOrFail($id);

        if ($video->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Kamu tidak bisa menghapus JJ milik orang lain.'], 403);
        }

        Storage::disk('public')->delete('jj/' . $video->filename);
        $video->delete();

        ActivityLogger::logFor(
            $video,
            "Video JJ dihapus",
            Auth::user(),
            null,
            'jeje_destroy'
        );

        return response()->json([
            'status'        => 'success',
            'message'       => 'Video JJ berhasil dihapus.',
            'redirect'      => 'back',
            'redirect_type' => 'history',
        ]);
    }
}
