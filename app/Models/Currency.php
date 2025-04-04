<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'country_id',
        'symbol',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
