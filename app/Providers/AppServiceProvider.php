<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider; use App\Services\Ai\AiPipelineInterface; use App\Services\Ai\MockAiPipeline; use App\Services\Ai\HttpAiPipeline;
class AppServiceProvider extends ServiceProvider { public function register(): void { $this->app->bind(AiPipelineInterface::class, fn()=>config('room-ai.driver')==='http'?new HttpAiPipeline:new MockAiPipeline); } public function boot(): void {} }
