<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'currency_code', 'wikiDataId'];

    public function cities()
    {
        return $this->hasMany(City::class , 'country_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class , 'country_id');
    }
}
