<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuardCheck extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'property_id',
        'guard_id',
        'result',
        'comment',
    ];

    public const RESULT_PAID = 'PAID';
    public const RESULT_UNPAID = 'UNPAID';
    public const RESULT_NON_BENEFICIARY = 'NON_BENEFICIARY';

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function guardUser()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
