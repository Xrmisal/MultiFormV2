<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'house_number',
        'street_name',
        'city',
        'postcode',
        'complete',
    ];

    public function getRouteKeyName(){
        return 'email';
    }
}
