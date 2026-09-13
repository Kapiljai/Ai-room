<?php

namespace App\Services\Ai;

interface AiPipelineInterface
{
    public function run(string $inputPath, string $outputDir, callable $progress): array;
}
