<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorableSlot extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeSponsorable($query)
    {
        return $query->whereNull('sponsorship_id')->where('publish_date', '>', now());
    }
}
