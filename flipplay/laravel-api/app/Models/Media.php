<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Media extends Model
{
    protected $fillable = [
        'title',
        'release_year',
        'genres',
        'type',
        'episodes',
        'score',
    ];

    protected $casts = [
        'genres' => 'array',
        'release_year' => 'integer',
        'episodes' => 'integer',
        'score' => 'decimal:1',
    ];

    public function mediaCollection(): BelongsToMany
    {
        return $this->belongsToMany(Media::class)
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
