<?php

namespace App\Http\Controllers;

use App\Models\RoomJob;
use App\Models\RoomArtifact;
use App\Jobs\ProcessRoomJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomJobController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png|max:' . ((int)config('room-ai.max_image_mb', 10) * 1024)]);
        $id = (string)Str::uuid();
        $path = $request->file('image')->store('uploads', 'public');
        $job = RoomJob::create(
            [
                'id' => $id, 
                'status' => 'queued', 
                'progress' => 0, 
                'original_path' => $path, 
                'meta' => 
                [
                    'original_name' => $request->file('image')->getClientOriginalName()
                ]
            ]
    );
        ProcessRoomJob::dispatch($id);
        return response()->json(['job_id' => $id, 'status' => 'queued'], 202);
    }
    public function show(RoomJob $job)
    {
        return response()->json(
            ['job_id' => $job->id,
             'status' => $job->status, 
             'progress' => $job->progress, 
             'error' => $job->error_message, 
             'artifacts' => $job->artifacts->map(fn($a) => 
             [
                'id' => $a->id, 
                'type' => $a->type, 
                'url' => url('/api/v1/jobs/' . $job->id . '/artifacts/' . $a->id)
                ]
                )
            ]
            );
    }
    public function artifact(RoomJob $job, RoomArtifact $artifact)
    {
        abort_unless($artifact->room_job_id === $job->id, 404);
        return Storage::disk('public')->download($artifact->path, basename($artifact->path), 
        [
            'Content-Type' => $artifact->mime_type ?? 'application/octet-stream'
        ]);
    }
}
