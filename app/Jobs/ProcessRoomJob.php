<?php

namespace App\Jobs;

use App\Models\RoomJob as RoomJobModel;
use App\Models\RoomArtifact;
use App\Services\Ai\AiPipelineInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessRoomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public int $backoff = 10;
    public function __construct(public string $jobId) {}
    public function handle(AiPipelineInterface $pipeline): void
    {
        $job = RoomJobModel::findOrFail($this->jobId);
        $job->update(['status' => 'processing', 'progress' => 5]);
        try {
            $dir = 'rooms/' . $job->id;
            $arts = $pipeline->run($job->original_path, $dir, function ($p, $stage) use ($job) {
                $job->update(['progress' => $p, 'status' => $stage === 'completed' ? 'completed' : 'processing']);
            });
            foreach ($arts as $type => $path) {
                RoomArtifact::create(
                    [
                        'room_job_id' => $job->id,
                        'type' => $type,
                        'path' => $path,
                        'mime_type' => str_ends_with($path, '.json') ? 'application/json' : 'image/jpeg'
                    ]
                );
            }
            $job->update(
                [
                    'status' => 'completed',
                    'progress' => 100
                ]
            );
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }
}
