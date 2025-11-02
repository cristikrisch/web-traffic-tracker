<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;
    use HasUlids;
    protected $fillable = ['visitor_key','user_agent_hash','first_seen_at','last_seen_at'];

    public function visits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}
