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

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function guard()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }
}
