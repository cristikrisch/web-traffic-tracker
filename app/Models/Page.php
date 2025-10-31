<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['canonical_url'];

    public function visits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}
