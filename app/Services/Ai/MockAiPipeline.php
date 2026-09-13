<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Storage;

class MockAiPipeline implements AiPipelineInterface
{
  public function run(string $inputPath, string $outputDir, callable $progress): array
  {
    $progress(20, 'segmenting');
    usleep(150000);
    $progress(45, 'detecting');
    usleep(150000);
    $progress(70, 'inpainting');
    usleep(150000);
    $progress(90, 'generating');
    $disk = Storage::disk('public');
    $disk->makeDirectory($outputDir);
    $base = pathinfo($inputPath, PATHINFO_FILENAME);
    $original = Storage::disk('public')->path($inputPath);
    $art = [];
    $types = ['room_mask' => 'mask', 'detections' => 'json', 'empty_room' => 'empty', 'furnished_room' => 'furnished'];
    foreach ($types as $type => $suffix) {
      $ext = $suffix === 'json' ? 'json' : 'jpg';
      $path = "$outputDir/{$base}_{$suffix}.{$ext}";
      if ($ext === 'json') $disk->put($path, json_encode(['objects' => ['sofa', 'table', 'chair'], 'driver' => 'mock'], JSON_PRETTY_PRINT));
      else copy($original, $disk->path($path));
      $art[$type] = $path;
    }
    $progress(100, 'completed');
    return $art;
  }
}
