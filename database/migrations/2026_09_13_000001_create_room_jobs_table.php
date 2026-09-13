<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('room_jobs', function(Blueprint $t){$t->uuid('id')->primary();$t->string('status')->index();$t->unsignedTinyInteger('progress')->default(0);$t->string('original_path');$t->text('error_message')->nullable();$t->json('meta')->nullable();$t->timestamps();}); } public function down(): void { Schema::dropIfExists('room_jobs'); } };
