<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'page_id',
        'full_url',
        'referrer',
        'ip',
        'ip_trunc',
        'ip_hash',
        'user_agent',
        'visited_at',
        'visit_date'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'visit_date' => 'date',
    ];

    public function page(): BelongsTo {
        return $this->belongsTo(Page::class);
    }

    public function visitor(): BelongsTo {
        return $this->belongsTo(Visitor::class);
    }
}
