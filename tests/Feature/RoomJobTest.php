<?php
namespace Tests\Feature; use Tests\TestCase; use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Http\UploadedFile; use Illuminate\Support\Facades\Queue; use App\Jobs\ProcessRoomJob;
class RoomJobTest extends TestCase {use RefreshDatabase; public function test_can_create_room_job():void{Queue::fake();$r=$this->postJson('/api/v1/jobs',['image'=>UploadedFile::fake()->image('room.jpg',1200,800)]);$r->assertStatus(202)->assertJsonStructure(['job_id','status']);Queue::assertPushed(ProcessRoomJob::class);}}
