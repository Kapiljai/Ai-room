<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RoomArtifact extends Model {
    protected $fillable=['room_job_id','type','path','mime_type','meta'];
    protected $casts=['meta'=>'array'];
    public function job(): BelongsTo { 
        return $this->belongsTo(RoomJob::class,'room_job_id'); 
    }
}
