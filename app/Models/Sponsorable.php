<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsorable extends Model
{
    use HasFactory;

    public static function findOrFailBySlug(string $slug)
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    public function slots(): HasMany
    {
        return $this->hasMany(SponsorableSlot::class);
    }
}
