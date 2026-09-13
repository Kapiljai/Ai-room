<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class HttpAiPipeline implements AiPipelineInterface
{
  public function run(string $inputPath, string $outputDir, callable $progress): array
  {
    $progress(10, 'queued');
    $file = Storage::disk('public')->get($inputPath);
    $res = Http::timeout(120)->attach('image', $file, basename($inputPath))->post(config('room-ai.service_url') . '/v1/process');
    $res->throw();
    $data = $res->json();
    $progress(95, 'saving');
    $disk = Storage::disk('public');
    $disk->makeDirectory($outputDir);
    $result = [];
    foreach (['room_mask', 'detections', 'empty_room', 'furnished_room'] as $key) {
      if (isset($data[$key])) {
        $ext = $key === 'detections' ? 'json' : 'jpg';
        $path = "$outputDir/$key.$ext";
        $disk->put($path, base64_decode($data[$key]));
        $result[$key] = $path;
      }
    }
    $progress(100, 'completed');
    return $result;
  }
}
