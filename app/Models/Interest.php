<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Interest extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Interest $interest) {
            $interest->slug = $interest->slug ?: static::makeSlug($interest->name);
        });
    }

    /** Str::slug ينقل العربية حرفيًا، ويعيد فراغًا مع الرموز — فنضمن معرّفًا فريدًا دائمًا */
    public static function makeSlug(string $name): string
    {
        $slug = Str::slug($name);

        if ($slug === '' || static::where('slug', $slug)->exists()) {
            $slug = 'interest-'.Str::lower(Str::random(6));
        }

        return $slug;
    }

    /**
     * تحوّل معرّفات مخزّنة في leads.interests إلى أسماء معروضة.
     * لو حُذف مجال بعد تسجيل اهتمام عليه، يُعرض معرّفه بدل أن ينهار العرض.
     */
    public static function labelsFor(?array $slugs): \Illuminate\Support\Collection
    {
        $slugs = collect($slugs ?? [])->filter()->values();

        if ($slugs->isEmpty()) {
            return collect();
        }

        $names = static::whereIn('slug', $slugs)->pluck('name', 'slug');

        return $slugs->map(fn ($slug) => $names[$slug] ?? $slug);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /** عدد الاهتمامات المسجلة على هذا المجال — leads.interests يخزّن الـslug */
    public function leadsCount(): int
    {
        return Lead::whereJsonContains('interests', $this->slug)->count();
    }
}
