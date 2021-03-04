<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sponsorable extends Model
{
    use HasFactory;

    public static function findOrFailBySlug(string $slug)
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    public function slots()
    {
        return $this->hasMany(SponsorableSlot::class);
    }
}
