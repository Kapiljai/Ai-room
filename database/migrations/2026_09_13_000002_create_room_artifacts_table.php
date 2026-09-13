<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('room_artifacts', function(Blueprint $t){$t->id();$t->uuid('room_job_id');$t->string('type')->index();$t->string('path');$t->string('mime_type')->nullable();$t->json('meta')->nullable();$t->timestamps();$t->foreign('room_job_id')->references('id')->on('room_jobs')->cascadeOnDelete();}); } public function down(): void { Schema::dropIfExists('room_artifacts'); } };
