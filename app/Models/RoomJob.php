<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class RoomJob extends Model {
    protected $fillable=['id','status','progress','original_path','error_message','meta'];
    protected $casts=['meta'=>'array'];
    public $incrementing=false; protected $keyType='string';
    public function artifacts(): HasMany { 
        return $this->hasMany(RoomArtifact::class); 
    }
}
