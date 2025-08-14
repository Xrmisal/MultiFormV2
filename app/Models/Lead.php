<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lead extends Model
{

    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'house_number',
        'street_name',
        'city',
        'postcode',
        'proof_of_id',
        'proof_of_address',
        'complete',
        'failed',
        'converted'
    ];

}
