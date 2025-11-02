<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVisit extends Model
{
    protected $fillable = [
        'visitor_id','page_id','full_url','referrer',
        'utm_source','utm_medium','utm_campaign','utm_term','utm_content',
        'ip','ip_trunc', 'ip_hash', 'user_agent','visited_at','visit_date'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'visit_date' => 'date',
    ];

    public function page(): BelongsTo
    { return $this->belongsTo(Page::class); }
    public function visitor(): BelongsTo
    { return $this->belongsTo(Visitor::class); }
}
