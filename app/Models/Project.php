<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name', 'slug', 'summary', 'description', 'location',
        'area', 'units', 'status', 'zone_id', 'sort', 'active',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            $project->slug = $project->slug ?: static::makeSlug($project->name);
        });
    }

    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        if ($slug === '' || static::where('slug', $slug)->exists()) {
            $slug = 'project-'.Str::lower(Str::random(6));
        }

        return $slug;
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
